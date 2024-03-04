<?php

namespace sisViaticos\Http\Controllers;

use Illuminate\Http\Request;
use sisViaticos\Http\Requests;
use sisViaticos\DetalleFolio;
use sisViaticos\Transfer;
use sisViaticos\Folio;
use sisViaticos\FirmaGasto;
use sisViaticos\Moneda;
use sisViaticos\Status;
use sisViaticos\User;
use sisViaticos\Repayment;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use DB;
use Carbon\Carbon;
use Mail;

class CuentasXPagarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request)
        {	
            $query=trim($request->get('searchText'));

            if (in_array(Auth::user()->id, [27, 2249, 1038, 2260, 2196, 2217, 2230, 2319])) {
                $companias = ['QRO', 'QRO,SLM', 'QRO,SLM,MTY'];
            } elseif (Auth::user()->id == 1195) {
                $companias = ['QRO', 'QRO,SLM', 'QRO,SLM,MTY', 'MTY'];                
            } elseif (in_array(Auth::user()->id, [7, 1190])) {
                $companias = ['QRO', 'SLM', 'QRO,SLM'];
            } elseif (Auth::user()->id == 6) {
                $companias = ['QRO'];
            } elseif (in_array(Auth::user()->id, [4, 5])) {
                $companias = ['MTY'];
            }

            $folios=DB::table('VIEW_SSM_FOLIOS_CCP')
            ->select('id_header_folio as folio','name','company','fecha','tipo','destino','id_status','anticipo','all_total', 'status','descripcion')
            ->whereIn('BranchRH', $companias)
	        ->where('name','LIKE','%'.$query.'%')
	        ->whereIn('id_status',['15', '19'])
	        ->orderBy('id_header_folio','desc')
	        ->get();	            

            $usernom = Auth::user()->numeroNom;
            $branch = Auth::user()->company;

            $company = substr($branch,0,1);                
            $valuesUser = $company.$usernom;

            $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));
           
            return view('accounting.comprobacion.index2',["folios"=>$folios,"searchText"=>$query,"userClaims"=>$userClaims]);
        }

	}

    public function report($id)
    {
        // Nueva Fucion a Traves de un stored Procedure

        $reporte=DB::select("EXEC SP_get_viat_reporte_gastos  ?", Array ($id));

        $folio=DB::table('ssm_viat_header_folio')
        ->select('id_header_folio')
        ->where('id_header_folio','=',$id)
        ->first();

        $cuentas=DB::table('VIEW_SSM_SAP_CUENTAS')
        ->select('AcctCode', 'FormatCode', 'NameCuenta')
        ->Get();      

        $suma =DB::table('ssm_viat_reporte_gastos')
        ->select (DB::raw('SUM(debe) as total'))
        ->where('id_header_folio','=',$id)
        ->first();

        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;

        $company = substr($branch,0,1);                
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));
        
        return view('accounting.comprobacion.reporte',["userClaims"=>$userClaims, "cuentas"=>$cuentas, "folio"=>$folio, "reporte"=>$reporte, "suma"=>$suma]);
    }

	public function show($id)
    {


        $folio=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','u.name','f.id_status','f.tipo','f.destino','f.evidencia_viaje','f.pdfevidencia','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.dias','criterio','m.moneda','f.anticipo','f.all_total','f._token')
        ->where('f.id_header_folio','=',$id)
        ->first();

        $folioi=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->leftJoin('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
        ->select('f.id_header_folio','f.fecha','u.name','t.montopesos','f.id_status','f.tipo','f.destino','f.evidencia_viaje','f.pdfevidencia','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.dias','criterio','m.moneda','f.anticipo','f.all_total','f._token')
        ->where('f.id_header_folio','=',$id)
        ->first();

        $detalles=DB::table('VIEW_SSM_DETALLE_FOLIO')
        ->select('id_detalle_folio', 'id_header_folio','id_gasto','nomGasto','metodoPago', 'subtotal', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'comentarios', 'xml', 'pdf', 'fecha_factura' )
        ->where('id_header_folio','=',$id)
        ->get();

        //AGREGAR A LOS DETALLES EL IMPORTE EN PESOS, EL TIPO DE MONEDA, PARA PODER DIFERENCIAR ENTRE LOS GASTOS CREADOS 
        $detallesint=DB::table('VIEW_SSM_DETALLE_FOLIO_INT')
        ->select('id_detalle_folio', 'id_header_folio','id_gasto','nomGasto','metodoPago', 'moneda','tipomoneda','subtotal', 'subtotalint', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'xml', 'pdf', 'comentarios', 'importeint', 'pdfint', 'fecha_factura', 'subtotalotro')
        ->where('id_header_folio','=',$id)
        ->get();

        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;

        $company = substr($branch,0,1);                
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

        
        return view('accounting.comprobacion.show',["userClaims"=>$userClaims, "folio"=>$folio, "folioi"=>$folioi, "detalles"=>$detalles, "detallesint"=>$detallesint]);

    }

	public function edit($id){

    	$folio=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','f.id_solicitante','f.id_status','u.name','u.numeroNom','u.company','u.id','tipo','f.destino','f.proposito','f.fecha_salida','f.fecha_llegada','f.dias','criterio','m.moneda','f.anticipo','f.all_total')
        ->where('f.id_header_folio','=',$id)
        ->first();

        $folioi=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->leftJoin('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
        ->select('f.id_header_folio','f.fecha','f.id_solicitante','t.montopesos','f.id_status','u.name','u.id', 'u.numeroNom', 'u.company','tipo','f.destino','f.proposito','f.fecha_salida','f.fecha_llegada','f.dias','criterio','m.moneda','f.anticipo','f.all_total')
        ->where('f.id_header_folio','=',$id)
        ->first();

        // $profile=DB::table('user_profile')->where('id_user','=',$folio->id_solicitante)->first();

        $profiles=DB::table('VIEW_SSM_INFO_USERS')->where('TrabajadorID','=',$folio->numeroNom)->where('compania','LIKE','%'.$folio->company.'%')->where('NombreCompleto','LIKE','%'.$folio->name.'%')->first();
        
        $detalles=DB::table('VIEW_SSM_DETALLE_FOLIO')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'subtotal', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'comentarios', 'xml', 'pdf' )
        ->where('id_header_folio','=',$id)
        ->get();

        //AGREGAR A LOS DETALLES EL IMPORTE EN PESOS, EL TIPO DE MONEDA, PARA PODER DIFERENCIAR ENTRE LOS GASTOS CREADOS 

        $detalle=DB::table('VIEW_SSM_DETALLE_FOLIO_INT')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'moneda','tipomoneda','subtotal', 'subtotalint', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'xml', 'pdf', 'comentarios', 'importeint', 'pdfint')
        ->where('id_header_folio','=',$id)
        ->get();
        

        //TIPO DE MONEDA

        $tipomoneda=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_moneda as m','d.tipomoneda','=','m.id_moneda')
        ->select('d.id_gasto', 'm.moneda')
        ->where('id_header_folio','=',$id)
        ->groupBy('d.id_gasto','m.moneda')
        ->get();

        $UserAnticipo=DB::table('ssm_viat_firma_anticipo as s')
        ->join('users as u','s.id_user','=','u.id')
        ->select('s.id_anticipo', 's.id_header_folio', 'u.id', 's.anticipo', 'u.name')
        ->where('s.id_header_folio','=', $id)
        ->whereNotNull('s.id_user')
        ->whereNotNull('s.anticipo')
        ->orderBy('s.anticipo','desc')
        ->take(1)
        ->get();

         $UserGasto=DB::table('ssm_viat_firma_gasto as s')
        ->join('users as u','s.id_user','=','u.id')
        ->select('s.id_gasto', 's.id_header_folio', 'u.id', 's.gasto', 'u.name')
        ->where('s.id_header_folio','=', $id)
        ->whereNotNull('s.id_user')
        ->whereNotNull('s.gasto')
        ->orderBy('s.gasto','desc')
        ->take(1)
        ->get();

        $Auto1Anticipo=DB::table('ssm_viat_firma_anticipo as s')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','s.id_autorizador','=','a.TrabajadorID')
        ->join('ssm_viat_header_folio as f', 'a.email', '=', 'f.correo_auto1')
        ->select('s.id_anticipo', 's.id_header_folio', 's.id_autorizador', 's.anticipo', 'a.email', 'a.NombreCompleto')
        ->where('s.id_header_folio','=', $id)
        ->whereNotNull('s.anticipo')
        ->whereNotNull('a.email')
        ->orderBy('s.anticipo','asc')
        ->take(1)
        ->get();

        $Auto1Gasto=DB::table('ssm_viat_firma_gasto as s')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','s.id_autorizador','=','a.TrabajadorID')
        ->join('ssm_viat_header_folio as f', 'a.email', '=', 'f.correo_auto1')
        ->select('s.id_gasto', 's.id_header_folio', 's.id_autorizador', 's.gasto', 'a.email','a.NombreCompleto')
        ->where('s.id_header_folio','=', $id)
        ->whereNotNull('s.gasto')
        ->whereNotNull('a.email')
        ->orderBy('s.gasto','asc')
        ->take(1)
        ->get();

        $Auto2Anticipo=DB::table('ssm_viat_firma_anticipo as s')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','s.id_autorizador','=','a.TrabajadorID')
        ->join('ssm_viat_header_folio as f', 'a.email', '=', 'f.correo_auto2')
        ->select('s.id_anticipo', 's.id_header_folio', 's.id_autorizador', 's.anticipo', 'a.email','a.NombreCompleto')
        ->where('s.id_header_folio','=', $id)
        ->whereNotNull('s.anticipo')
        ->orderBy('s.anticipo','desc')
        ->take(1)
        ->get();

        $Auto2Gasto=DB::table('ssm_viat_firma_gasto as s')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','s.id_autorizador','=','a.TrabajadorID')
        ->join('ssm_viat_header_folio as f', 'a.email', '=', 'f.correo_auto2')
        ->select('s.id_gasto', 's.id_header_folio', 's.id_autorizador', 's.gasto', 'a.email','a.NombreCompleto')
        ->where('s.id_header_folio','=', $id)
        ->whereNotNull('s.gasto')
        ->orderBy('s.gasto','desc')
        ->take(1)
        ->get();

        $Auto3Anticipo=DB::table('ssm_viat_firma_anticipo as s')
        ->join('ssm_viat_autorizadores as a','s.id_autorizador','=','a.id_autorizador')
        ->select('s.id_anticipo', 's.id_header_folio', 's.id_autorizador', 's.anticipo', 'a.autorizador')
        ->where('s.id_header_folio','=', $id)
        ->whereNotNull('s.anticipo')
        ->whereIn('s.id_autorizador',['5', '19'])
        ->orderBy('s.anticipo','desc')
        ->take(1)
        ->get();

        $Auto3Gasto=DB::table('ssm_viat_firma_gasto as s')
        ->join('ssm_viat_autorizadores as a','s.id_autorizador','=','a.id_autorizador')
        ->select('s.id_gasto', 's.id_header_folio', 's.id_autorizador', 's.gasto', 'a.autorizador')
        ->where('s.id_header_folio','=', $id)
        ->whereNotNull('s.gasto')
        ->whereIn('s.id_autorizador',['5', '19'])
        ->orderBy('s.gasto','desc')
        ->take(1)
        ->get();
        
        $view = view ('pdf.travel.folio',compact('folio','folioi','profiles','detalles','detalle', 'tipomoneda','Auto1Anticipo','Auto1Gasto','Auto2Anticipo','Auto2Gasto','Auto3Anticipo','Auto3Gasto','UserAnticipo','UserGasto'));
        
        
        $pdf = \App::make('dompdf.wrapper');
        $pdf->LoadHTML($view);
        return $pdf->stream('foliopdf-'.$folio->id_header_folio.'.pdf');

    	#return view("pdf/travel/folio",['folio'=>$folio]);

    }

    //Funcion para denegar la solicitud y regresarlo a usuario
    public function denegado($id, $token)
    {
    	$folio=Folio::where('id_header_folio','=',$id)
        ->where('_token','=',$token)
        ->First();
        $folio->id_status='21';
        $folio->save();

        $firmaUser=FirmaGasto::where('id_header_folio','=',$folio->id_header_folio)
        ->where('id_user','=',$folio->id_solicitante)
        ->first();
        $firmaUser->status='0';
        $firmaUser->update();

        //Traer informacion para enviar correo para denegar el folio 
        $this->replyRequestApprobNo($id, $token);
        return Redirect::to('accounting/comprobacion');
    }

    public function entregado($id, $token)
    { 	

        //Se manda el folio a los autorizadores
    	$folio=Folio::where('id_header_folio','=',$id)
        ->where('_token','=',$token)
        ->First();

        if ($folio->id_status == '15'){
            $folio->id_status='16';
            $folio->save();
            $this->sendMailRembolso($id, $token);
        }
        else{
            $folio->id_status='9';
            $folio->save();
        

            //Cambia el estatus de la firma del usuario del folio a 1, para poder generar el dato de la firma del autorizador
            $mytime = Carbon::now('America/Monterrey');


            $firmaUser=FirmaGasto::where('id_header_folio','=',$folio->id_header_folio)
            ->where('id_user','=',$folio->id_solicitante)
            ->first();
            $firmaUser->status='1';
            $firmaUser->gasto=$mytime;
            $firmaUser->update();

            
            //Se agrega la informacion para el autorizador

            $folioMai=DB::table('ssm_viat_header_folio as f')
            ->join('users as u','f.id_solicitante','=','u.id')
            ->join('VIEW_SSM_INFO_USERS as p','u.numeroNom','=','p.TrabajadorID')
            ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
            ->join('VIEW_SSM_GET_AUTHORIZERS as a','f.correo_auto1','=','a.email')
            ->select('f.id_header_folio','f.fecha','f.tipo','f.destino','f.proposito','f.evidencia_viaje','f.pdfevidencia','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.anticipo','f.all_total','m.moneda','u.name','f.correo_solicitante as emailU','u.company','a.TrabajadorID','a.email as emailA','f._token')
            ->where('f.id_header_folio','=',$id)
            ->where('p.compania','LIKE','%'.$folio->company.'%')
            ->where('_token','=',$token)
            ->first();

            $firma=DB::table('ssm_viat_firma_gasto')
            ->select(DB::raw('count( id_gasto ) as gasto'))
            ->where('id_header_folio','=',$id)
            ->where('id_autorizador','=',$folioMai->TrabajadorID)
            ->first();

            if ($firma->gasto == 0)
            {
                $folioMai=DB::table('ssm_viat_header_folio as f')
                ->join('users as u','f.id_solicitante','=','u.id')
                ->join('VIEW_SSM_INFO_USERS as p','u.numeroNom','=','p.TrabajadorID')
                ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
                ->join('VIEW_SSM_GET_AUTHORIZERS as a','f.correo_auto1','=','a.email')
                ->select('f.id_header_folio','f.fecha','f.tipo','f.destino','f.proposito','f.evidencia_viaje','f.pdfevidencia','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.anticipo','f.all_total','m.moneda','u.name','f.correo_solicitante as emailU','u.company','a.TrabajadorID','a.email as emailA','f._token')
                ->where('f.id_header_folio','=',$id)
                ->where('p.compania','LIKE','%'.$folio->company.'%')
                ->where('_token','=',$token)
                ->first();
        
                $firmaAuto = new FirmaGasto;
                $firmaAuto->company=$folioMai->company;
                $firmaAuto->id_autorizador=$folioMai->TrabajadorID;
                $firmaAuto->id_header_folio=$folioMai->id_header_folio;
                $firmaAuto->status='0';
                $firmaAuto->save();
            }
            else{

            }

            $this->sendMailAuto1($id, $token);
            return Redirect::to('accounting/comprobacion');
        }

    }

    public function replyRequestApprobNo($id, $token)
    {
        $folioMaill=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','t.montopesos','f.evidencia_viaje','f.pdfevidencia','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.name','u.company','f.correo_solicitante as emailU','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('f._token','=',$token)
        ->first();

        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','f.proposito','f.evidencia_viaje','f.pdfevidencia','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.name','u.company','f.correo_solicitante as emailU','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('f._token','=',$token)
        ->first();

        $detalles=DB::table('VIEW_SSM_DETALLE_FOLIO')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'Subtotal', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'comentarios', 'xml', 'pdf' )
        ->where('id_header_folio','=',$id)
        ->get();

        //AGREGAR A LOS DETALLES EL IMPORTE EN PESOS, EL TIPO DE MONEDA, PARA PODER DIFERENCIAR ENTRE LOS GASTOS CREADOS 

        $detalle=DB::table('VIEW_SSM_DETALLE_FOLIO_INT')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'moneda','tipomoneda','Subtotal', 'Subtotalint', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'xml', 'pdf', 'comentarios', 'importeint', 'pdfint')
        ->where('id_header_folio','=',$id)
        ->get();

        $tipomoneda=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_moneda as m','d.tipomoneda','=','m.id_moneda')
        ->select('d.id_gasto', 'm.moneda')
        ->where('id_header_folio','=',$id)
        ->groupBy('d.id_gasto','m.moneda')
        ->get();

        Mail::Send('mails.replyRequestCxPNo', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail){
            $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
            $mail->to($folioMail->emailU, $folioMail->name);
        });
    }

    public function sendMailAuto1($id ,$token)
    {
        $folio=Folio::where('_token','=',$token)
        ->where('id_header_folio','=', $id)
        ->first();

        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_INFO_USERS as p','u.numeroNom','=','p.TrabajadorID')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','f.correo_auto1','=','a.email')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.destino','f.proposito','f.evidencia_viaje','f.pdfevidencia','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.anticipo','f.all_total','m.moneda','u.name','f.correo_solicitante as emailU','a.TrabajadorID','a.email as emailA','f._token')
        ->where('f.id_header_folio','=',$id)
        ->where('_token','=',$token)
        ->where('p.compania','LIKE','%'.$folio->company.'%')
        ->first();

        $folioMaill=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_INFO_USERS as p','u.numeroNom','=','p.TrabajadorID')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','f.correo_auto1','=','a.email')
        ->join('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.destino','f.proposito','f.evidencia_viaje','f.pdfevidencia','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.anticipo','f.all_total','t.montopesos','m.moneda','u.name','f.correo_solicitante as emailU','a.TrabajadorID','a.email as emailA','f._token')
        ->where('f.id_header_folio','=',$id)
        ->where('_token','=',$token)
        ->where('p.compania','LIKE','%'.$folio->company.'%')
        ->first();

        $detalles=DB::table('VIEW_SSM_DETALLE_FOLIO')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'Subtotal', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'comentarios', 'xml', 'pdf' )
        ->where('id_header_folio','=',$id)
        ->get();

        //AGREGAR A LOS DETALLES EL IMPORTE EN PESOS, EL TIPO DE MONEDA, PARA PODER DIFERENCIAR ENTRE LOS GASTOS CREADOS 

        $detalle=DB::table('VIEW_SSM_DETALLE_FOLIO_INT')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'moneda','tipomoneda','Subtotal', 'Subtotalint', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'xml', 'pdf', 'comentarios', 'importeint', 'pdfint')
        ->where('id_header_folio','=',$id)
        ->get();

        $tipomoneda=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_moneda as m','d.tipomoneda','=','m.id_moneda')
        ->select('d.id_gasto', 'm.moneda')
        ->where('id_header_folio','=',$id)
        ->groupBy('d.id_gasto','m.moneda')
        ->get();

        $this->sendMailAprobCXP($id, $token);

       
        Mail::Send('mails.requestExpenseAuto1', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles,'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail){
            $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
            $mail->to($folioMail->emailA);
        });
    }

    public function sendMailAprobCXP($id, $token){
        $folioMaill=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','t.montopesos','f.evidencia_viaje','f.pdfevidencia','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.name','u.company','f.correo_solicitante as emailU','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('f._token','=',$token)
        ->first();

        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','f.proposito','f.evidencia_viaje','f.pdfevidencia','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.name','u.company','f.correo_solicitante as emailU','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('f._token','=',$token)
        ->first();

        $detalles=DB::table('VIEW_SSM_DETALLE_FOLIO')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'Subtotal', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'comentarios', 'xml', 'pdf' )
        ->where('id_header_folio','=',$id)
        ->get();

        //AGREGAR A LOS DETALLES EL IMPORTE EN PESOS, EL TIPO DE MONEDA, PARA PODER DIFERENCIAR ENTRE LOS GASTOS CREADOS 

        $detalle=DB::table('VIEW_SSM_DETALLE_FOLIO_INT')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'moneda','tipomoneda','Subtotal', 'Subtotalint', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'xml', 'pdf', 'comentarios', 'importeint', 'pdfint')
        ->where('id_header_folio','=',$id)
        ->get();

        $tipomoneda=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_moneda as m','d.tipomoneda','=','m.id_moneda')
        ->select('d.id_gasto', 'm.moneda')
        ->where('id_header_folio','=',$id)
        ->groupBy('d.id_gasto','m.moneda')
        ->get();

        Mail::Send('mails.replyRequestExpenseSiCxP', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail){
            $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
            $mail->to($folioMail->emailU, $folioMail->name);
        });

    }
    public function sendMailRembolso($id ,$token)
    {
        
        $folio=Folio::where('_token','=',$token)
        ->where('id_header_folio','=', $id)
        ->first();
        
        
        $folioPendiente=DB::table('ssm_viat_header_folio as f')
        ->join('ssm_viat_transfer as t','f.id_header_folio','=','t.id_header_folio')
        ->select('f.*','t.company as Com','t.id_transfer','t.id_header_folio as IDFOLIO','t.deposito')
        ->where('f.company','=',$folio->company)
        ->whereBetween('f.id_status',[2,15,19])
        ->where('f.id_solicitante','=',$folio->id_solicitante)
        ->where('f.id_header_folio','!=',$folio->id_header_folio)
        ->where('t.deposito','=','0')
        ->orderBy('fecha_salida','asc')
        ->limit(1)
        ->get();

        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_INFO_USERS as p','u.numeroNom','=','p.TrabajadorID')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','f.proposito','f.evidencia_viaje','f.pdfevidencia','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.id','u.name','u.company','f.correo_solicitante as emailU','p.BancoCuenta','p.CLABE','p.Banco','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('p.compania','LIKE','%'.$folio->company.'%')
        ->where('f._token','=',$token)
        ->first();

        $folioMaill=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_INFO_USERS as p','u.numeroNom','=','p.TrabajadorID')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','t.montopesos','f.destino','f.evidencia_viaje','f.pdfevidencia','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.id','u.name','u.company','f.correo_solicitante as emailU','p.BancoCuenta','p.CLABE','p.Banco','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('p.compania','LIKE','%'.$folio->company.'%')
        ->where('f._token','=',$token)
        ->first();

        $detalles=DB::table('VIEW_SSM_DETALLE_FOLIO')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'subtotal', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'comentarios', 'xml', 'pdf' )
        ->where('id_header_folio','=',$id)
        ->get();

        //AGREGAR A LOS DETALLES EL IMPORTE EN PESOS, EL TIPO DE MONEDA, PARA PODER DIFERENCIAR ENTRE LOS GASTOS CREADOS 

        $detalle=DB::table('VIEW_SSM_DETALLE_FOLIO_INT')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'moneda','tipomoneda','subtotal', 'Subtotalint', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'xml', 'pdf', 'comentarios', 'importeint', 'pdfint')
        ->where('id_header_folio','=',$id)
        ->get();

        $tipomoneda=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_moneda as m','d.tipomoneda','=','m.id_moneda')
        ->select('d.id_gasto', 'm.moneda')
        ->where('id_header_folio','=',$id)
        ->groupBy('d.id_gasto','m.moneda')
        ->get();

        if(count($folioPendiente)>0)
        {
            foreach ($folioPendiente as $folioPend) {
                 # code...
                $folioID = $folioPend->id_header_folio;
                $folioCompany = $folioPend->company;
                $folioToken = $folioPend->_token;

            }

            $trans=Transfer::where('id_header_folio','=',$folioID)->first();

            $folioUpdate=Folio::where('id_header_folio','=',$folioID)
            ->where('company','=',$folioCompany)
            ->where('_token','=',$folioToken)
            ->first();

            try{
                DB::beginTransaction();

                $trans->deposito='1';
                $trans->save();

                DB::commit();

            }catch(\Exception $e)
            {
               
            }   

            Mail::Send('mails.preTransferCXC', ['folioMail'=> $folioMail,'detalles'=>$detalles, 'detalle'=>$detalle,'folioUpdate'=>$folioUpdate], function($mail) use($folioMail){
                $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                $mail->to('gerardo.castro@summitmx.com', 'GERARDO CASTRO','coral.mederos@summitmx.com')
                // ->cc('gerardo.castro@summitmx.com')
                ->cc($folioMail->emailU);
                // $mail->to('gerardo.castro@summitmx.com', 'GERARDO CASTRO')
                // ->cc($folioMail->emailU);
            });

        }

        if($folio->tipo =='Nacional' ||$folio->id_moneda == 1)
        {
            if ($folio->anticipo < $folio->all_total) 
            {
                $rembolso = new Repayment;
                $rembolso->company=$folioMail->company;
                $rembolso->id_header_folio=$folioMail->id_header_folio;
                $rembolso->rembolso='0';
                $rembolso->save();

                Mail::Send('mails.repayment', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                    $mail->to('gerardo.castro@summitmx.com', 'GERARDO CASTRO','coral.mederos@summitmx.com')
                    // ->cc('gerardo.castro@summitmx.com')
                    ->cc($folioMail->emailU);
                    // $mail->to('gerardo.castro@summitmx.com', 'GERARDO CASTRO')
                    // ->cc($folioMail->emailU);
                    
                });
                
            }
            else
            {
                Mail::Send('mails.repaymentUser', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                    $mail->to($folioMail->emailU, $folioMail->name)
                    ->cc('gerardo.castro@summitmx.com');
                    // ->cc('gerardo.castro@summitmx.com');
                    
                    
                });
            }
           
        }
        else{
            if ($folioMaill->montopesos < $folioMaill->all_total) 
            {
                $rembolso = new Repayment;
                $rembolso->company=$folioMail->company;
                $rembolso->id_header_folio=$folioMail->id_header_folio;
                $rembolso->rembolso='0';
                $rembolso->save();

                Mail::Send('mails.repayment', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                    $mail->to('gerardo.castro@summitmx.com', 'GERARDO CASTRO','coral.mederos@summitmx.com')
                    // ->cc('gerardo.castro@summitmx.com')
                    ->cc($folioMail->emailU);
                    // $mail->to('gerardo.castro@summitmx.com', 'GERARDO CASTRO')
                    // ->cc($folioMail->emailU);
                    
                });
                
            }
            else
            {
                Mail::Send('mails.repaymentUser', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                    $mail->to($folioMail->emailU, $folioMail->name)
                    ->cc('gerardo.castro@summitmx.com');
                    // ->cc('gerardo.castro@summitmx.com');
                    
                    
                });
            }
            
        }
    }
   

}
