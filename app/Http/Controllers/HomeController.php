<?php

namespace sisViaticos\Http\Controllers;

use Illuminate\Http\Request;

use sisViaticos\Http\Requests;

use sisViaticos\Folio;
use sisViaticos\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use sisViaticos\Http\Controllers\Controller;
use Validator;
use sisViaticos\Event;
 
use Calendar;
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
        // return view('home');
        $events = DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->select('f.id_header_folio as id','u.name','f.destino','f.proposito','f.fecha_salida','f.fecha_llegada')
        ->where('f.id_status','!=','17')
        ->get();
        // $events = DB::table('ssm_viat_header_folio')
        //  ->select('id_header_folio as id','destino','proposito','fecha_salida','fecha_llegada')
        //  ->get();
        $event_list = [];
        foreach ($events as $key => $event) {
             # code...
            $event_list[] = Calendar::event(
                $event->name,
                true,
                new \DateTime($event->fecha_salida),
                new \DateTime($event->fecha_llegada.' +1 day'),
                $event->id,
                [
                    // 'color' => sprintf("#%06x",rand(0,16777215)),
                    'color' => "#".dechex(rand(0x000000, 0xFFFFFF)),
                    'description' => "Folio: ". $event->id."<br>Destino: " . $event->destino. '<br>Motivo: '.$event->proposito
                ]
            );
        }

        $calendar_details = Calendar::addEvents($event_list)
        ->setCallbacks([ //set fullcalendar callback options (will not be JSON encoded)
                'eventClick' => 'function(calEvent, jsEvent, view) {
                    $("#modalTitle").html(calEvent.title);
                    $("#modalBody").html(calEvent.description);
                    $("#eventUrl").attr("href",calEvent.url);
                    $("#fullCalModal").modal();
                }'
            ]);

        $Approbs = DB::table('ssm_viat_autorizadores as a')
        ->join('users as u','a.id_user','=','u.id')
        ->where('a.id_user','=',Auth::user()->id)
        ->get();

        //dd($Approbs);

        //Agregar datos de userClaims

        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;

        $company = substr($branch,0,1);                
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));
       
        return View('home', compact('calendar_details','Approbs', 'userClaims'));
        //die($valuesUser);
         
        // return view('home', compact('calendar_details'));
    }
}
