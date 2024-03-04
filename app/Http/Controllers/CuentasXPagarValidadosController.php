<?php

namespace sisViaticos\Http\Controllers;

use Illuminate\Http\Request;
use sisViaticos\Http\Requests;
use sisViaticos\DetalleFolio;
use sisViaticos\Folio;
use sisViaticos\Moneda;
use sisViaticos\Status;
use sisViaticos\User;
use sisViaticos\Repayment;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

use Yajra\DataTables\Services\DataTable;

use DB;
use Carbon\Carbon;

class CuentasXPagarValidadosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request) {
            $fechaI = trim($request->get('fechaI'));
            $query = trim($request->get('searchText'));
            if ($fechaI == "") {
                $fechaI = Carbon::create(2018, 1, 1, 0, 0, 0, 'America/Monterrey');
            }

            $fechaF = trim($request->get('fechaF'));
            if ($fechaF == "") {
                $fechaF = Carbon::now('America/Monterrey');
            }

            if (in_array(Auth::user()->id, [7, 1038, 2291, 10, 27, 2249, 2260, 2196, 2217, 2261, 2319])) {
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

            $folios = DB::table('VIEW_SSM_FOLIOS_CCP')
            ->select('id_header_folio as folio', 'name', 'company', 'fecha', 'fecha_salida', 'fecha_llegada', 'tipo', 'destino', 'id_status', 'anticipo', 'all_total', 'status', 'descripcion')
            ->whereIn('BranchRH', $companias)
            ->where('name', 'LIKE', '%' . $query . '%')
            ->whereIn('id_status', ['16'])
            ->whereBetween('fecha_salida', [$fechaI, $fechaF])
            ->orwhere('id_header_folio', 'LIKE', '%' . $query . '%')
            ->whereIn('BranchRH', $companias)
            ->whereIn('id_status', ['16'])
            ->whereBetween('fecha_salida', [$fechaI, $fechaF])
            ->orderBy('id_header_folio', 'desc')
            ->get();           

            $usernom = Auth::user()->numeroNom;
            $branch = Auth::user()->company;
            $company = substr($branch, 0, 1);
            $valuesUser = $company . $usernom;

            $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', array($valuesUser));

            return view('accounting.validados.index', ["folios" => $folios, "searchText" => $query, "FechaI" => $fechaI, "FechaF" => $fechaF, "userClaims" => $userClaims]);
        }
    }

    public function show($id)
    {
        $folio = DB::table('ssm_viat_header_folio as f')
            ->join('users as u', 'f.id_solicitante', '=', 'u.id')
            ->join('ssm_viat_moneda as m', 'f.id_moneda', '=', 'm.id_moneda')
            ->select('f.id_header_folio', 'f.fecha', 'u.name', 'f.id_status', 'f.tipo', 'f.destino', 'f.proposito', 'f.evidencia_viaje', 'f.pdfevidencia', 'f.eq_computo', 'f.fecha_salida', 'f.fecha_llegada', 'f.dias', 'criterio', 'm.moneda', 'f.anticipo', 'f.all_total', 'f._token')
            ->where('f.id_header_folio', '=', $id)
            ->first();

        $detalles = DB::table('VIEW_SSM_DETALLE_FOLIO')
            ->select('id_detalle_folio', 'id_header_folio', 'id_gasto', 'nomGasto', 'metodoPago', 'subtotal', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'comentarios', 'xml', 'pdf', 'fecha_factura')
            ->where('id_header_folio', '=', $id)
            ->get();

        //AGREGAR A LOS DETALLES EL IMPORTE EN PESOS, EL TIPO DE MONEDA, PARA PODER DIFERENCIAR ENTRE LOS GASTOS CREADOS 
        $detallesint = DB::table('VIEW_SSM_DETALLE_FOLIO_INT')
            ->select('id_detalle_folio', 'id_header_folio', 'id_gasto', 'nomGasto', 'metodoPago', 'moneda', 'tipomoneda', 'subtotal', 'subtotalint', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'xml', 'pdf', 'comentarios', 'importeint', 'pdfint', 'fecha_factura', 'subtotalotro')
            ->where('id_header_folio', '=', $id)
            ->get();

        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;
        $company = substr($branch, 0, 1);
        $valuesUser = $company . $usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', array($valuesUser));

        return view('accounting.validados.show', ["userClaims" => $userClaims, "folio" => $folio, "detalles" => $detalles, "detallesint" => $detallesint]);
    }

    public function report($id)
    {
        $reporte = DB::select("EXEC SP_get_viat_reporte_gastos  ?", array($id));
        $folio = DB::table('ssm_viat_header_folio')
            ->select('id_header_folio')
            ->where('id_header_folio', '=', $id)
            ->first();

        $cuentas = DB::table('VIEW_SSM_SAP_CUENTAS')
            ->select('AcctCode', 'FormatCode', 'NameCuenta')
            ->Get();

        $suma = DB::table('ssm_viat_reporte_gastos')
            ->select(DB::raw('SUM(debe) as total'))
            ->where('id_header_folio', '=', $id)
            ->where('MetodoPago', '=', 'Efectivo')
            ->first();

        // Traer el conteo de datos registrados con tarjeta AMEX Si es mayor a 1, se mostrara el boton

        $count = DB::table('ssm_viat_reporte_gastos')
            ->where('MetodoPago', '=', 'AMEX')
            ->where('id_header_folio', '=', $id)
            ->count();

        $detallesAMEX = DB::select("EXEC Sp_Get_viat_detalles_AMEX ?", array($id));

        $tiposRetenciones = DB::select("EXEC Sp_Get_Tipos_Retenciones");

        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;
        $company = substr($branch, 0, 1);
        $valuesUser = $company . $usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', array($valuesUser));

        return view('accounting.validados.reporte', ["userClaims" => $userClaims, "cuentas" => $cuentas, "folio" => $folio, "reporte" => $reporte, "suma" => $suma, "count" => $count, "detallesAMEX" => $detallesAMEX, "tiposRetenciones" => $tiposRetenciones]);
    }

    public function updateTipoAMEX(Request $request)
    {
        if ($request->ajax()) {
            try {
                DB::update("EXEC SP_update_viat_TipoRetencion ?, ?", array($request->WTCode, $request->id_detalleAmex));
            } catch (\Trhowable $th) {
                DB::rollback();
                return response()->json(['errors' => $th]);
            }
            return response()->json([
                'validated' => true
            ]);
        }
    }

    public function updatecuentaitemAMEX(Request $request)
    {
        if ($request->ajax()) {
            try {
                DB::update("EXEC SP_update_viat_CuentaItem ?, ?", array($request->AcctCode, $request->id_detalleAmex));
            } catch (\Trhowable $th) {
                DB::rollback();
                return response()->json(['errors' => $th]);
            }
            return response()->json([
                'validated' => true
            ]);
        }
    }

    public function DeleteRetencion(Request $request)
    {
        if ($request->ajax()) {
            try {
                DB::update("EXEC SP_Delete_viat_CuentaItem ?", array($request->id_detalleAmex));
            } catch (\Trhowable $th) {
                DB::rollback();
                return response()->json(['errors' => $th]);
            }
            return response()->json([
                'validated' => true
            ]);
        }
    }

    public function updatecuenta(Request $request)
    {
        if ($request->ajax()) {
            try {
                DB::update("EXEC SP_update_viat_gastos ?, ?", array($request->AcctCode, $request->id_rep_gasto));
            } catch (\Trhowable $th) {
                DB::rollback();
                return response()->json(['errors' => $th]);
            }
            return response()->json([
                'validated' => true
            ]);
        }
    }

    public function detalleAccount(Request $request)
    {
        if ($request->ajax()) {
            $detalles = DB::select("EXEC SP_get_viat_cuenta ?", array($request->id_rep_gasto));
            if (count($detalles) > 0) {
                return response()->json(array('detalles' => $detalles));
            }
        }
    }

    public function detalleAccountAMEX(Request $request)
    {
        if ($request->ajax()) {
            $detalles = DB::select("EXEC Sp_Get_viat_detalles_AMEX ?", array($request->id_header_folio));
            if (count($detalles) > 0) {
                return response()->json(array('detalles' => $detalles));
            }
        }
    }

    public function reporttotales(Request $request)
    {
        if ($request) {
            $fechaI = trim($request->get('fechaI'));
            if ($fechaI == "") {
                $fechaI = Carbon::create(2018, 1, 1, 0, 0, 0, 'America/Monterrey');
            }

            $fechaF = trim($request->get('fechaF'));
            if ($fechaF == "") {
                $fechaF = Carbon::now('America/Monterrey');
            }

            if (Auth::user()->id == 7) {
                $totales = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select('NombreCompleto', 'TrabajadorID', 'fecha_salida', 'Dtotal', 'Ttotal', 'Totales')
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->whereIn('compania', ['SLM', 'QRO']) #Para Coral de QRO
                    ->orderBy('id_header_folio', 'asc')
                    ->get();

                $suma = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select(DB::raw('SUM(Dtotal) as total'))
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->whereIn('compania', ['SLM', 'QRO'])
                    ->first();

                $suma1 = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select(DB::raw('SUM(Ttotal) as total'))
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->whereIn('compania', ['SLM', 'QRO'])
                    ->first();

                $suma2 = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select(DB::raw('SUM(Totales) as total'))
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->whereIn('compania', ['SLM', 'QRO'])
                    ->first();
            }
            if (Auth::user()->id == 6) {
                $totales = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select('NombreCompleto', 'TrabajadorID', 'fecha_salida', 'Dtotal', 'Ttotal', 'Totales')
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->where('compania', '=', 'QRO') #Para Zulem de QRO
                    ->orderBy('id_header_folio', 'asc')
                    ->get();

                $suma = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select(DB::raw('SUM(Dtotal) as total'))
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->where('compania', '=', 'QRO') #Para Zulem de QRO
                    ->first();

                $suma1 = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select(DB::raw('SUM(Ttotal) as total'))
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->where('compania', '=', 'QRO') #Para Zulem de QRO
                    ->first();

                $suma2 = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select(DB::raw('SUM(Totales) as total'))
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->where('compania', '=', 'QRO') #Para Zulem de QRO
                    ->first();
            }
            if (Auth::user()->id == 10 || Auth::user()->id == 27 || Auth::user()->id == 2249 || Auth::user()->id == 2260 || Auth::user()->id == 2196 || Auth::user()->id == 2230) {
                $totales = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select('NombreCompleto', 'TrabajadorID', 'fecha_salida', 'Dtotal', 'Ttotal', 'Totales')
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->whereIn('compania', ['SLM', 'QRO', 'QRO,SLM,MTY']) #Para Nancy
                    ->orderBy('id_header_folio', 'asc')
                    ->get();

                $suma = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select(DB::raw('SUM(Dtotal) as total'))
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->whereIn('compania', ['SLM', 'QRO']) #Para Nancy
                    ->first();

                $suma1 = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select(DB::raw('SUM(Ttotal) as total'))
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->whereIn('compania', ['SLM', 'QRO']) #Para Nancy
                    ->first();

                $suma2 = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select(DB::raw('SUM(Totales) as total'))
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->whereIn('compania', ['SLM', 'QRO']) #Para Nancy
                    ->first();
            }
            if (Auth::user()->id == 1190) {
                $totales = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select('NombreCompleto', 'TrabajadorID', 'fecha_salida', 'Dtotal', 'Ttotal', 'Totales')
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->whereIn('compania', ['SLM', 'QRO']) #Para Nancy
                    ->orderBy('id_header_folio', 'asc')
                    ->get();

                $suma = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select(DB::raw('SUM(Dtotal) as total'))
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->whereIn('compania', ['SLM', 'QRO']) #Para Nancy
                    ->first();

                $suma1 = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select(DB::raw('SUM(Ttotal) as total'))
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->whereIn('compania', ['SLM', 'QRO']) #Para Nancy
                    ->first();

                $suma2 = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select(DB::raw('SUM(Totales) as total'))
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->whereIn('compania', ['SLM', 'QRO']) #Para Nancy
                    ->first();
            } elseif (Auth::user()->id == 1 || Auth::user()->id == 1195) {
                $totales = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select('NombreCompleto', 'TrabajadorID', 'fecha_salida', 'Dtotal', 'Ttotal', 'Totales')
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->whereIn('compania', ['SLM', 'QRO', 'MTY'])
                    ->orderBy('id_header_folio', 'asc')
                    ->get();

                $suma = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select(DB::raw('SUM(Dtotal) as total'))
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->whereIn('compania', ['SLM', 'QRO', 'MTY'])
                    ->first();

                $suma1 = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select(DB::raw('SUM(Ttotal) as total'))
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->whereIn('compania', ['SLM', 'QRO', 'MTY'])
                    ->first();

                $suma2 = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select(DB::raw('SUM(Totales) as total'))
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->whereIn('compania', ['SLM', 'QRO', 'MTY'])
                    ->first();
            }

            if (Auth::user()->id == 4 || Auth::user()->id == 5) {
                $totales = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select('NombreCompleto', 'TrabajadorID', 'fecha_salida', 'Dtotal', 'Ttotal', 'Totales')
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->where('compania', '=', 'MTY') #Para Zulem de QRO
                    ->orderBy('id_header_folio', 'asc')
                    ->get();

                $suma = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select(DB::raw('SUM(Dtotal) as total'))
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->where('compania', '=', 'MTY') #Para Zulem de QRO
                    ->first();

                $suma1 = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select(DB::raw('SUM(Ttotal) as total'))
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->where('compania', '=', 'MTY') #Para Zulem de QRO
                    ->first();

                $suma2 = DB::table('VIEW_SSM_FOLIOS_TOTALES')
                    ->select(DB::raw('SUM(Totales) as total'))
                    ->whereBetween('fecha_salida', [$fechaI, $fechaF])
                    ->where('compania', '=', 'MTY') #Para Zulem de QRO
                    ->first();
            }
        }

        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;
        $company = substr($branch, 0, 1);
        $valuesUser = $company . $usernom;
        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', array($valuesUser));

        return view('accounting.validados.reportetotal', ["userClaims" => $userClaims, "totales" => $totales, "FechaI" => $fechaI, "FechaF" => $fechaF, "suma" => $suma, "suma1" => $suma1, "suma2" => $suma2]);
    }

    public function statusFolios()
    {
        if (Auth::user()->id == 7) {
            $estatus = DB::table('VIEW_SSM_STATUS_COMPROBACION')
                ->select('id_header_folio', 'NombreCompleto', 'fecha_salida', 'descripcion', 'id_gasto', 'NOMBREAUTO')
                ->whereIn('company', ['SLM', 'QRO']) #Para Coral de QRO
                ->orderBy('id_header_folio', 'asc')
                ->get();
        }
        if (Auth::user()->id == 6) {
            $estatus = DB::table('VIEW_SSM_STATUS_COMPROBACION')
                ->select('id_header_folio', 'NombreCompleto', 'fecha_salida', 'descripcion', 'id_gasto', 'NOMBREAUTO')
                ->where('company', '=', 'QRO') #Para Zulem de QRO
                ->orderBy('id_header_folio', 'asc')
                ->get();
        }
        if (Auth::user()->id == 10 || Auth::user()->id == 27 ||  Auth::user()->id == 2249 || Auth::user()->id == 2260 || Auth::user()->id == 2196  || Auth::user()->id == 2217 || Auth::user()->id == 2230 || Auth::user()->id==2319) {
            $estatus = DB::table('VIEW_SSM_STATUS_COMPROBACION')
                ->select('id_header_folio', 'NombreCompleto', 'fecha_salida', 'descripcion', 'id_gasto', 'NOMBREAUTO')
                ->whereIn('company', ['SLM', 'QRO']) #Para Nancy
                ->orderBy('id_header_folio', 'asc')
                ->get();
        }
        if (Auth::user()->id == 1190) {
            $estatus = DB::table('VIEW_SSM_STATUS_COMPROBACION')
                ->select('id_header_folio', 'NombreCompleto', 'fecha_salida', 'descripcion', 'id_gasto', 'NOMBREAUTO')
                ->whereIn('company', ['SLM', 'QRO']) #Para Nancy
                ->orderBy('id_header_folio', 'asc')
                ->get();
        } elseif (Auth::user()->id == 1 || Auth::user()->id == 1195) {
            $estatus = DB::table('VIEW_SSM_STATUS_COMPROBACION')
                ->select('id_header_folio', 'NombreCompleto', 'fecha_salida', 'descripcion', 'id_gasto', 'NOMBREAUTO')
                ->whereIn('company', ['SLM', 'QRO', 'MTY'])
                ->orderBy('id_header_folio', 'asc')
                ->get();
        }

        if (Auth::user()->id == 4 || Auth::user()->id == 5) {
            $estatus = DB::table('VIEW_SSM_STATUS_COMPROBACION')
                ->select('id_header_folio', 'NombreCompleto', 'fecha_salida', 'descripcion', 'id_gasto', 'NOMBREAUTO')
                ->where('company', '=', 'MTY') #Para Zulem de QRO
                ->orderBy('id_header_folio', 'asc')
                ->get();
        }

        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;
        $company = substr($branch, 0, 1);
        $valuesUser = $company . $usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', array($valuesUser));

        return view('accounting.status', ["userClaims" => $userClaims, "estatus" => $estatus]);
    }

    public function subirSAP(Request $request)
    {
        if($request->ajax()){                        
            $client = new \GuzzleHttp\Client();
            $response = Http::get("http://www.summitmx.com:8001/sap_api/createAPIJournalEntry.php?id_folio=" . $request->id_folio . "&username=" . $request->username . "&password=" .$request->password ."");
            $jsonData = $response->json();
            if ($jsonData['status'] == 'true') {
                $folio = DB::table('ssm_viat_header_folio')
                    ->where('id_header_folio', '=', $request->id_folio)
                    ->first();
                $respuesta = $jsonData['message'] . $folio->ACSAP;
                return response()->json(array('jsonData' => $respuesta, 'status' => true));
            } else {
                $mensajeError = $jsonData['message'];
                return response()->json(array('jsonData' => $mensajeError, 'status' => false));
            }
            
            // return response()->json(array('jsonData'=>$jsonData['message']));
            // return response()->json(array('jsonData'=>$jsonData));
        }
    }

}
