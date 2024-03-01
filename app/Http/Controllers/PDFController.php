<?php

namespace sisViaticos\Http\Controllers;

use Illuminate\Http\Request;
use sisViaticos\Folio;
use sisViaticos\DetalleFolio;
use sisViaticos\User;
use sisViaticos\UserProfile;
use Illuminate\Support\Facades\Redirect;
use sisViaticos\Http\Requests\FolioFormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Mail;

use DB;
use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;
use Barryvdh\DomPDF\Facade as PDF;

class PDFController extends Controller
{
    //

    public function index(Request $request)
    {
        if ($request)
        {
            $query=trim($request->get('searchText'));
            $folios=DB::table('ssm_viat_header_folio as f')
            ->join('ssm_viat_status as s','f.id_status','=','s.id_status')
            ->select('f.id_header_folio as folio', 'f.fecha','f.tipo','f.destino','f.anticipo', 's.status','s.descripcion')
            ->where('f.destino','LIKE','%'.$query.'%')
            ->where('f.id_solicitante','=', Auth::user()->id)
            ->whereIn('f.id_status',['8','16','15'])
            ->orderBy('id_header_folio','desc')
            ->paginate(7);

            //$newOption=DB::table('ssm_viat_header_folio')
            //->where('id_solicitante','=', Auth::user()->id)
            //->where('id_status','<','16')
            //->where('id_status','>','0')
            //->get();

            $usernom = Auth::user()->numeroNom;
            $branch = Auth::user()->company;
    
            $company = substr($branch,0,1);                
            $valuesUser = $company.$usernom;
    
            $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

            return view('travel.print.index',["userClaims"=>$userClaims,"folios"=>$folios,"searchText"=>$query]);
        }

    }

    public function view($id){

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
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'subtotal')
        ->where('id_header_folio','=',$id)
        ->get();

        //AGREGAR A LOS DETALLES EL IMPORTE EN PESOS, EL TIPO DE MONEDA, PARA PODER DIFERENCIAR ENTRE LOS GASTOS CREADOS 

        $detalle=DB::table('VIEW_SSM_DETALLE_FOLIO_INT')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'moneda','tipomoneda','subtotal', 'subtotalint')
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


}
