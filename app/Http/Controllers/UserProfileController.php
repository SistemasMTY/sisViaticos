<?php

namespace sisViaticos\Http\Controllers;

use Illuminate\Http\Request;

use sisViaticos\Http\Requests;
use sisViaticos\User;
use sisViaticos\UserProfile;
use sisViaticos\Area;
use sisViaticos\Departamento;
use sisViaticos\Banco;
use Illuminate\Support\Facades\Redirect;
use sisViaticos\Http\Requests\UsuarioFormRequest;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Response;

class UserProfileController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show($id)
    {   

        $profiles=DB::select("EXEC SP_get_users_info  ?, ?", Array ($id, Auth::user()->company));
        $autorizadores=DB::select("EXEC SP_get_authorizers  ?, ?", Array ($id, Auth::user()->company));
        //Mostrar los autorizadores actuales, con respecto a lo que aparece en el RH7 y la informacion del usuario
        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;

        $company = substr($branch,0,1);                
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

        return view("seguridad/profile/show",["userClaims"=>$userClaims,"profiles"=>$profiles, "autorizadores"=>$autorizadores]);
    }
    
    //public function edit($id)
    //{	
    	
    	// $articulo=Articulo::findOrFail($id);
    	// $categorias=DB::table('categoria')->where('condicion','=','1')->get();
    	//$areas=DB::table('ssm_viat_area')->where('status','=','1')->get();
    	//$departamentos=DB::table('ssm_viat_depto')->where('status','=','1')->get();
        //$bancos=DB::table('ssm_viat_banco')->where('status','=','1')->get();
    	//$profile=DB::table('user_profile')->where('id_user','=', $id)->first();
    	//$gerentes=DB::table('ssm_viat_autorizadores')->where('lvl','=','3')->get();
    	//$gerentesGral=DB::table('ssm_viat_autorizadores')->where('lvl','=','2')->get();
    	//$directores=DB::table('ssm_viat_autorizadores')->where('lvl','=','1')->get();
    	//return $profile;

    	//return view("seguridad/profile/edit",["areas"=>$areas,"departamentos"=>$departamentos,"bancos"=>$bancos,"profile"=>$profile,"gerentes"=>$gerentes,"gerentesGral"=>$gerentesGral,"directores"=>$directores]);
       
        // return view("almacen.articulo.edit",["articulo"=>$articulo,"categorias"=>$categorias]);

    //}

    //public function update(Request $request, $id)
    //{
        
        //$mytime=Carbon::now('America/Monterrey')->toDateTimeString();

    	//$profile=UserProfile::where('id_user','=', $id)->first();
        //$profile->id_area=$request->get('area');
        //$profile->id_depto=$request->get('departamento');
        //$profile->id_gerente=$request->get('gerente');
        //$profile->id_gerenteGral=$request->get('gerenteGral');
        //$profile->id_directorGral=$request->get('director');
        //$profile->banco=$request->get('banco');
        //$profile->cuenta=$request->get('cuenta');
        //$profile->numeroNom=$request->get('NumNomina');
        //$profile->TimeStampMod=$mytime;
        //$profile->update();

        //return Redirect::to('/');
    	
        //$articulo=Articulo::findOrFail($id);
        //$articulo->idcategoria=$request->get('idcategoria');
        //$articulo->codigo=$request->get('codigo');
        //$articulo->nombre=$request->get('nombre');
        //$articulo->stock=$request->get('stock');
        //$articulo->descripcion=$request->get('descripcion');

        //if ($request->hasfile('imagen')) {
        	# code...
       // 	$file=$request->file('imagen');
       // 	$file->move(public_path().'/imagenes/articulos/',$file->getClientOriginalName());
       // 	$articulo->imagen=$file->getClientOriginalName();
        //}
        //$articulo->update();
        //return Redirect::to('almacen/articulo');
    //}
   
}
