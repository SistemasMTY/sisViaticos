<?php

namespace sisViaticos\Http\Controllers;

use Illuminate\Http\Request;

use sisViaticos\Http\Requests;
use sisViaticos\User;
use sisViaticos\Vuelo;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

use DB;
use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;

class FlightsController extends Controller
{
    //
     public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        if ($request){
            // Gastos de Vuelo que cumplan con QRO, QRO,SLM, QRO,SLM,MTY
            if(Auth::user()->id==187 || Auth::user()->id==1195 || Auth::user()->id==2310){
                $query=trim($request->get('searchText'));
                $folios=DB::table('VIEW_SSM_FLIGHTS_NO')
                ->select('id_header_folio as folio','NombreCompleto as name','compania as company','fecha','tipo','destino','criterio', 'fecha_salida', 'fecha_llegada')
                ->whereIn('compania',['QRO', 'QRO,SLM', 'QRO,SLM,MTY']) #Pendiente de Revisar
	            ->where('NombreCompleto','LIKE','%'.$query.'%')
	            ->orwhere('id_header_folio','LIKE','%'.$query.'%')
                ->whereIn('compania',['QRO', 'QRO,SLM', 'QRO,SLM,MTY']) #Pendiente de Revisar
	            ->orderBy('id_header_folio','desc')
	            ->paginate(7);	            
            }

            // Informacion para planta SLM
            if(Auth::user()->id==2304){
                $query=trim($request->get('searchText'));
                $folios=DB::table('VIEW_SSM_FLIGHTS_NO')
                ->select('id_header_folio as folio','NombreCompleto as name','compania as company','fecha','tipo','destino','criterio', 'fecha_salida', 'fecha_llegada')
                ->whereIn('compania',['SLM'])
	            ->where('NombreCompleto','LIKE','%'.$query.'%')
                ->orwhere('id_header_folio','LIKE','%'.$query.'%')
                ->whereIn('compania',['QRO', 'QRO,SLM', 'QRO,SLM,MTY']) #Pendiente de Revisar
	            ->orderBy('id_header_folio','desc')
	            ->paginate(7);	  
            }

            // Informacion para planta MTY
           

            // Info para admins
            if(Auth::user()->id==2321|| Auth::user()->id==5 ){
                $query=trim($request->get('searchText'));
                $folios=DB::table('VIEW_SSM_FLIGHTS_NO')
                ->select('id_header_folio as folio','NombreCompleto as name','compania as company','fecha','tipo','destino','criterio', 'fecha_salida', 'fecha_llegada')
                ->whereIn('compania',['MTY'])
	            ->where('NombreCompleto','LIKE','%'.$query.'%')
                ->orwhere('id_header_folio','LIKE','%'.$query.'%')
                ->whereIn('compania',['MTY']) #Pendiente de Revisar
	            ->orderBy('id_header_folio','desc')
	            ->paginate(7);	  
            }

            $usernom = Auth::user()->numeroNomActual;
            $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($usernom));

            return view('administracion.vuelos.index',["userClaims"=>$userClaims, "folios"=>$folios,"searchText"=>$query]);
        }
    }

    public function show($id)
    {

        $folio=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('ssm_viat_flights as v', 'f.id_header_folio', '=', 'v.id_header_folio' )
        ->select('f.id_header_folio','f.fecha','u.name', 'f.id_solicitante','tipo','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.dias','criterio','m.moneda','f.anticipo','f._token', 'v.id_flight')
        ->where('f.id_header_folio','=',$id)
        ->first();

        // Traer la tabla para meter la informacion de la factura del vuelo 
        $foliovuelo = DB::select('EXEC SP_Get_Folio_Flight ?', Array($id));

        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;
        $usernom = Auth::user()->numeroNomActual;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($usernom));

        $monedas=DB::table('ssm_viat_moneda')->get();

        return view("administracion.vuelos.show",["userClaims"=>$userClaims, "folio"=>$folio, "foliovuelo"=>$foliovuelo, "monedas"=>$monedas]);
    }

    public function store(Request $request)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);
        ini_set('post_max_size', '84M');
        ini_set('upload_max_filesize', '84M');
              
        $folio=DB::table('ssm_viat_header_folio')
        ->where('id_header_folio','=',$request->get('id_folio'))
        ->first();
        
        try{
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
                }
                else
                if($MetodoPago =='USD'){
                   $MetodoPago2 = $request->get('id_moneda');
                   $TipoCambio = $request->get('importemxn');
                }
                else{
                    $MetodoPago2 = $request->get('id_moneda');
                    $TipoCambio = $request->get('importemxn');
                }
                
                if($FolioComprobante==""){   
                    $CFDIR = $docXML->getElementsByTagName('CFDIRegistroFiscal');
                    foreach ($CFDIR as $cfdi) {
                        $FolioComprobante = $cfdi->getAttribute("Folio") ;
                    }
                }

                // if ($FechaComprobante > $folio->fecha_llegada) {
                //     Session()->flash('msg','La factura no corresponde al periodo de viaje.');
                //     return view("administracion.vuelos.show",["folio"=>$folio]);  
                // }

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
                }else{
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
                if($jsonData['status'] == 'true'){
                    $respuesta = $jsonData['data'] . ' TT: ' . $jsonData['tipo'];
                    return response()->json(array('jsonData'=>$respuesta));
                }
                else{
                    if($jsonData['data'] = 'El XML ya ha sido cargado anteriormente'){
                        $fileXML->move($folder,$fileXML->getClientOriginalName());
                        $filePDF=$request->file('xpdf'); 
                        $filePDF->move($folder,$filePDF->getClientOriginalName());

                        $detalle=Vuelo::where('id_header_folio','=',$request->get('id_folio'))
                        ->whereNull('fecha_compra')
                        ->First();
                            
                        $detalle->branch=Auth::user()->company;
                        $detalle->id_header_folio=$folio->id_header_folio;
                        $detalle->fecha_compra=$FechaComprobante;
                        $detalle->proveedor=$nombreEmisor;
                        $detalle->RFC=$rfcEmisor;
                        $detalle->no_factura=$FolioComprobante;                        
                        $detalle->importe=$SubTotalComprobante;
                        $detalle->IVA=$IvaImpuesto;
                        $detalle->otro_impuesto=$IshImpLocal;
                        $detalle->total_vuelo = $SubTotalComprobante + $IvaImpuesto + $IshImpLocal;
                        $detalle->xml=$fileXML->getClientOriginalName();
                        $detalle->pdf=$filePDF->getClientOriginalName();
                        $detalle->UUID=$UUID1;
                        $detalle->usuario_subio = Auth::user()->numeroNom;
                        $detalle->id_cuenta='1';
                        $detalle->id_gasto = '9';
                        $detalle->fecha_insert = Carbon::now('America/Monterrey');
                        $detalle->tarjetaPago = $request->get('xTarjetaPago');
                        $detalle->id_moneda = $MetodoPago2;
                        $detalle->tipo_cambio = $TipoCambio;
    
                        $detalle->update(); 
                        
                        // Se realiza el stored procedure para subir los detalles
                        $addDetallesAmex = DB::UPDATE('EXEC Sp_Add_Items_AMEX ?,?,?,?', Array($UUID1, $request->get('id_folio'),$TasaRetencion, $Retencion));
                    }
                    else{
                        Session()->flash('msg',$jsonData['data']);
                    }
                }                  
            }
            else{
                $detalle=Vuelo::where('id_header_folio','=',$request->get('id_folio'))
                ->whereNull('Fecha_compra')
                ->First();
                    
                $detalle->branch=Auth::user()->company;
                $detalle->id_header_folio=$folio->id_header_folio;
                $detalle->fecha_compra=$request->get('fecha_factura');
                $detalle->proveedor='NO DEDUCIBLE';
                $detalle->RFC='XEX010101000';
                $detalle->no_factura=$request->get('noFactura');                        
                $detalle->importe=$request->get('importe');
                $detalle->IVA='0.00';
                $detalle->otro_impuesto='0.00';
                $detalle->total_vuelo = $request->get('importe');
                if ($request->hasFile('pdf')) {
                    $filePDF=$request->file('pdf'); 
                    $filePDF->move($folder,$filePDF->getClientOriginalName());
                    $detalle->pdf=$filePDF->getClientOriginalName();
                }
                $detalle->UUID='';
                $detalle->usuario_subio = Auth::user()->numeroNom;
                $detalle->id_cuenta='1';
                $detalle->id_gasto = '9';
                $detalle->fecha_insert = Carbon::now('America/Monterrey');
                $detalle->tarjetaPago = $request->get('TarjetaPago');
                $detalle->id_moneda = $request->get('metodo');
                $detalle->tipo_cambio = $request->get('importe');

                $detalle->update(); 

                $UUID1 = '';
                $TasaRetencion= '';
                $Retencion = '';
                
                // Se realiza el stored procedure para subir los detalles
                $addDetallesAmex = DB::UPDATE('EXEC Sp_Add_Items_AMEX ?,?,?,?', Array($UUID1, $request->get('id_folio'),$TasaRetencion, $Retencion));                
            }                                
            DB::commit();
        }
        catch(\Exception $e){
            DB::rollback();
            return response()->json(['message' => $e->getMessage()]);
        }   
        Session()->flash('msg','Se ha guardado el registro de comprobante de vuelo correctamente');
        return redirect()->action('FlightsController@show', [$folio->id_header_folio]);
        // return redirect()->action('FlightsController@index');
    }

    public function terminarFacturas($id, $id_folio, request $request){
        // Se realiza la busqueda de folio limpio 
        $folio=DB::table('ssm_viat_flights')
        ->where('id_header_folio', '=', $id_folio)
        ->whereNull('fecha_compra')
        ->first();

        // Realiza un procedimiento para eliminar los registros en blanco de ese folio
        $finalizar = DB::UPDATE('EXEC Sp_Delete_Folios_Blanco ?', Array($id_folio));
        
        Session()->flash('msg','Se ha finalizado el registro de comprobante de vuelo correctamente');
        return redirect()->action('FlightsController@index');
    }

    public function destroy($id, request $request)
    {   
        $folio=DB::table('ssm_viat_flights')
        ->where('id_header_folio','=',$id)
        ->first();

        $folioSum=Vuelo::where('id_header_folio','=',$folio->id_header_folio)
        ->where('branch','=',Auth::user()->company)
        ->first();

        $folioSum->motivo_descartar=$request->comentariosdescartar;
        $folioSum->usuario_subio=Auth::user()->numeroNom;
        $folioSum->fecha_insert=Carbon::now('America/Monterrey');
        $folioSum->save();

        return redirect()->action('FlightsController@index');
    }
}
