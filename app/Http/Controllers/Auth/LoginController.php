<?php

namespace sisViaticos\Http\Controllers\Auth;

use Auth;
use Illuminate\Http\Request;
use sisViaticos\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use sisViaticos\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use DB;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    // use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');

    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login()
    {
    //Condicional donde Revisa si lo seleccionado es Email o Numero de Nomina
        $NumEmail = request('EmNom');

        if($NumEmail == "NumNom"){
            $BranchRH = request('Branchid');
            $user = request('numeroNom');
            $pass = request('password');
            //die($BranchRH);
            
            $credentials = $this->validate(request(),[
                'numeroNom'=>'required',
                'password'=>'required|string'

            ]);

            //Se realiza la busqueda en SQL que exista el usuario y clave en la base de datos de Serlam2
            $userNominas = DB::select('EXEC Sp_Usuarios_Nomina ?, ?, ?', Array($user, $pass, $BranchRH ));

            //Condicional donde revisa que existe el usuario 
            if($userNominas){

                //Se realiza la busqueda si el usuario existe en la base de datos de viaticos, para ver si agregar o modificar la clave
                $userAsios = User::where('numeroNom','=',$userNominas[0]->Usuario)
                ->where('company','=',$BranchRH)
                ->first();

                // die($userAsios->name);
                //Se realiza la busqueda para el guardado de datos en la tabla users (Busqueda de RH7)
                if(!$userAsios)
                {
                    $company = substr($BranchRH,0,1);                
                    $valuesUser = $company.$userNominas[0]->Usuario;
    
                    $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

                    $dataToBeSaved = [
                        'numeroNom'=>$userNominas[0]->Usuario,
                        'name' => $userClaims[0]->NombreCompleto,
                        'email' => $userClaims[0]->Email,
                        'password' => bcrypt(request('password')),
                        'company'=>$BranchRH,
                        'numeroNomActual' => $userClaims[0]->TrabajadorID
                    ];
        
                    
                    $userSave = User::create($dataToBeSaved);

                    $credentials = $this->validate(request(),[
                        'numeroNom'=>'required',
                        'password'=>'required|string'
        
                    ]);

                    if(Auth::attempt(['numeroNomActual' => $userClaims[0]->TrabajadorID, 'password' => $pass]))
                    {
                        //return Redirect::to("/home");
                        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

                        return Redirect('/home');
                    }

                   
                }
                else{
                    //die($userAsios->name);
                    $company = substr($BranchRH,0,1);                
                    $valuesUser = $company.$userNominas[0]->Usuario;

                    //die ($valuesUser);
                    $userClaims = DB::select("EXEC Sp_Get_RH7_Info_Users ?", Array($valuesUser) );

                    //die($userClaims['0']->TrabajadorID);
                    if(Hash::check(request('password'), $userAsios["password"]))
                    {
                        if(Auth::attempt(['numeroNomActual' => $userClaims[0]->TrabajadorID, 'password' => $pass]))
                        {
                        //return Redirect::to("/home");
                        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

                        return Redirect('/home');
                    }
                        return 'Failure';
                    }
                    else{

                        
                        $userAsios->password = bcrypt(request('password'));
                        $userAsios->update();

                        if(Auth::attempt(['numeroNomActual' => $userClaims[0]->TrabajadorID, 'password' => $pass]))
                        {
                            $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));
                            //return Redirect::to("/home");
                            return Redirect('/home');
                        }
                        // return 'Failure';
                    }
                }
                //die($userNominas[0]->Usuario. ' '. $userNominas[0]->Password);
            }
            else{
                return back()->withErrors(['email'=>trans('auth.failed')])->withInput(request(['numeroNom']));
            }           
        }
        else if($NumEmail == "email"){
            $credentials = $this->validate(request(),[
                'email'=>'required|string',
                'password'=>'required|string'

            ]);
            
            $user = request('email');
            $pass = request('password');

            $ldap = base64_decode('QDRkbTFuU0w=');
            $ldap_username = 'qrosi01@in-servilamina.com.mx';
            $ldap_connection = ldap_connect('in-servilamina.com.mx');
            
            if (FALSE === $ldap_connection){
                die('Unable to connect to the ldap server');
            }

            ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3) or die('Unable to set LDAP protocol version');
            ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0);
            
            if (TRUE === ldap_bind($ldap_connection, $ldap_username, $ldap)){
            
                $ldap_base_dn = 'DC=in-servilamina,DC=com,DC=mx';
                $search_filter = '(&(objectCategory=person)(objectClass=user)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))';
            
                $result = ldap_search($ldap_connection, $ldap_base_dn, $search_filter);
            
                if (FALSE !== $result){
                    $entries = ldap_get_entries($ldap_connection, $result);
                
                    $users = array();
                
                    for ($x=0; $x<$entries['count']; $x++){
                    
                        $LDAP_samaccountname = "";
                        if (!empty($entries[$x]['samaccountname'][0])) {
                            $LDAP_samaccountname = $entries[$x]['samaccountname'][0];
                            if ($LDAP_samaccountname == "NULL"){
                                $LDAP_samaccountname= "";
                            }
                        } 
                        else {
                            $LDAP_uSNCreated = $entries[$x]['usncreated'][0];
                            $LDAP_samaccountname= "CONTACT_" . $LDAP_uSNCreated;
                        }
                    
                        $LDAP_InternetAddress = "";
                        if (!empty($entries[$x]['mail'][0])) {
                            $LDAP_InternetAddress = $entries[$x]['mail'][0];	
                            if ($LDAP_InternetAddress == "NULL"){
                                $LDAP_InternetAddress = "";
                            }
                        }
                    
                        $n_user =  array (
                            'user' => $LDAP_samaccountname,
                            'email' => $LDAP_InternetAddress
                        );
                    
                        $users[$x] = $n_user;
                    } 
                }
                ldap_unbind($ldap_connection);  
            }

            foreach($users as $userA){
                if($userA['email'] == $user){
                    $user = $userA['user'];
                }
            }

            $ldaprdn = trim($user).'@in-servilamina.com.mx'; 
            $ldappass = trim($pass); 
            $ds = 'in-servilamina.com.mx'; 
            $dn = 'dc=in-servilamina,dc=com,dc=mx';  
            $puertoldap = 389; 
            $ldapconn = ldap_connect($ds,$puertoldap);
            ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION,3); 
            ldap_set_option($ldapconn, LDAP_OPT_REFERRALS,0); 
            $ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass); 
            if ($ldapbind){
                $filter="(|(SAMAccountName=".trim($user)."))";
                $fields = array("SAMAccountName","mail","telephonenumber","department","initials","physicaldeliveryofficename","givenname","sn"); 
                $sr = @ldap_search($ldapconn, $dn, $filter, $fields); 
                $info = @ldap_get_entries($ldapconn, $sr); 
                //$array = $info[0]["samaccountname"][0];
                $array = array($info[0]["samaccountname"][0], $info[0]["mail"][0], $info[0]["telephonenumber"][0], $info[0]["department"][0], $info[0]["initials"][0], $info[0]["physicaldeliveryofficename"][0], $info[0]["givenname"][0], $info[0]["sn"][0]);
            }
            else{ 
                $array=0;
            } 
            ldap_close($ldapconn); 
            // return $array;

            if ($array) {

                $fieldType = filter_var(request('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'user';

                //Revision de los datos que contiene el array 
                // die(' SAMAccountName: '. $array[0]. ' mail: '. $array[1]. ' telephonenumber: '. $array[2]. ' department: '. $array[3]. ' initials: '. $array[4]. ' physicaldeliveryofficename: '. $array[5]. ' givenname: '. $array[6]. ' sn: '. $array[7]);

                $userAsios = User::where('email','=',$array[1])->first();
                
                if(!$userAsios)
                {
                    //die($array[1]);
                    $company = substr($array[5],0,1);

                    $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($company.$array[4]));

                    //die($company.$array[4]);

                    $dataToBeSaved = [
                        'numeroNom'=>$array[4],
                        'name' => $userClaims[0]->NombreCompleto,
                        'email' => request('email'),
                        'password' => bcrypt(request('password')),
                        'company'=>$array[5],
                        'numeroNomActual' => $userClaims[0]->TrabajadorID
                    ];
        
                    $userSave = User::create($dataToBeSaved);

                    if(Auth::attempt($credentials))
                    {
                        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($company.$array[4]));
                        //return Redirect::to("/home");
                        return Redirect('/home');
                    }
                }
                else{
                    //die($userAsios->name);
                    $company = substr($userAsios["company"],0,1);                
                    $valuesUser = $company.$userAsios["numeroNom"];

                    //die ($valuesUser);
                    $userClaims = DB::select("EXEC Sp_Get_RH7_Info_Users ?", Array($company.$array[4]) );

                    //die($userClaims['0']->TrabajadorID);
                    if(Hash::check(request('password'), $userAsios["password"]))
                    {
                        if(Auth::attempt($credentials))
                        {
                            $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($company.$array[4]));
                            //return Redirect::to("/home");
                            return Redirect('/home');
                        }
                    }
                    else{
                        $userAsios->password = bcrypt(request('password'));
                        $userAsios->update();

                        if(Auth::attempt($credentials))
                        {
                            $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($company.$array[4]));
                            //return Redirect::to("/home");
                            return Redirect('/home');
                        }
                    }
                }
            }
            else{
                return back()->withErrors(['email'=>trans('auth.failed')])->withInput(request(['email']));
            }
        }
    }

    public function logout(Request $request) 
    {
        Auth::logout();
        return redirect('/login');
    }

    public function logintest(Request $request)
    {
    //Condicional donde Revisa si lo seleccionado es Email o Numero de Nomina
        $NumEmail = request('EmNom');

        if($NumEmail == "NumNom"){
            $BranchRH = request('Branchid');
            $user = request('numeroNom');
            $pass = request('password');
            //die($BranchRH);
            
            $credentials = $this->validate(request(),[
                'numeroNom'=>'required',
                'password'=>'required|string'

            ]);

            //Se realiza la busqueda en SQL que exista el usuario y clave en la base de datos de Serlam2
            $userNominas = DB::select('EXEC Sp_Usuarios_Nomina ?, ?, ?', Array($user, $pass, $BranchRH ));

            //Condicional donde revisa que existe el usuario 
            if($userNominas){

                //Se realiza la busqueda si el usuario existe en la base de datos de viaticos, para ver si agregar o modificar la clave
                $userAsios = User::where('numeroNom','=',$userNominas[0]->Usuario)
                ->where('company','=',$BranchRH)
                ->first();

                // die($userAsios->name);
                //Se realiza la busqueda para el guardado de datos en la tabla users (Busqueda de RH7)
                if(!$userAsios)
                {
                    $company = substr($BranchRH,0,1);                
                    $valuesUser = $company.$userNominas[0]->Usuario;
    
                    $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

                    $dataToBeSaved = [
                        'numeroNom'=>$userNominas[0]->Usuario,
                        'name' => $userClaims[0]->NombreCompleto,
                        'email' => $userClaims[0]->Email,
                        'password' => bcrypt(request('password')),
                        'company'=>$BranchRH
                    ];
        
                    $userSave = User::create($dataToBeSaved);

                    if(Auth::attempt($credentials))
                    {
                        //return Redirect::to("/home");
                        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

                        return Redirect('/home');
                    }

                   
                }
                else{
                    //die($userAsios->name);
                    $company = substr($BranchRH,0,1);                
                    $valuesUser = $company.$userNominas[0]->Usuario;

                    //die ($valuesUser);
                    $userClaims = DB::select("EXEC Sp_Get_RH7_Info_Users ?", Array($valuesUser) );

                    //die($userClaims['0']->TrabajadorID);
                    if(Hash::check(request('password'), $userAsios["password"]))
                    {
                        if(Auth::attempt($credentials))
                        {
                            $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));
                            //return Redirect::to("/home");
                            return Redirect('/home');
                        }
                    }
                    else{

                        
                        $userAsios->password = bcrypt(request('password'));
                        $userAsios->update();

                        if(Auth::attempt($credentials))
                        {
                            $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));
                            //return Redirect::to("/home");
                            return Redirect('/home');
                        }
                    }
                }
                //die($userNominas[0]->Usuario. ' '. $userNominas[0]->Password);
            }
            else{
                return back()->withErrors(['email'=>trans('auth.failed')])->withInput(request(['numeroNom']));
            }           
        }
        else if($NumEmail == "email"){
            $credentials = $this->validate(request(),[
                'email'=>'required|string',
                'password'=>'required|string'

            ]);
            
            $user = request('email');
            $pass = request('password');

            $ldap = base64_decode('QDRkbTFuU0w=');
            $ldap_username = 'qrosi01@in-servilamina.com.mx';
            $ldap_connection = ldap_connect('in-servilamina.com.mx');
            
            if (FALSE === $ldap_connection){
                die('Unable to connect to the ldap server');
            }

            ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3) or die('Unable to set LDAP protocol version');
            ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0);
            
            if (TRUE === ldap_bind($ldap_connection, $ldap_username, $ldap)){
            
                $ldap_base_dn = 'DC=in-servilamina,DC=com,DC=mx';
                $search_filter = '(&(objectCategory=person)(objectClass=user)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))';
            
                $result = ldap_search($ldap_connection, $ldap_base_dn, $search_filter);
            
                if (FALSE !== $result){
                    $entries = ldap_get_entries($ldap_connection, $result);
                
                    $users = array();
                
                    for ($x=0; $x<$entries['count']; $x++){
                    
                        $LDAP_samaccountname = "";
                        if (!empty($entries[$x]['samaccountname'][0])) {
                            $LDAP_samaccountname = $entries[$x]['samaccountname'][0];
                            if ($LDAP_samaccountname == "NULL"){
                                $LDAP_samaccountname= "";
                            }
                        } 
                        else {
                            $LDAP_uSNCreated = $entries[$x]['usncreated'][0];
                            $LDAP_samaccountname= "CONTACT_" . $LDAP_uSNCreated;
                        }
                    
                        $LDAP_InternetAddress = "";
                        if (!empty($entries[$x]['mail'][0])) {
                            $LDAP_InternetAddress = $entries[$x]['mail'][0];	
                            if ($LDAP_InternetAddress == "NULL"){
                                $LDAP_InternetAddress = "";
                            }
                        }
                    
                        $n_user =  array (
                            'user' => $LDAP_samaccountname,
                            'email' => $LDAP_InternetAddress
                        );
                    
                        $users[$x] = $n_user;
                    } 
                }
                ldap_unbind($ldap_connection);  
            }

            foreach($users as $userA){
                if($userA['email'] == $user){
                    $user = $userA['user'];
                }
            }

            $ldaprdn = trim($user).'@in-servilamina.com.mx'; 
            $ldappass = trim($pass); 
            $ds = 'in-servilamina.com.mx'; 
            $dn = 'dc=in-servilamina,dc=com,dc=mx';  
            $puertoldap = 389; 
            $ldapconn = ldap_connect($ds,$puertoldap);
            ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION,3); 
            ldap_set_option($ldapconn, LDAP_OPT_REFERRALS,0); 
            $ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass); 
            if ($ldapbind){
                $filter="(|(SAMAccountName=".trim($user)."))";
                $fields = array("SAMAccountName","mail","telephonenumber","department","initials","physicaldeliveryofficename","givenname","sn"); 
                $sr = @ldap_search($ldapconn, $dn, $filter, $fields); 
                $info = @ldap_get_entries($ldapconn, $sr); 
                //$array = $info[0]["samaccountname"][0];
                $array = array($info[0]["samaccountname"][0], $info[0]["mail"][0], $info[0]["telephonenumber"][0], $info[0]["department"][0], $info[0]["initials"][0], $info[0]["physicaldeliveryofficename"][0], $info[0]["givenname"][0], $info[0]["sn"][0]);
            }
            else{ 
                $array=0;
            } 
            ldap_close($ldapconn); 
            // return $array;

            if ($array) {

                $fieldType = filter_var(request('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'user';

                //Revision de los datos que contiene el array 
                // die(' SAMAccountName: '. $array[0]. ' mail: '. $array[1]. ' telephonenumber: '. $array[2]. ' department: '. $array[3]. ' initials: '. $array[4]. ' physicaldeliveryofficename: '. $array[5]. ' givenname: '. $array[6]. ' sn: '. $array[7]);

                $userAsios = User::where('email','=',$array[1])->first();
                
                if(!$userAsios)
                {
                    //die($array[1]);
                    $company = substr($array[5],0,1);

                    $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($company.$array[4]));

                    //die($company.$array[4]);

                    $dataToBeSaved = [
                        'numeroNom'=>$array[4],
                        'name' => $userClaims[0]->NombreCompleto,
                        'email' => request('email'),
                        'password' => bcrypt(request('password')),
                        'company'=>$array[5]
                    ];
        
                    $userSave = User::create($dataToBeSaved);

                    if(Auth::attempt($credentials))
                    {
                        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));
                        //return Redirect::to("/home");
                        return Redirect('/home');
                    }
                }
                else{
                    //die($userAsios->name);
                    $company = substr($userAsios["company"],0,1);                
                    $valuesUser = $company.$userAsios["numeroNom"];

                    //die ($valuesUser);
                    $userClaims = DB::select("EXEC Sp_Get_RH7_Info_Users ?", Array($valuesUser) );

                    //die($userClaims['0']->TrabajadorID);
                    if(Hash::check(request('password'), $userAsios["password"]))
                    {
                        if(Auth::attempt($credentials))
                        {
                            $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));
                            //return Redirect::to("/home");
                            return Redirect('/home');
                        }
                    }
                    else{
                        $userAsios->password = bcrypt(request('password'));
                        $userAsios->update();

                        if(Auth::attempt($credentials))
                        {
                            $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));
                            //return Redirect::to("/home");
                            return Redirect('/home');
                        }
                    }
                }
            }
            else{
                return back()->withErrors(['email'=>trans('auth.failed')])->withInput(request(['email']));
            }
        }
    }
}
