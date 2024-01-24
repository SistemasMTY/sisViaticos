<?php

namespace sisViaticos\Http\Controllers;

use Illuminate\Http\Request;

use sisViaticos\Http\Requests;
use sisViaticos\User;
use sisViaticos\UserProfile;
use Illuminate\Support\Facades\Redirect;
use sisViaticos\Http\Requests\UsuarioFormRequest;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;

class UsuarioController extends Controller
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

            if(Auth::user()->id==1)
            {
                $query=trim($request->get('searchText'));
                $usuarios=DB::table('users')
                ->where('name','LIKE','%'.$query.'%')
                ->orderBy('id','desc')
                ->paginate(7);
                
            }
            else
            {
                $query=trim($request->get('searchText'));
                $usuarios=DB::table('users')
                ->where('name','LIKE','%'.$query.'%')
                ->where('company','=',Auth::user()->company)
                ->orderBy('id','desc')
                ->paginate(7);

            }

            $usernom = Auth::user()->numeroNom;
            $branch = Auth::user()->company;
    
            $company = substr($branch,0,1);                
            $valuesUser = $company.$usernom;
    
            $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));
        }

        return view('seguridad.usuario.index',["userClaims"=>$userClaims,"usuarios"=>$usuarios,"searchText"=>$query]);
    }

    public function create()
    {
        $usuarios=DB::table('VIEW_SSM_GET_NAME_USERS')
        ->where('compania','=',Auth::user()->company)
        ->get();

        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;

        $company = substr($branch,0,1);                
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));
        
        return view("seguridad.usuario.create",["userClaims"=>$userClaims,"usuarios"=>$usuarios]);
    }

     public function store (UsuarioFormRequest $request)
    {
        $usuarios=DB::table('VIEW_SSM_GET_NAME_USERS')
        ->where('NombreCompleto','=',$request->get('name'))
        ->first();

        $usuario = User::create([
            'numeroNom'=>$usuarios->TrabajadorID,
            'name'=>$request->get('name'), 
            'email'=>$request->get('email'),
            'password'=> bcrypt($request->get('password')),
            'company'=>Auth::user()->company]
        );
        
        $mytime=Carbon::now('America/Monterrey')->toDateTimeString();

        //$profile=new UserProfile;
        //$profile->id_user=$usuario->id;
        //$profile->usuarioAlta=Auth::user()->id;
        //$profile->TimeStampAlta=$mytime;
        //$profile->BRH=$request->get('branch');
        //$profile->save();

        //$usuario=new User;
        //$usuario->name=$request->get('name');
        //$usuario->email=$request->get('email');
        //$usuario->password=bcrypt($request->get('password'));
        //$usuario->save();
        

        // $profile=UserProfile::where('nombre','=',$request->get('name'))
        // ->first();
        // $profile->id_user=$usuario->id;
        // $profile->company=$usuario->company;
        // $profile->usuarioUpdate=Auth::user()->id;
        // $profile->TimeStampAlta=$mytime;
        // $profile->TimeStampMod=$mytime;
        // $profile->save();

        return Redirect::to('seguridad/usuario');


    }

    public function show($id)
    {
        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;

        $company = substr($branch,0,1);                
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

        return view("seguridad.usuario.show",["userClaims"=>$userClaims, "usuario"=>User::findOrFail($id)]);
    }

    public function edit($id)
    {
        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;

        $company = substr($branch,0,1);                
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

        return view("seguridad.usuario.edit",["userClaims"=>$userClaims, "usuario"=>User::findOrFail($id)]);
    }

    public function update(UsuarioFormRequest $request,$id)
    {
        $usuario=User::findOrFail($id);
        $usuario->name=$request->get('name');
        $usuario->email=$request->get('email');
        $usuario->password=bcrypt($request->get('password'));
        $usuario->update();

        $mytime=Carbon::now('America/Monterrey')->toDateTimeString();
        // $profile=UserProfile::where('id_user','=',$id)
        // ->first();
        // if(!empty($profile)){
        //     $profile->usuarioUpdate=Auth::user()->id;
        //     $profile->TimeStampMod=$mytime;
        //     $profile->save();
        // }

        return Redirect::to('seguridad/usuario');
    }

     public function destroy($id)
    {
        $usuario=DB::table('users')->where('id','=',$id)->delete();

        $mytime=Carbon::now('America/Monterrey')->toDateTimeString();

        // $profile=UserProfile::where('id_user','=',$id)
        // ->first();
        // if(!empty($profile)){
        //     $profile->id_user='0';
        //     $profile->usuarioUpdate=Auth::user()->id;
        //     $profile->TimeStampMod=$mytime;
        //     $profile->save();
        // }
        return Redirect::to('seguridad/usuario');
    }


}
