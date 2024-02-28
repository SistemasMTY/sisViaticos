<?php

namespace sisViaticos\Http\Controllers;

use Illuminate\Http\Request;
use sisViaticos\Http\Requests;
use sisViaticos\User;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;

class ReportesGastosNoComprobadosController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {
        $query=trim($request->get('searchText'));

        //$folio = DB::select('EXEC Sp_Get_Reporte_Folios_Retraso');

        $folio=DB::table('VIEW_SSM_REPORTE_FOLIOS_RUSUARIO')
        ->select('company','name','depto','division','foliosRetraso', 'Año_fecha')
        ->where('Año_fecha','LIKE','%'.$query.'%')
        ->get();

        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;

        $company = substr($branch,0,1);  
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));


        return view('reports.comprobacionesRetrasos.index',["userClaims"=>$userClaims, "folio"=>$folio, "searchText"=>$query]);
    }

    public function show($id)
    {
        $values = [
            $id
        ]; 
        $folio = DB::select('EXEC Sp_Get_Reporte_Folios_Retraso_Usuario ?', $values);


        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;

        $company = substr($branch,0,1);  
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

        return view('reports.comprobacionesRetrasos.ReporteUsuario',["userClaims"=>$userClaims, "folio"=>$folio]);
    }
}
?>