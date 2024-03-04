<?php

namespace sisViaticos\Http\Controllers;

use Illuminate\Http\Request;
use sisViaticos\Http\Requests;
use sisViaticos\Repayment;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

use Mail;

use DB;
use Carbon\Carbon;
use Response;

class RepaymentController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request)
        {
            $query=trim($request->get('searchText'));
            
            $folios=DB::table('ssm_viat_repayment as r')
            ->join('ssm_viat_header_folio as f','r.id_header_folio','=','f.id_header_folio')
            ->join('users as u','f.id_solicitante','=','u.id')
            ->join('ssm_viat_status as s','f.id_status','=','s.id_status')
            ->select('f.id_header_folio as folio','u.name','u.company','f.fecha','f.tipo','f.destino','f.anticipo','f.all_total', 's.status','s.descripcion')
            ->where('r.rembolso','=','0')
            ->where('u.name','LIKE','%'.$query.'%')
            ->orderBy('r.id_header_folio','desc')
            ->paginate(7);


            //$newOption=DB::table('ssm_viat_header_folio')
            //->where('id_solicitante','=', Auth::user()->id)
            //->where('id_status','<','16')
            //->where('id_status','>','0')
            //->get();

            //dd($folios);

            $usernom = Auth::user()->numeroNom;
            $branch = Auth::user()->company;
    
            $company = substr($branch,0,1);                
            $valuesUser = $company.$usernom;
    
            $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

            return view('treasury.rembolso.index',["userClaims"=>$userClaims,"folios"=>$folios,"searchText"=>$query]);
        }

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
        ->select('id_detalle_folio', 'id_header_folio','id_gasto','nomGasto','metodoPago', 'subtotal', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'comentarios', 'xml', 'pdf', 'fecha_factura' )
        ->where('id_header_folio','=',$id)
        ->get();

        //AGREGAR A LOS DETALLES EL IMPORTE EN PESOS, EL TIPO DE MONEDA, PARA PODER DIFERENCIAR ENTRE LOS GASTOS CREADOS 

        $detalle=DB::table('VIEW_SSM_DETALLE_FOLIO_INT')
        ->select('id_detalle_folio', 'id_header_folio','id_gasto','nomGasto','metodoPago', 'moneda','tipomoneda','subtotal', 'Subtotalint', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'xml', 'pdf', 'comentarios', 'importeint', 'pdfint', 'fecha_factura', 'subtotalotro')
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

    public function destroy($id)
    {
        $mytime = Carbon::now('America/Monterrey');

        $rembolso=Repayment::where('id_header_folio','=',$id)->first();
        $rembolso->fecha=$mytime;
        $rembolso->rembolso='1';
        $rembolso->save();

        $data2=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_INFO_USERS as p','u.numeroNom','=','p.TrabajadorID')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','u.name','u.company','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.all_total','m.moneda','f.anticipo','f.correo_solicitante as emailU','f._token','p.BancoCuenta','p.CLABE','p.Banco')
        ->where('f.id_header_folio','=',$id)
        ->where('p.compania','LIKE','%'.$rembolso->company.'%')
        ->first();

        $data22=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_INFO_USERS as p','u.numeroNom','=','p.TrabajadorID')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
        ->select('f.id_header_folio','f.fecha','f.tipo','t.montopesos','f.id_status','u.name','u.company','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.all_total','m.moneda','f.anticipo','f.correo_solicitante as emailU','f._token','p.BancoCuenta','p.CLABE','p.Banco')
        ->where('f.id_header_folio','=',$id)
        ->where('p.compania','LIKE','%'.$rembolso->company.'%')
        ->first();

        $detalles=DB::table('VIEW_SSM_DETALLE_FOLIO')
        ->select('id_detalle_folio', 'id_header_folio','id_gasto','nomGasto','metodoPago', 'Subtotal', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'comentarios', 'xml', 'pdf', 'fecha_factura' )
        ->where('id_header_folio','=',$id)
        ->get();

        //AGREGAR A LOS DETALLES EL IMPORTE EN PESOS, EL TIPO DE MONEDA, PARA PODER DIFERENCIAR ENTRE LOS GASTOS CREADOS 

        $detalle=DB::table('VIEW_SSM_DETALLE_FOLIO_INT')
        ->select('id_detalle_folio', 'id_header_folio','id_gasto','nomGasto','metodoPago', 'moneda','tipomoneda','Subtotal', 'Subtotalint', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'xml', 'pdf', 'comentarios', 'importeint', 'pdfint', 'fecha_factura', 'subtotalotro')
        ->where('id_header_folio','=',$id)
        ->get();


        Mail::Send('mails.transferRepayment', ['data2'=> $data2, 'data22'=> $data22,'detalles'=>$detalles], function($mail) use($data2){
            $mail->subject('[TEST] SOLICITUD Y REPORTE DE VIAJE: '.$data2->name.', Folio: '.$data2->id_header_folio);
            $mail->to($data2->emailU, $data2->name)
            ->cc('gerardo.castro@yopmail.com');
        });

        return Redirect::to('treasury/rembolso');
    }
}
