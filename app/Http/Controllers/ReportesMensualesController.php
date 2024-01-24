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

class ReportesMensualesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request)
    {

        $fechaI=trim($request->get('fechaI'));
        if ($fechaI=="") {
            # code...
            $fechaI = NULL;
        }
        
        $fechaF=trim($request->get('fechaF'));
        if ($fechaF=="") {
            # code...
            $fechaF = NULL;
        }

        $values = [
            $fechaI,
            $fechaF,
        ];

        $folio = DB::select('EXEC SP_Get_Anticipos_AC_Total ?, ?', $values);


        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;

        $company = substr($branch,0,1);  
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

        if ($fechaI== NULL) {
            # code...
            $fechaI = 0;
        }
        
        if ($fechaF== NULL) {
            # code...
            $fechaF = 0;
        }

        return view('reports.reportes.index',["userClaims"=>$userClaims, "folio"=>$folio,"FechaI"=>$fechaI,"FechaF"=>$fechaF]);
    }

    public function show($FechaI, $FechaF, $id, $status)
    {
        
        if ($FechaI=="0") {
            # code...
            $FechaI = NULL;
        }
        
        if ($FechaF=="0") {
            # code...
            $FechaF = NULL;
        }

        $values = [
            $FechaI,
            $FechaF,
            $id, 
            $status
        ];

        $folio = DB::select('EXEC SP_Get_Anticipos_Persona ?, ?, ?, ?', $values);


        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;

        $company = substr($branch,0,1);  
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

        return view('reports.reportes.ReporteUsuario',["userClaims"=>$userClaims, "folio"=>$folio]);
    }
}
?>