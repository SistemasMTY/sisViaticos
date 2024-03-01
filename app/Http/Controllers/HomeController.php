<?php

namespace sisViaticos\Http\Controllers;

use Illuminate\Http\Request;

use sisViaticos\Http\Requests;

use sisViaticos\Folio;
use sisViaticos\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use sisViaticos\Http\Controllers\Controller;
use Validator;
use sisViaticos\Event;
 
use DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $events = DB::table('ssm_viat_header_folio as f')
        ->join('users as u', 'f.id_solicitante', '=', 'u.id')
        ->select('f.id_header_folio as id', 'u.name', 'f.destino', 'f.proposito', 'f.fecha_salida', 'f.fecha_llegada')
        ->where('f.id_status', '!=', '17')
        ->get();

        $event_list = [];
        foreach ($events as $key => $event) {
            $event_list[] = [
                'title' => $event->name,
                'start' => $event->fecha_salida,
                'end' => date('Y-m-d', strtotime($event->fecha_llegada . ' +1 day')),
                'id' => $event->id,
                'color' => "#" . dechex(rand(0x000000, 0xFFFFFF)),
                'description' => "Folio: " . $event->id . "<br>Destino: " . $event->destino . '<br>Motivo: ' . $event->proposito,
            ];
        }

        $calendar_details = [
            'events' => $event_list,
            'callbacks' => [
                'eventClick' => 'function(calEvent, jsEvent, view) {
                    $("#modalTitle").html(calEvent.title);
                    $("#modalBody").html(calEvent.description);
                    $("#eventUrl").attr("href", calEvent.url);
                    $("#fullCalModal").modal();
                }'
            ]
        ];

        $Approbs = DB::table('ssm_viat_autorizadores as a')
        ->join('users as u','a.id_user','=','u.id')
        ->where('a.id_user','=',Auth::user()->id)
        ->get();

        //Agregar datos de userClaims
        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;

        $company = substr($branch,0,1);                
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));
       
        return View('home', compact('calendar_details','Approbs', 'userClaims'));
    }
}
