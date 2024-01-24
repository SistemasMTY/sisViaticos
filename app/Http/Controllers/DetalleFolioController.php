<?php

namespace sisViaticos\Http\Controllers;

use Illuminate\Http\Request;

use sisViaticos\Http\Requests;
use sisViaticos\DetalleFolio;
use sisViaticos\Folio;
use sisViaticos\Moneda;
use sisViaticos\Tipocambio;
use sisViaticos\Status;
use sisViaticos\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

use Session;
use DB;
use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;
use File;

class DetalleFolioController extends Controller
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
            $folios=DB::table('ssm_viat_header_folio as f')
            ->join('ssm_viat_status as s','f.id_status','=','s.id_status')
            ->join('ssm_viat_firma_gasto as gas', function ($join) {
                $join->on('f.id_header_folio', '=', 'gas.id_header_folio')
                ->whereIn('gas.status',['0','2']);
            })
            ->leftjoin('VIEW_SSM_GET_AUTHORIZERS as auto', 'gas.id_autorizador','=','auto.TrabajadorID')
            ->select('f.id_header_folio as folio', 'f.fecha','f.tipo','f.destino','f.anticipo','f.fecha_salida','f.fecha_llegada','f.all_total','s.status','s.descripcion','f.fecha_llegada','gas.id_autorizador','auto.NombreAuto as autorizador')
            ->where('f.destino','LIKE','%'.$query.'%')
            ->where('f.id_solicitante','=', Auth::user()->id)
            ->where('f.company','=',Auth::user()->company)
            ->whereIn('f.id_status',['8','9','10','11','12','13','14','18', '19', '21'])
            ->orderBy('f.fecha_salida','asc')
            // ->inRandomOrder('f.id_header_folio')
            ->paginate(15);
    
            $usernom = Auth::user()->numeroNom;
            $branch = Auth::user()->company;

            $company = substr($branch,0,1);                
            $valuesUser = $company.$usernom;

            $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));
            
            return view('travel.gasto.index',["userClaims"=>$userClaims,"folios"=>$folios,"searchText"=>$query]);
        }
	}

    public function show($id)
    {
        $folio=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','u.name','tipo','f.destino','f.proposito','f.fecha_salida','f.eq_computo','f.fecha_llegada','f.dias','criterio','m.moneda','f.anticipo','f.evidencia_viaje','pdfevidencia','f.all_total')
        ->where('f.id_header_folio','=',$id)
        ->where('f.id_solicitante','=', Auth::user()->id)
        ->where('f.company','=',Auth::user()->company)
        ->first();

        $folioi=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->leftJoin('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
        ->select('f.id_header_folio','f.fecha','u.name','tipo','f.destino','f.proposito','f.fecha_salida','f.eq_computo','f.fecha_llegada','f.dias','criterio','m.moneda','f.anticipo', 't.montopesos','f.all_total','f.evidencia_viaje','pdfevidencia','f._token')
        ->where('f.id_header_folio','=',$id)
        ->where('f.id_solicitante','=', Auth::user()->id)
        ->where('f.company','=',Auth::user()->company)
        ->first();

        $mytime = Carbon::now('America/Monterrey');
        
        $detalles=DB::table('VIEW_SSM_DETALLE_FOLIO')
        ->select('id_detalle_folio', 'id_header_folio','id_gasto','nomGasto','metodoPago', 'subtotal', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'comentarios', 'xml', 'pdf', 'fecha_factura' )
        ->where('id_header_folio','=',$id)
        ->get();

        //AGREGAR A LOS DETALLES EL IMPORTE EN PESOS, EL TIPO DE MONEDA, PARA PODER DIFERENCIAR ENTRE LOS GASTOS CREADOS 
        $detallesint=DB::table('VIEW_SSM_DETALLE_FOLIO_INT')
        ->select('id_detalle_folio', 'id_header_folio','id_gasto','nomGasto','metodoPago', 'moneda','tipomoneda','subtotal', 'Subtotalint', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'xml', 'pdf', 'comentarios', 'importeint', 'pdfint', 'fecha_factura', 'subtotalotro')
        ->where('id_header_folio','=',$id)
        ->get();
        
        $monedas=DB::table('ssm_viat_moneda')->get();
        $metodoP=DB::table('ssm_viat_metodo_pago')->get();
        $gastos=DB::table('ssm_viat_gastos')
        ->where('id_gasto', '<', 9)
        ->get();  
        
        // Obtener a que pertenece el folio de Gastos  Opcion
        $opciones=DB::table('VIEW_SSM_OPCIONES_FOLIO')
        ->where('id_header_folio','=',$id)
        ->first();

        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;

        $company = substr($branch,0,1);                
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));
        
        return view("travel.gasto.edit",["userClaims"=>$userClaims, "folio"=>$folio, "folioi"=>$folioi, "detalles"=>$detalles, "detallesint"=>$detallesint, "gastos"=>$gastos, "monedas"=>$monedas, "metodoP"=>$metodoP, "opciones"=>$opciones]);
    }

     public function store(Request $request)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);
        ini_set('post_max_size', '84M');
        ini_set('upload_max_filesize', '84M');
        
        $folio=DB::table('ssm_viat_header_folio')
        ->where('id_header_folio','=',$request->get('id'))
        ->where('company','=',Auth::user()->company)
        ->first();
        
        //Revisa si el check de solo guardar evidencia esta activado
        if ( $request->has('checksolo')) {
            $folioEv=Folio::where('id_header_folio','=',$request->get('id'))
            ->where('company','=',Auth::user()->company)
            ->first();
            try{
                DB::beginTransaction();
                $folder=public_path().'/imagenes/folios/'.$folio->id_header_folio;
                $folioEv->evidencia_viaje=$request->get('comentarioevidencia');
                
                if ($request->hasFile('pdfevidencia')) {
                    $filePDF4=$request->file('pdfevidencia'); 
                    $filePDF4->move($folder,$filePDF4->getClientOriginalName());
                    $folioEv->pdfevidencia=$filePDF4->getClientOriginalName();
                }
                $folioEv->save();

                DB::commit();

                }catch(\Exception $e)
                {
                    DB::rollback();
                }   
        }
        else{
            $folio=DB::table('ssm_viat_header_folio')
            ->where('id_header_folio','=',$request->get('id'))
            ->where('company','=',Auth::user()->company)
            ->first();
            try{
                // Realizar el proceso del link enviado por Alonso
                DB::beginTransaction();

                $folder=public_path().'/imagenes/folios/'.$folio->id_header_folio;

                if ($request->hasFile('xml')) {
                    libxml_use_internal_errors(true);
                    $fileXML=$request->file('xml'); 
                    $docXML = new \DOMDocument(); 
                    $docXML->load($fileXML);

                    $Comprobantes = $docXML->getElementsByTagName('Comprobante');
                    if($Comprobantes[0]->getAttribute("xmlns:cfdi") === 'http://www.sat.gob.mx/cfd/4'){
                        $Emisores = $docXML->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/4','Emisor');
                        $Impuestos = $docXML->getElementsByTagName("Impuestos");
                        $ImpuestosLocales = $docXML->getElementsByTagName("ImpuestosLocales");
                        $ImpuestosRetencion = $docXML->getElementsByTagName("RetencionesLocales");
                    $ImpuestosRetenciones = $docXML->getElementsByTagName("Retencion");
                        $Receptor = $docXML->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/4','Receptor');
                        $Complemento = $docXML->getElementsByTagName("TimbreFiscalDigital");
                    } 
                    else{
                        $Emisores = $docXML->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/3','Emisor');
                        $Impuestos = $docXML->getElementsByTagName("Impuestos");
                        $ImpuestosLocales = $docXML->getElementsByTagName("ImpuestosLocales");
                        $ImpuestosRetencion = $docXML->getElementsByTagName("RetencionesLocales");
                    $ImpuestosRetenciones = $docXML->getElementsByTagName("Retencion");
                        $Receptor = $docXML->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/3','Receptor');
                        $Complemento = $docXML->getElementsByTagName("TimbreFiscalDigital");
                    }

                    foreach ($Receptor as $ssm) {        
                        $rfcSSM = $ssm->getAttribute("Rfc");
                    }

                    foreach ($Comprobantes as $comprobante) {
                        $FolioComprobante=$comprobante->getAttribute("Folio"); 
                        $FechaComprobante = $comprobante->getAttribute("Fecha");
                        $SubTotalComprobante = $comprobante->getAttribute("SubTotal");
                        $MetodoPago = $comprobante->getAttribute("Moneda");
                        $TipoCambio = $comprobante->getAttribute("TipoCambio");
                    }

                    //DEFINIR EL MONTO EN PESOS DEL TIPO DE CAMBIO 
                    if($MetodoPago == 'MXN'){
                        $MetodoPago2 = 1;
                        $SubTotalComprobante2 = $SubTotalComprobante;
                    }
                    else
                    if($MetodoPago =='USD'){
                        $MetodoPago2 = 2;
                        $SubTotalComprobante2 = $SubTotalComprobante * $TipoCambio;
                    }
                    else{
                        $MetodoPago2 = 3;
                        $SubTotalComprobante2 = $SubTotalComprobante * $TipoCambio;
                    }
                
                    if($FolioComprobante=="")
                    {   
                        $CFDIR = $docXML->getElementsByTagName('CFDIRegistroFiscal');
                        foreach ($CFDIR as $cfdi) {
                            $FolioComprobante = $cfdi->getAttribute("Folio") ;
                        }
                    }

                    if ($FechaComprobante < $folio->fecha_salida) {
                        Session()->flash('msg','La factura no corresponde al periodo de viaje.');
                        return redirect()->action('DetalleFolioController@show', [$folio->id_header_folio]);
                    }

                    foreach ($Emisores as $emisor) {
                        $nombre = $emisor->getNodePath(2);         
                        $rfcEmisor = $emisor->getAttribute("Rfc");
                        $nombreEmisor = $emisor->getAttribute("Nombre");
                    }
                    foreach ($Complemento as $complemento) {
                        $UUID1=$complemento->getAttribute("UUID"); 
                    }

                    if (count($Impuestos)>0) {
                        foreach ($Impuestos as $impuesto) {
                            $IvaImpuesto = $impuesto->getAttribute("TotalImpuestosTrasladados");
                        }

                        //DEFINIR EL IVA EN PESOS, DEPENDIENDO DEL TIPO DE MONEDA
                        if($MetodoPago == 'MXN'){
                            $IvaImpuesto2 = $IvaImpuesto;
                        }
                        else{
                            $IvaImpuesto2 = $IvaImpuesto * $TipoCambio;
                        }

                        if ($IvaImpuesto=="") {
                            $IvaImpuesto='0.00';
                            $IvaImpuesto2 ='0.00';
                        }
                    }
                    else{
                        $IvaImpuesto='0.00';
                        $IvaImpuesto2 ='0.00';
                    }

                    if (count($ImpuestosLocales)>0) {
                        foreach ($ImpuestosLocales as $implocal) {
                            $IshImpLocal = $implocal->getAttribute("TotaldeTraslados");
                        }
                    }else
                    {
                        $IshImpLocal= '0.00';
                    }

                    if (count($ImpuestosRetenciones)>0) {
                        foreach ($ImpuestosRetenciones as $ImpuestosRetencione) {                        
                            $TasaRetencion = $ImpuestosRetencione->getAttribute("Impuesto");                        
                            $Retencion = $ImpuestosRetencione->getAttribute("Importe");
                        }
                    }else{
                        $Retencion= '0.00';
                        $TasaRetencion = '';
                    }
    
                    if (count($ImpuestosRetencion)>0) {
                        foreach ($ImpuestosRetencion as $ImpuestoRetencion) {
                            if($ImpuestoRetencion->getAttribute("TasadeRetencion")){
                                $TasaRetencion = $ImpuestoRetencion->getAttribute("TasadeRetencion");
                            }
                            else{
                                $TasaRetencion = $ImpuestoRetencion->getAttribute("Impuesto");
                            }
                            
                            $Retencion = $ImpuestoRetencion->getAttribute("Importe");
                        }
                    }else{
                        $Retencion= '0.00';
                        $TasaRetencion = '';
                    }

                    // Realiza la validacion del archivo mediante el portal enviado por ALONSO                    
                    $client = new \GuzzleHttp\Client();
                    $response = Http::get("http://www.summitmx.com/proveedores/control/funcion_cl_v.php?xml=%5C%5Cserlam6app%5Cc$%5Cinetpub%5Cwwwroot%5Cproveedores%5CdocsProveedores%5C". $rfcEmisor ."%5C[".$UUID1."] ". $fileXML->getClientOriginalName() ."");
                    $jsonData = $response->json();
                        
                    // Realiza la condicional para revisar que informacion fue la que se obtuvo de respuesta
                    if($jsonData){
                        if($jsonData['status'] == 'true'){
                            $respuesta = $jsonData['data'] . ' TT: ' . $jsonData['tipo'];
                            return response()->json(array('jsonData'=>$respuesta));
                        }
                        else{
                            if($jsonData['data'] = 'El XML ya ha sido cargado anteriormente'){
                                //GUARDA LOS DATOS CON XML QUE SEAN NACIONALES
                                if($folio->tipo=='Nacional'){
                                    $fileXML->move($folder,$fileXML->getClientOriginalName());
                                    $filePDF=$request->file('xpdf'); 
                                    $filePDF->move($folder,$filePDF->getClientOriginalName());

                                    $detalle = new DetalleFolio;
                                    $detalle->company=Auth::user()->company;
                                    $detalle->id_header_folio=$folio->id_header_folio;
                                    $detalle->fecha_factura=$FechaComprobante;
                                    $detalle->proveedor=$nombreEmisor;
                                    $detalle->RFC=$rfcEmisor;
                                    $detalle->noFactura=$FolioComprobante;
                                    $detalle->id_gasto=$request->get('xidgasto');
                                    $detalle->id_cuenta='1';
                                    $detalle->metodoPago=$request->get('xmetodo');
                                    $detalle->importe=$SubTotalComprobante;
                                    $detalle->IVA=$IvaImpuesto;
                                    $detalle->otro_impuesto=$IshImpLocal;
                                    $detalle->xml=$fileXML->getClientOriginalName();
                                    $detalle->pdf=$filePDF->getClientOriginalName();
                                    $detalle->comentarios=$request->get('xcomentarios');
                                    $detalle->UUID=$UUID1;
                                    $detalle->save();
                                }
                                //GUARDA LOS DATOS CON XML QUE SEAN INTERNACIONALES
                                else{
                                    $filePDF=$request->file('xpdf'); 
                                    $filePDF->move($folder,$filePDF->getClientOriginalName());

                                    $detalle = new DetalleFolio;
                                    $detalle->company=Auth::user()->company;
                                    $detalle->id_header_folio=$folio->id_header_folio;
                                    $detalle->fecha_factura=$FechaComprobante;
                                    $detalle->proveedor=$nombreEmisor;
                                    $detalle->RFC=$rfcEmisor;
                                    $detalle->noFactura=$FolioComprobante;
                                    $detalle->id_gasto=$request->get('xidgasto');
                                    $detalle->id_cuenta='1';
                                    $detalle->metodoPago=$request->get('xmetodo');
                                    $detalle->importe=$SubTotalComprobante;
                                    $detalle->importeint=$SubTotalComprobante2;
                                    $detalle->tipomoneda=$MetodoPago2;
                                    $detalle->IVA=$IvaImpuesto;
                                    $detalle->otro_impuesto=$IshImpLocal;
                                    $detalle->xml=$fileXML->getClientOriginalName();
                                    $detalle->pdf=$filePDF->getClientOriginalName();
                                    $detalle->comentarios=$request->get('xcomentarios');
                                    $detalle->UUID=$UUID1;
                                    $detalle->IVAint=$IvaImpuesto2;

                                    $detalle->save();
                                }
                            }
                            else{
                                Session()->flash('msg',$jsonData);
                            }
                            // En caso de que el metodo de pago sea AMEX, se guarda los detalles de los items
                            if($request->get('xmetodo') == 'AMEX'){
                                // Se realiza el stored procedure para subir los detalles
                                $addDetallesAmex = DB::UPDATE('EXEC Sp_Add_Items_AMEX ?,?,?,?', Array($UUID1, $folio->id_header_folio, $TasaRetencion, $Retencion));
                            }
                        }    
                    }
                    else{
                        Session()->flash('msg','Ocurrio un error al cargar la factura');                                
                        return redirect()->action('DetalleFolioController@show', [$folio->id_header_folio]);
                    }
                }
                else{
                    // Obtener a que pertenece el folio de Gastos  Opcion
                    $opciones=DB::table('VIEW_SSM_OPCIONES_FOLIO')
                    ->where('id_header_folio','=',$request->get('id'))
                    ->first();

                    // Obtener el total de los gastos de viatico del dia 
                    $sumViatico = DB::table('VIEW_SSM_SOLOVIATICO')
                    ->where('id_header_folio','=',$request->get('id'))
                    ->first();

                    if(!$sumViatico){
                        $sumViaticos = 0;
                    }
                    else{
                        $sumViaticos = $sumViatico->impviatico;
                    }

                    if($request->get('gasto') == 1){
                        if($opciones->Opcion == 1){
                            if($request->get('importe')> 0){
                                Session()->flash('msg','Debido a que es un viaje a una de las Plantas no se agrega el gasto del viatico');                                
                                return redirect()->action('DetalleFolioController@show', [$folio->id_header_folio]);
                            }
                        }
                        elseif($opciones->Opcion == 2){
                            if(($sumViaticos + $request->get('importe'))> 200){
                                Session()->flash('msg','Debido a que es un viaje de un dia, no se permite mas de 200 pesos en el viatico');
                                return redirect()->action('DetalleFolioController@show', [$folio->id_header_folio]);
                            }
                        }
                        elseif($opciones->Opcion == 3){
                            if($request->get('importe')> 250){
                                Session()->flash('msg','Debido a que es un viaje de un dos dias dentro de alguna de las plantas, no se permite mas de 250 pesos diarios en el viatico');
                                return redirect()->action('DetalleFolioController@show', [$folio->id_header_folio]);
                            }
                        }
                        elseif($opciones->Opcion == 4){
                            if($request->get('importe')> 450){
                                Session()->flash('msg','Debido a que es un viaje de un dos dias, no se permite mas de 450 pesos diarios en el viatico');
                                return redirect()->action('DetalleFolioController@show', [$folio->id_header_folio]);
                            }
                        }
                        elseif($opciones->Opcion == 5){
                            if($request->get('importe')> 250){
                                Session()->flash('msg','Debido a que es un viaje de mas de 2 dias hacia alguna de las plantas, no se permite mas de 250 pesos diarios en el viatico');
                                return redirect()->action('DetalleFolioController@show', [$folio->id_header_folio]);
                            }
                        }
                        elseif($opciones->Opcion == 6){
                            if($request->get('importe')> 450){
                                Session()->flash('msg','Debido a que es un viaje de mas de 2 dias, no se permite mas de 450 pesos diarios en el viatico');
                                return redirect()->action('DetalleFolioController@show', [$folio->id_header_folio]);
                            }
                        }
                    }
                    //GUARDA LOS DATOS SIN XML QUE SEAN NACIONALES
                    if($folio->tipo=='Nacional'){
                        $detalle = new DetalleFolio;
                        $detalle->company=Auth::user()->company;
                        $detalle->id_header_folio=$folio->id_header_folio;
                        $detalle->fecha_factura=$request->get('fecha_factura');
                        $detalle->proveedor='NO DEDUCIBLE';
                        $detalle->RFC='XEX010101000';
                        $detalle->noFactura=$request->get('noFactura');
                        $detalle->id_gasto=$request->get('gasto');
                        $detalle->id_cuenta='1';
                        $detalle->metodoPago=$request->get('metodo');
                        $detalle->importe=$request->get('importe');
                        $detalle->IVA='0.00';
                        $detalle->otro_impuesto='0.00';
                        if ($request->hasFile('pdf')) {
                            $filePDF=$request->file('pdf'); 
                            $filePDF->move($folder,$filePDF->getClientOriginalName());
                            $detalle->pdf=$filePDF->getClientOriginalName();
                        }
                        $detalle->comentarios=$request->get('comentarios');
                        $detalle->UUID='';
                        $detalle->save();
                    }
                    //GUARDA LOS DATOS SIN XML QUE SEAN INTERNACIONALES
                    else{
                        $moneda = $request->get('id_moneda');
                        $detalle = new DetalleFolio;
                        $detalle->company=Auth::user()->company;
                        $detalle->id_header_folio=$folio->id_header_folio;
                        $detalle->fecha_factura=$request->get('fecha_factura');
                        $detalle->proveedor='NO DEDUCIBLE';
                        $detalle->RFC='XEX010101000';
                        $detalle->noFactura=$request->get('noFactura');
                        $detalle->id_gasto=$request->get('gasto');
                        $detalle->id_cuenta='1';
                        $detalle->metodoPago=$request->get('metodo');
                        $detalle->importe=$request->get('importe');
                        $detalle->tipomoneda=$request->get('id_moneda');
                        if($moneda == 1){
                            $detalle->importeint=$request->get('importe');
                        }
                        else{
                            $detalle->importeint=$request->get('importemxn');
                        }
                        $detalle->IVA='0.00';
                        $detalle->otro_impuesto='0.00';
                        
                        if ($request->hasFile('pdf')) {
                            $filePDF=$request->file('pdf'); 
                            $filePDF->move($folder,$filePDF->getClientOriginalName());
                            $detalle->pdf=$filePDF->getClientOriginalName();
                        }
                        if ($request->hasFile('pdfimporte')) {
                            $filePDF2=$request->file('pdfimporte'); 
                            $filePDF2->move($folder,$filePDF2->getClientOriginalName());
                            $detalle->pdfint=$filePDF2->getClientOriginalName();
                        }
                        $detalle->comentarios=$request->get('comentarios');
                        $detalle->UUID='';
                        $detalle->IVAint='0.00';
                        $detalle->save();
                    }
                }
                //MODIFICACION EN LOS FOLIOS (HEADER FOLIO) EN LA SUMA DE LOS COSTOS 
                if($folio->tipo =='Nacional'){
                    $detalleUpdate=DB::table('ssm_viat_detalle_folio')
                    ->select('id_header_folio', DB::raw('sum(importe) as subtotal'), DB::raw('sum(IVA) as iva'),DB::raw('sum(otro_impuesto) as impuesto') ,DB::raw('sum(importe + IVA + otro_impuesto ) as total'))
                    ->where('id_header_folio','=',$detalle->id_header_folio)
                    ->where('metodoPago','=','Efectivo')
                    ->where('company','=',Auth::user()->company)
                    ->groupBy('id_header_folio')
                    ->first();
                }
                else{
                    $detalleUpdate=DB::table('ssm_viat_detalle_folio')
                    ->select('id_header_folio', DB::raw('sum(importeint) as subtotal'), DB::raw('sum(IVAint) as iva'),DB::raw('sum(otro_impuesto) as impuesto') ,DB::raw('sum(importeint + IVAint + otro_impuesto ) as total'))
                    ->where('id_header_folio','=',$detalle->id_header_folio)
                    ->where('metodoPago','=','Efectivo')
                    ->where('company','=',Auth::user()->company)
                    ->groupBy('id_header_folio')
                    ->first();
                }
                       
                if(!empty($detalleUpdate->subtotal)){
                    $folioSum=Folio::where('id_header_folio','=',$detalle->id_header_folio)
                    ->where('company','=',Auth::user()->company)
                    ->first();
                    $folioSum->all_total=$detalleUpdate->total;
                    $folioSum->all_subtotal=$detalleUpdate->subtotal;
                    $folioSum->all_iva=$detalleUpdate->iva;
                    $folioSum->all_otros_imp=$detalleUpdate->impuesto;
                    if($folioSum->id_status != '18'){
                        $folioSum->id_status='8';
                    }
                    $folioSum->save();
                }

                $folioEv=Folio::where('id_header_folio','=',$detalle->id_header_folio)
                ->where('company','=',Auth::user()->company)
                ->first();
                $evidenciaviaje= $request->get('comentarioevidencia');
                if ( $evidenciaviaje !='') {
                    $folioEv->evidencia_viaje=$request->get('comentarioevidencia');
                    $folioEv->save();
                }
                if ($request->hasFile('pdfevidencia')) {
                    $filePDF4=$request->file('pdfevidencia'); 
                    $filePDF4->move($folder,$filePDF4->getClientOriginalName());
                    $folioEv->evidencia_viaje=$request->get('comentarioevidencia');
                    $folioEv->pdfevidencia=$filePDF4->getClientOriginalName();
                }

                $folioEv->save();
                DB::commit();
            }
            catch(\Exception $e)
            {
                DB::rollback();
                return response()->json(['message' => $e->getMessage()]);
            }   
        }
        return redirect()->action('DetalleFolioController@show', [$folio->id_header_folio]);
    }

     public function edit($id)
    {
        $detalle=DetalleFolio::findOrFail($id);

        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;

        $company = substr($branch,0,1);                
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));
        
        return view("travel.gasto.edit",["userClaims"=>$userClaims, "detalle"=>$detalle]);
    }

    public function update (request $request, $id)
    { 
        $detalle=DetalleFolio::findOrFail($id);
        $detalle->fecha_gasto=$request->get('fecha_gasto');
        $detalle->gasto_tarjeta=$request->get('gasto_tarjeta');
        $detalle->gasto_efectivo=$request->get('gasto_efectivo');
        $detalle->viatico=$request->get('viatico');
        $detalle->save();

        
        $detalleUpdate=DB::table('ssm_viat_detalle_folio')
        ->select('id_header_folio', DB::raw('sum(gasto_tarjeta + gasto_efectivo + viatico ) as subtotal'))
        ->where('id_header_folio','=',$detalle->id_header_folio)
        ->where('company','=',Auth::user()->company)
        ->groupBy('id_header_folio')
        ->first();

        $folio=Folio::where('id_header_folio','=',$detalle->id_header_folio)
        ->where('company','=',Auth::user()->company)
        ->first();
        $folio->all_total=$detalleUpdate->subtotal;
        $folio->id_status='8';
        $folio->save();

        return redirect()->action('DetalleFolioController@show', [$detalle->id_header_folio]);

    }

     public function destroy($id)
    {   

        $detalle=DB::table('ssm_viat_detalle_folio')
        ->where('id_detalle_folio','=',$id)
        ->where('company','=',Auth::user()->company)
        ->first();

        $eliminar=DetalleFolio::findOrFail($id);
        $eliminar->delete();
        
        $folio=DB::table('ssm_viat_header_folio')
        ->where('id_header_folio','=',$detalle->id_header_folio)
        ->where('company','=',Auth::user()->company)
        ->first();

        if($folio->tipo=='Nacional'){
            $detalleUpdate=DB::table('ssm_viat_detalle_folio')
            ->select('id_header_folio', DB::raw('sum(importe) as subtotal'), DB::raw('sum(IVA) as iva'),DB::raw('sum(otro_impuesto) as impuesto') ,DB::raw('sum(importe + IVA + otro_impuesto ) as total'))
            ->where('id_header_folio','=',$detalle->id_header_folio)
            ->where('metodoPago','=','Efectivo')
            ->where('company','=',Auth::user()->company)
            ->groupBy('id_header_folio')
            ->first();
        }
        else{
            $detalleUpdate=DB::table('ssm_viat_detalle_folio')
            ->select('id_header_folio', DB::raw('sum(importeint) as subtotal'), DB::raw('sum(IVAint) as iva'),DB::raw('sum(otro_impuesto) as impuesto') ,DB::raw('sum(importeint + IVAint + otro_impuesto ) as total'))
            ->where('id_header_folio','=',$detalle->id_header_folio)
            ->where('metodoPago','=','Efectivo')
            ->where('company','=',Auth::user()->company)
            ->groupBy('id_header_folio')
            ->first();
        }
            
        if (!empty($detalleUpdate)) {
            $folioSum=Folio::where('id_header_folio','=',$detalle->id_header_folio)
            ->where('company','=',Auth::user()->company)
            ->first();
            $folioSum->all_total=$detalleUpdate->total;
            $folioSum->all_subtotal=$detalleUpdate->subtotal;
            $folioSum->all_iva=$detalleUpdate->iva;
            $folioSum->all_otros_imp=$detalleUpdate->impuesto;
            if($folioSum->id_status != '18'){
                $folioSum->id_status='8';
            }
            $folioSum->save();
        } else{
            $folioSum=Folio::where('id_header_folio','=',$detalle->id_header_folio)
            ->where('company','=',Auth::user()->company)
            ->first();
            $folioSum->all_total='0';
            $folioSum->all_subtotal='0';
            $folioSum->all_iva='0';
            $folioSum->all_otros_imp='0';
            if($folioSum->id_status != '18'){
                $folioSum->id_status='8';
            }
            $folioSum->save();
        }
        //dd($detalleUpdate->total);
        return redirect()->action('DetalleFolioController@show', [$detalle->id_header_folio]);
    }

    public function confirm($id)
    {
        $folio=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','u.name','tipo','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.dias','criterio','f.evidencia_viaje','f.pdfevidencia','m.moneda','f.anticipo','f.all_total','f._token')
        ->where('f.id_header_folio','=',$id)
        ->where('f.id_solicitante','=', Auth::user()->id)
        ->where('f.company','=',Auth::user()->company)
        ->first();

        $folioi=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
        ->select('f.id_header_folio','f.fecha','u.name','tipo','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.dias','criterio','m.moneda','f.anticipo', 'f.evidencia_viaje','f.pdfevidencia','t.montopesos','f.all_total','f._token')
        ->where('f.id_header_folio','=',$id)
        ->where('f.id_solicitante','=', Auth::user()->id)
        ->where('f.company','=',Auth::user()->company)
        ->first();

        $detalles=DB::table('VIEW_SSM_DETALLE_FOLIO')
        ->select('id_detalle_folio', 'id_header_folio','id_gasto','nomGasto','metodoPago', 'subtotal', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'comentarios', 'xml', 'pdf', 'fecha_factura' )
        ->where('id_header_folio','=',$id)
        ->get();

        //AGREGAR A LOS DETALLES EL IMPORTE EN PESOS, EL TIPO DE MONEDA, PARA PODER DIFERENCIAR ENTRE LOS GASTOS CREADOS 
        $detallesint=DB::table('VIEW_SSM_DETALLE_FOLIO_INT')
        ->select('id_detalle_folio', 'id_header_folio','id_gasto','nomGasto','metodoPago', 'moneda','tipomoneda','subtotal', 'Subtotalint', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'xml', 'pdf', 'comentarios', 'importeint', 'pdfint', 'fecha_factura', 'subtotalotro')
        ->where('id_header_folio','=',$id)
        ->get();

        $FlightPTE = DB::table('VIEW_SSM_FLIGHTS_NO')
        ->select('id_header_folio')
        ->where('id_header_folio', '=', $id)
        ->first();

        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;
        $company = substr($branch,0,1);                
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));
        
        return view("travel.gasto.confirm",["userClaims"=>$userClaims, "folio"=>$folio, "folioi"=>$folioi,"detalles"=>$detalles, "detallesint"=>$detallesint, "FlightPTE"=>$FlightPTE]);
    }

    public function cambio(Request $request){
        if($request->ajax()){
            $Cambio = DB::table('ssm_viat_tipocambio')
            ->where('fecha_cambio','=',$request->fechacambio)
            ->first();
            
            return response()->json(array('Cambio'=>$Cambio));

        }
    }
}

    
            