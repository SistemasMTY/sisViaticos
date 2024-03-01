<?php

namespace sisViaticos\Http\Controllers;

use Illuminate\Http\Request;

use sisViaticos\Http\Requests;
use sisViaticos\Folio;
use sisViaticos\Moneda;
use sisViaticos\Status;
use sisViaticos\User;
use sisViaticos\FirmaAnticipo;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use sisViaticos\Http\Requests\FolioFormRequest;
use Illuminate\Support\Str;

use DB;
use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;

class FolioController extends Controller
{
    //
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
            ->join('ssm_viat_firma_anticipo as anti', function ($join) {
                $join->on('f.id_header_folio', '=', 'anti.id_header_folio')
                    ->where('anti.status','=',"0");
            })
            ->leftjoin('VIEW_SSM_GET_AUTHORIZERS as auto', 'anti.id_autorizador','=','auto.TrabajadorID')
            ->select('f.id_header_folio as folio', 'f.fecha','f.tipo','f.destino','f.anticipo', 's.status','s.descripcion','f.fecha_salida','f.fecha_llegada','anti.id_autorizador','auto.NombreAuto as autorizador')
            ->where('f.destino','LIKE','%'.$query.'%')
            ->where('f.id_solicitante','=', Auth::user()->id)
            ->where('f.company','=',Auth::user()->company)
            ->whereIn('f.id_status',['1','2','3','4','5','6','7'])
            ->orderBy('f.fecha_salida','asc')
            ->paginate(7);

            $usernom = Auth::user()->numeroNom;
            $branch = Auth::user()->company;
    
            $company = substr($branch,0,1);                
            $valuesUser = $company.$usernom;
    
            $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

            // Obtener el conteo de los folios abiertos actualmente
            $conteoProceso = DB::select('EXEC SP_Get_Folios_EnProceso ?', Array($valuesUser));

            return view('travel.solicitud.index',["userClaims"=>$userClaims, "folios"=>$folios,"searchText"=>$query, "conteoProceso" =>$conteoProceso]);
        }


    }
    public function create()
    {	
        $monedas=DB::table('ssm_viat_moneda')->get();
        $autorizadores=DB::select("EXEC SP_get_authorizers  ?, ?", Array (Auth::user()->numeroNom, Auth::user()->company));
        
        // return view("travel.solicitud.create",["monedas"=>$monedas]);
        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;

        $company = substr($branch,0,1);                
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

        return view("travel.solicitud.create",["userClaims"=>$userClaims, "monedas"=>$monedas, "autorizadores"=>$autorizadores]);
        
    }

    public function store (FolioFormRequest $request)
    {
        $folios=DB::table('ssm_viat_header_folio')
        ->where('id_solicitante','=', Auth::user()->id)
        ->where('id_status','<','16')
        ->where('id_status','>','0')
        ->where(function ($query) use($request)
        {
            $query->where('fecha_llegada','>=',$request->get('fecha_salida'))
            ->where('fecha_salida','<=',$request->get('fecha_llegada'));;
        })
        ->get();

        if(count($folios)==0)
        {   
            try{
                DB::beginTransaction();

                $date1 = new Carbon($request->get('fecha_salida'));
                $date2 = new Carbon($request->get('fecha_llegada'));
                $diff = $date1->diff($date2);
                $mytime = Carbon::now('America/Monterrey');

                //Toma los correos de los autorizadores que se encontraron 
                $categoria = $request->get('categoria');
                $a1 = $request->get('auto_1'); //Autorizador1
                $a2 = $request->get('auto_2'); //Autorizador2
                //Revisa si existe autorizador 1

                if($request->get('destinoviaje') === 'OTRO'){
                    $destino = $request->get('destino');
                }
                else{
                    if ($request->get('destinoviaje') === 'SSMQUERETARO'){
                        $destino = 'SSM QUERETARO';
                    }
                    else if ($request->get('destinoviaje') === 'SSMMONTERREY'){
                        $destino = 'SSM MONTERREY';
                    }
                    else{
                        $destino = 'SSM SALAMANCA';
                    }
                }

                $folio = Folio::create([
                    'company'=>Auth::user()->company,
                    'fecha'=>$mytime,
                    'id_solicitante'=>$request->get('id_solicitante'),
                    'correo_solicitante'=>Auth::user()->email,
                    'tipo'=>$request->get('tipo'),
                    'id_status'=>'1',
                    'destino'=>$destino,
                    'proposito'=>$request->get('proposito'),
                    'eq_computo'=>$request->get('eq_computo'),
                    'fecha_salida'=>$request->get('fecha_salida'),
                    'fecha_llegada'=>$request->get('fecha_llegada'),
                    'dias'=>$diff->days + 1,
                    'criterio'=>$request->get('criterio'),
                    'id_moneda'=>$request->get('id_moneda'),
                    'anticipo'=>$request->get('anticipo'),
                    'all_subtotal'=>'0',
                    'all_iva'=>'0',
                    'all_otros_imp'=>'0',
                    'all_total'=>'0',
                    'correo_auto1'=>$a1,
                    'correo_auto2'=>$a2,
                    '_token'=>Str::random(40)
                ]);

                $firma = new FirmaAnticipo;

                $firma->company=Auth::user()->company;
                $firma->id_user=$request->get('id_solicitante');
                $firma->id_header_folio=$folio->id_header_folio;
                $firma->status='0';
                $firma->save();
                
                DB::commit();

            }catch(\Exception $e)
            {
                DB::rollback();
            }

            return Redirect::to('travel/solicitud');
            
        }else
        {   
            Session()->flash('msg','Lo sentimos, pero actualmente cuentas con Solicitudes de Viajes activas en las fechas seleccionadas.');

            return Redirect::to("travel/solicitud/create")->withInput();
        }

        //return Redirect::to('travel/solicitud');
    }
    public function show($id)
    {

        $folio=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','u.name', 'f.id_solicitante','tipo','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.dias','criterio','m.moneda','f.anticipo','f._token')
        ->where('f.id_header_folio','=',$id)
        ->where('f.company','=',Auth::user()->company)
        ->first();

        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;

        $company = substr($branch,0,1);                
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

        return view("travel.solicitud.show",["userClaims"=>$userClaims, "folio"=>$folio]);
    }
   
    public function destroy($id)
    {
        $folio=Folio::findOrFail($id);
        $folio->id_status='17';
        $folio->update();
        
        //Realiza la comprobacion de la informacion si existe autorizadores en lista 
        //Revisa si existen folios de anticipo con autorizadores 

        $getAutos=FirmaAnticipo::where('id_header_folio','=',$folio->id_header_folio)
        ->where('Id_autorizador','>','0')
        ->where('status','=','0')
        ->get();

        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        //->join('user_profile as p','u.numeroNom','=','p.numeroNom')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.anticipo','m.moneda','u.name','f.correo_solicitante as emailU','u.company','f.correo_auto1','f._token')
        ->where('f.id_header_folio','=',$folio->id_header_folio)
        ->where('_token','=',$folio->_token)
        ->first();

        //Realiza el conteo de los autorizadores para enviar el correo de cancelacion

        if(count($getAutos)>0)
            {
                foreach ($getAutos as $getAuto) {

                    $id_autorizador=$getAuto->Id_autorizador;

                    $Autorizador=DB::table('VIEW_SSM_GET_AUTHORIZERS')
                    ->where('TrabajadorID','=',$id_autorizador)
                    ->first();

                    Mail::Send('mails.cancelFolio', ['folioMail'=> $folioMail], function($mail) use($folioMail, $Autorizador){
                            $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                            $mail->to($Autorizador->email, $Autorizador->NombreAuto);
                            // $mail->to('enedelia.alanis@summitmx.com');
                    });

                }
            }
        
        //Se eliminan los datos existentes dentro de la tabla de de anticipo, donde existan informacion de autorizadores
        $firmasAuto=FirmaAnticipo::where('id_header_folio','=',$folio->id_header_folio)
        ->where('Id_autorizador','>','0')
        ->delete();

        return Redirect::to('travel/solicitud');
    }

    public function edit($id)
    {
        $folio=DB::table('ssm_viat_header_folio')
        ->where('id_header_folio','=',$id)
        ->where('company','=',Auth::user()->company)
        ->first();;
        $monedas=DB::table('ssm_viat_moneda')->get();
        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;

        $company = substr($branch,0,1);                
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

        return view("travel.solicitud.edit",["userClaims"=>$userClaims, "folio"=>$folio,"monedas"=>$monedas]);
        //return $folio;
    }

    public function update(FolioFormRequest $request, $id)
    {   
        $folio=Folio::findOrFail($id);

        $folios=DB::table('ssm_viat_header_folio')
        ->where('id_solicitante','=', Auth::user()->id)
        ->where('id_status','<','16')
        ->where('id_status','>','0')
        ->whereNotIn('id_header_folio',[$folio->id_header_folio])
        ->where(function ($query) use($request)
        {
            $query->where('fecha_llegada','>=',$request->get('fecha_salida'))
            ->where('fecha_salida','<=',$request->get('fecha_llegada'));;
        })
        ->get();

        //dd($folios,$request->get('fecha_salida'),$request->get('fecha_llegada') );

        //dd($folio);

        if(count($folios)==0)
        {   
            try{
                DB::beginTransaction();

                $date1 = new Carbon($request->get('fecha_salida'));
                $date2 = new Carbon($request->get('fecha_llegada'));
                $diff = $date1->diff($date2);

                $folio->id_status='1';
                $folio->tipo=$request->get('tipo');
                $folio->destino=$request->get('destino');
                $folio->proposito=$request->get('proposito');
                $folio->eq_computo=$request->get('eq_computo');
                $folio->fecha_salida=$request->get('fecha_salida');
                $folio->fecha_llegada=$request->get('fecha_llegada');
                $folio->dias=$diff->days + 1;
                $folio->criterio=$request->get('criterio');
                $folio->id_moneda=$request->get('id_moneda');
                $folio->anticipo=$request->get('anticipo');
                $folio->update();

                DB::commit();

            }catch(\Exception $e)
            {
                DB::rollback();
            }

            return Redirect::to('travel/solicitud');
            
        }else
        {   
            Session()->flash('msg','Lo sentimos, pero actualmente cuentas con Solicitudes de Viajes activas en las fechas seleccionadas.');

            return Redirect::to("travel/solicitud/".$folio->id_header_folio."/edit")->withInput();
        }

    }

}
