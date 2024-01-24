<?php

namespace sisViaticos\Http\Controllers;

use Illuminate\Http\Request;
use sisViaticos\Http\Requests;
use sisViaticos\DetalleFolio;
use sisViaticos\Folio;
use sisViaticos\Moneda;
use sisViaticos\Status;
use sisViaticos\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use DB;
use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;
use File;

class XmlController extends Controller
{
    //
    public function store(Request $request)
    {

        $folio=DB::table('ssm_viat_header_folio')
        ->where('id_header_folio','=',$request->get('id'))
        ->first();

        //try{
//            DB::beginTransaction();

            //$total = $request->get('total_venta');

            //$folioSuma=Folio::findOrFail($request->get('id'));
            //$folioSuma->id_status='8';
            //$folioSuma->all_total=$total;
            //$folioSuma->save();



            $detalle = new DetalleFolio;
            $detalle->id_header_folio=$folio->id_header_folio;
            //$detalle->fecha_factura=$request->get('fecha_factura');
            $detalle->id_proveedor='1';
            //$detalle->noFactura=$request->get('noFactura');
            $detalle->id_gasto=$request->get('idgasto');
            $detalle->id_cuenta='1';
            $detalle->metodoPago=$request->get('metodo');
            //$detalle->importe=$request->get('importe');
            //$detalle->IVA='0.00';
            //$detalle->otro_impuesto='0.00';
            //$detalle->IVA=$request->get('iva');
            //$detalle->otro_impuesto=$request->get('otrosImpuestos');

             
            $folder=public_path().'/imagenes/folios/'.$folio->id_header_folio;

            if (!File::exists($folder)) {
                # code...
                File::makeDirectory($folder, 0775, true);
            }

             if (input::hasfile('xml')) {
        	# code...
	        	$file=input::file('xml');
	        	$xml = simplexml_load_file($url);
	        	$detalle->noFactura=$xml->Folio;
        	}

            $detalle->save();

           // DB::commit();

//        }catch(\Exception $e)
 //       {
           // DB::rollback();
 //       }
         //dd($xml);
        //return redirect()->action('DetalleFolioController@show', [$folio->id_header_folio]);
        return Redirect::to('travel/gasto/');

    }

}
