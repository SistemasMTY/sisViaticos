<?php

namespace sisViaticos\Http\Controllers;

use Illuminate\Http\Request;
use sisViaticos\Http\Requests;
use sisViaticos\User;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;

class ReportesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {

        $query=trim($request->get('searchText'));
        $folio=DB::table('SSM_VIEW_VISITAS_CLIENTES')
        ->select('id_header_folio', 'fecha_salida','nombre','destino','proposito','anticipo','Devolucion')
        ->where('fecha_salida','LIKE','%'.$query.'%')
        ->groupBy('id_header_folio', 'fecha_salida','nombre','destino','proposito','anticipo','Devolucion')
        ->get();

        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;

        $company = substr($branch,0,1);                
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

        return view('travel.reportes.reporte',["userClaims"=>$userClaims, "folio"=>$folio,"searchText"=>$query]);
    }
}
?>