<?php

namespace sisViaticos\Http\Controllers;

use Illuminate\Http\Request;
use sisViaticos\Folio;
use sisViaticos\FirmaFolio;
use sisViaticos\FirmaAnticipo;
use sisViaticos\FirmaGasto;
use sisViaticos\Transfer;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use Mail;

use DB;
use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;

class ReviewController extends Controller
{
    //
    public function __construct()
    {

    }
    
    public function sendAuto($id ,$token)
    {
        $folio=Folio::findOrFail($id);

        if ($folio->id_status<'4') 
        {   
            $folio->id_status='2';
            $folio->update();

            $mytime = Carbon::now('America/Monterrey');

            // $firma = new FirmaFolio;
            // $firma->company=Auth::user()->company;
            // $firma->id_header_folio=$folio->id_header_folio;
            // $firma->id_user=Auth::user()->id;
            // $firma->anticipo=$mytime;
            // $firma->save();

            $firmaUser=FirmaAnticipo::where('id_header_folio','=',$folio->id_header_folio)
            ->where('id_user','=',Auth::user()->id)
            ->first();
            $firmaUser->status='1';
            $firmaUser->anticipo=$mytime;
            $firmaUser->update();

            $this->sendMailAuto1($id, $token);

            return Redirect::to('travel/solicitud');

        }

        //Envia la solicitud cuando esta a sido denagada por Auto2
        if ($folio->id_status=='5')
        {
            $this->sendMailAuto2($id, $token);

            return Redirect::to('travel/solicitud');
        }

    }

    public function getAuto1($token, $id, $option)
    {
        $folio=Folio::where('_token','=',$token)
        ->where('id_header_folio','=', $id)
        ->first();

        if ($folio->id_status<'4')
        {   
            $Auto1=$this->Auto1($id, $token);
            $Auto2=$this->Auto2($id, $token);
        
            if($option=='si')
            {   
                $folio->id_status='4';
                $folio->update();
                //return [$Auto1, $Auto2];
                $mytime = Carbon::now('America/Monterrey');

                $firmaAuto1=FirmaAnticipo::where('id_header_folio','=',$folio->id_header_folio)
                ->where('id_autorizador','>','0')
                ->where('status','=','0')
                ->first();
                $firmaAuto1->status='1';
                $firmaAuto1->anticipo=$mytime;
                $firmaAuto1->update();

                if ($Auto1->TrabajadorID==$Auto2->TrabajadorID) 
                {
                       
                    $folio->id_status='8';
                    $folio->update();

                    // $this->Treasury($id, $token,$Auto2->id_autorizador);

                    $this->sendMailStaff($id, $token, $Auto2->TrabajadorID);

                    return Redirect::to('mails/optionSi');                    
                }
                else
                {
                    $this->sendMailAuto2($id, $token);
                    $this->replyRequestApprobSi($id, $token, $Auto1->TrabajadorID);

                    return Redirect::to('mails/optionSi');
                }
                
            }
            elseif($option=='no')
            {
                if ($folio->id_status<'3') 
                {
                    $folio->id_status='3';
                    $folio->update();

                    $this->replyRequestApprobNo($id, $token, $Auto1->TrabajadorID);

                    return Redirect::to('mails/optionNo');
                }
                else
                {
                   return Redirect::to('mails/optionFail'); 
                }
            }            
        }
        else
        {
            return Redirect::to('mails/optionFail');
        }
    }

    public function getAuto2($token, $id, $option)
    {   
        $folio=Folio::where('_token','=',$token)
        ->where('id_header_folio','=', $id)
        ->first();

        if ($folio->id_status<'6') 
        {   
            $Auto1=$this->Auto1($id, $token);
            $Auto2=$this->Auto2($id, $token);

            if ($option=='si')
            {
                $folio->id_status='8';
                $folio->update();

                $mytime = Carbon::now('America/Monterrey');

                $firmaAuto2=FirmaAnticipo::where('id_header_folio','=',$folio->id_header_folio)
                ->where('id_autorizador','>','0')
                ->where('status','=','0')
                ->first();
                $firmaAuto2->status='1';
                $firmaAuto2->anticipo=$mytime;
                $firmaAuto2->update();

                $this->sendMailStaff($id, $token, $Auto2->TrabajadorID);

                return Redirect::to('mails/optionSi');
            }
            elseif($option=='no')
            {
                if ($folio->id_status<'5')
                {
                    $folio->id_status='5';
                    $folio->update();

                    $folioMail=DB::table('ssm_viat_header_folio as f')
                    ->join('users as u','f.id_solicitante','=','u.id')
                    ->join('VIEW_SSM_INFO_USERS as p','u.numeroNom','=','p.TrabajadorID')
                    ->join('VIEW_SSM_GET_AUTHORIZERS as a','a.TrabajadorID','=',DB::raw($Auto2->TrabajadorID))
                    ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
                    ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f._token','u.name','u.company','f.correo_solicitante as emailU','p.BancoCuenta','p.CLABE','p.Banco','a.TrabajadorID','a.NombreAuto','a.email as emailA','m.moneda')
                    ->where('f.id_header_folio','=',$id)
                    ->where('p.compania','LIKE','%'.$folio->company.'%')
                    ->where('u.name', 'LIKE', '%', 'p.NombreCompleto','%')
                    ->first();

                    Mail::Send('mails.replyRequestApprobNo', ['folioMail'=> $folioMail], function($mail) use($folioMail, $Auto1){
                            $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                            $mail->to($folioMail->emailU, $folioMail->name)
                            ->cc($Auto1->email);
                    });   

                    return Redirect::to('mails/optionNo');
                }
                else
                {
                    return Redirect::to('mails/optionFail');
                }
            }
        }
        else
        {
            return Redirect::to('mails/optionFail');
        }
    }

    public function sendMailAuto1($id ,$token)
    {
        //Busqueda con el stored procedure de los id de autorizador por el correo
        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        //->join('user_profile as p','u.numeroNom','=','p.numeroNom')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.anticipo','m.moneda','u.name','f.correo_solicitante as emailU','u.company','f.correo_auto1','f._token')
        ->where('f.id_header_folio','=',$id)
        ->where('_token','=',$token)
        ->first();


        $idauto=DB::table('VIEW_SSM_GET_AUTHORIZERS')
        ->select('TrabajadorID')
        ->where('email','=',$folioMail->correo_auto1)
        ->first();

        $firma=DB::table('ssm_viat_firma_anticipo')
        ->select(DB::raw('count( id_anticipo ) as anticipo'))
        ->where('id_header_folio','=',$id)
        ->where('id_autorizador','=',$idauto->TrabajadorID)
        ->first();

        if ($firma->anticipo == 0)
         {
            $folioMai=DB::table('ssm_viat_header_folio as f')
            ->join('users as u','f.id_solicitante','=','u.id')
            // ->join('user_profile as p','f.id_solicitante','=','p.numeroNom')
            ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
            ->select('f.id_header_folio','f.fecha','f.tipo','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.anticipo', 'm.moneda','u.name','f.correo_solicitante as emailU','u.company','f._token')
            ->where('f.id_header_folio','=',$id)
            ->where('_token','=',$token)
            ->first();
    
            $firmaAuto = new FirmaAnticipo;
            $firmaAuto->company=Auth::user()->company;
            $firmaAuto->id_autorizador=$idauto->TrabajadorID;
            $firmaAuto->id_header_folio=$folioMail->id_header_folio;
            $firmaAuto->status='0';
            $firmaAuto->save();
        }
        else{

        }

        Mail::Send('mails.requestApprobAuto1', ['folioMail'=> $folioMail], function($mail) use($folioMail){
            $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
            $mail->to($folioMail->correo_auto1);
        });

        if (Mail::failures()) 
        {
        // return response showing failed emails
            dd(Mail::failures());
        }
         //dd(Mail::failures());
    }

    public function sendMailAuto2($id ,$token)
    {
        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        //->join('user_profile as p','u.numeroNom','=','p.numeroNom')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.anticipo','m.moneda','u.name','f.correo_solicitante as emailU','u.company','f.correo_auto2','f._token')
        ->where('f.id_header_folio','=',$id)
        ->where('_token','=',$token)
        ->first();

        $idauto=DB::table('VIEW_SSM_GET_AUTHORIZERS')
        ->select('TrabajadorID')
        ->where('email','=',$folioMail->correo_auto2)
        ->first();

        $firma=DB::table('ssm_viat_firma_anticipo')
        ->select(DB::raw('count( id_anticipo ) as anticipo'))
        ->where('id_header_folio','=',$id)
        ->where('id_autorizador','=',$idauto->TrabajadorID)
        ->first();

        if ($firma->anticipo == 0)
        {
            $folioMai=DB::table('ssm_viat_header_folio as f')
            ->join('users as u','f.id_solicitante','=','u.id')
            // ->join('user_profile as p','f.id_solicitante','=','p.numeroNom')
            ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
            ->select('f.id_header_folio','f.fecha','f.tipo','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.anticipo', 'm.moneda','u.name','f.correo_solicitante as emailU','u.company','f._token')
            ->where('f.id_header_folio','=',$id)
            ->where('_token','=',$token)
            ->first();

            $firmaAuto = new FirmaAnticipo;
            $firmaAuto->company=$folioMail->company;
            $firmaAuto->id_autorizador=$idauto->TrabajadorID;
            $firmaAuto->id_header_folio=$folioMail->id_header_folio;
            $firmaAuto->status='0';
            $firmaAuto->save();
        }
        else{

    }
        Mail::Send('mails.requestApprobAuto2', ['folioMail'=> $folioMail], function($mail) use($folioMail){
            $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
            $mail->to($folioMail->correo_auto2);
        });

        if (Mail::failures()) 
        {
        // return response showing failed emails
            dd(Mail::failures());
        }
         //dd(Mail::failures());
    }

    public function Treasury($id ,$token, $id_auto)
    {          
        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('user_profile as p','f.id_solicitante','=','p.id_user')
        ->join('ssm_viat_autorizadores as a','a.id_autorizador','=',DB::raw($id_auto))
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f._token','u.id','u.name','u.company','u.email as emailU','p.cuenta','p.clabe','p.banco','a.id_autorizador','a.autorizador','a.autorizador_email as emailA','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('f._token','=',$token)
        ->first();

        // $trans = new Transfer;
        // $trans->company=$folioMail->company;
        // $trans->id_header_folio=$folioMail->id_header_folio;
        // $trans->deposito='2';
        // $trans->save();

    }

    public function sendMailStaff($id ,$token, $TrabajadorID)
    {
        
        $folio=Folio::where('_token','=',$token)
        ->where('id_header_folio','=', $id)
        ->first();

        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_INFO_USERS as p','u.numeroNom','=','p.TrabajadorID')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','a.TrabajadorID','=',DB::raw($TrabajadorID))
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f._token','u.id','u.name','u.company','f.correo_solicitante as emailU','p.BancoCuenta','p.CLABE','p.Banco','a.TrabajadorID','a.NombreAuto','a.email as emailA','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('p.compania','LIKE','%'.$folio->company.'%')
        ->first();

        $foliosPend=DB::table('ssm_viat_header_folio as f')
        ->where('company','=',$folioMail->company)
        ->whereBetween('id_status',[1,15])
        ->where('id_solicitante','=',$folioMail->id)
        ->where('id_header_folio','!=',$folioMail->id_header_folio)
        ->where('fecha_llegada','<',$folioMail->fecha_salida)
        ->where('anticipo','>','0')
        ->get();

        $firmaUser = new FirmaGasto;

        $firmaUser->company=$folioMail->company;
        $firmaUser->id_user=$folioMail->id;
        $firmaUser->id_header_folio=$folioMail->id_header_folio;
        $firmaUser->status='0';
        $firmaUser->save();

        // dd($foliosPend);
        //////////------AVISO DE ANTICIPO TESORERIA ----//////////////
        $data3['nameAdvT'] = 'GERARDO CASTRO';
        $data3['emailAdvT'] = 'gerardo.castro@summitmx.com';
        
        $data3['CCemailAdvT'] = 'coral.mederos@summitmx.com';
               
        /////////---------CORREO A RH MTY----------///////////////////
        $data3['MTYnameHR'] = 'RECURSOS HUMANOS MTY';
        $data3['MTYemailHR'] = 'wendy.garza@summitmx.com';

        $data3['CC1MTYemailHR'] = 'juan.barron@summitmx.com';
        
        $data3['CC2MTYemailHR'] = 'mty.vigilancia@summitmx.com';

        ///////--------RESERVACION DE VUELOS Y HOTEL MTY-----////////////
        $data3['MTYnameBuyF'] = 'RESERVACIONES Y VUELOS MTY';
        $data3['MTYemailBuyF'] = 'wendy.garza@summitmx.com';
        
        $data3['CCMTYemailBuyF'] = 'mildred.asueta@summitmx.com';

        /////////---------CORREO A RH QRO----------///////////////////
        $data3['QROnameHR'] = 'RECURSOS HUMANOS QRO';
        $data3['QROemailHR'] = 'juan.hernandez@summitmx.com';

        $data3['CC1QROemailHR'] = 'francisco.peguero@summitmx.com';
        
        $data3['CC2QROemailHR'] = 'vigilancia.ssm@summitmx.com';

        ///////--------RESERVACION DE VUELOS Y HOTEL QRO-----////////////
        $data3['QROnameBuyF'] = 'RESERVACIONES Y VUELOS QRO';
        $data3['QROemailBuyF'] = 'vanessa.gonzalez@summitmx.com';

        $data3['CCQROemailBuyF'] = 'alejandra.trujillo@summitmx.com';

        /////////---------CORREO A RH SLM----------///////////////////
        $data3['SLMnameHR'] = 'RECURSOS HUMANOS SLM';
        $data3['SLMemailHR'] = 'reyna.soto@summitmx.com';

        $data3['CC1SLMemailHR'] = 'francisco.peguero@summitmx.com';
        
        // $data3['CC2SLMemailHR'] = 'slm.vigilancia@summitmx.com';

        $data3['CC3SLMemailHR'] = 'viviana.mercado@summitmx.com';

        ///////--------RESERVACION DE VUELOS Y HOTEL SLM-----////////////
        $data3['SLMnameBuyF'] = 'RESERVACIONES Y VUELOS SLM';
        $data3['SLMemailBuyF'] = 'angelica.ruiz@summitmx.com';#

        $data3['CCSLMemailBuyF'] = 'alejandra.trujillo@summitmx.com';

        $data3['MTYemailCxP'] = 'jorge.garcia@summitmx.com';
        $data3['MTYnameCxP'] = 'CUENTAS X PAGAR MTY';
        $data3['CCMTYemailCxP'] = 'angel.fuentes@summitmx.com';
        

        $data3['QROemailCxP'] = 'coral.mederos@summitmx.com';
        $data3['QROnameCxP'] = 'CUENTAS X PAGAR QRO';
        $data3['CCQROemailCxP'] = 'pablo.resendiz@summitmx.com';
        
        $data3['SLMemailCxP'] = 'coral.mederos@summitmx.com';
        $data3['SLMnameCxP'] = 'CUENTAS X PAGAR SLM';
        $data3['CCSLMemailCxP'] = 'pablo.resendiz@summitmx.com';
        
        // ///////////////////////////////////////////////////////////////////////////////////////

        // ################################CORREOS DE PRUEBA
        //////////------AVISO DE ANTICIPO TESORERIA ----//////////////
        // $data3['nameAdvT'] = 'GERARDO CASTRO';
        // $data3['emailAdvT'] = 'gerardo.castro@yopmail.com';

        // // $data3['CCemailAdvT'] = 'gerardo.castro@yopmail.com';
               
        // // // /////////---------CORREO A RH MTY----------///////////////////
        // $data3['MTYnameHR'] = 'RECURSOS HUMANOS MTY';
        // $data3['MTYemailHR'] = 'wendy.garza@yopmail.com';

        // $data3['CC1MTYemailHR'] = 'juan.barron@yopmail.com';

        // $data3['CC2MTYemailHR'] = 'mty.vigilancia@yopmail.com';
        
        // // // ///////--------RESERVACION DE VUELOS Y HOTEL MTY-----////////////
        // $data3['MTYnameBuyF'] = 'RESERVACIONES Y VUELOS MTY';
        // $data3['MTYemailBuyF'] = 'wendy.garza@yopmail.com';

        // $data3['CCMTYemailBuyF'] = 'laura.delrio@yopmail.com';

        // // // /////////---------CORREO A RH QRO----------///////////////////
        // $data3['QROnameHR'] = 'RECURSOS HUMANOS QRO';
        // $data3['QROemailHR'] = 'juan.hernandez@yopmail.com';

        // $data3['CC1QROemailHR'] = 'francisco.peguero@yopmail.com';
        
        // $data3['CC2QROemailHR'] = 'vigilancia.ssm@yopmail.com';

        // // // ///////--------RESERVACION DE VUELOS Y HOTEL QRO-----////////////
        // $data3['QROnameBuyF'] = 'RESERVACIONES Y VUELOS QRO';
        // $data3['QROemailBuyF'] = 'laura.mendoza@yopmail.com';
        
        // $data3['CCQROemailBuyF'] = 'alejandra.trujillo@yopmail.com';
        
        // // // /////////---------CORREO A RH SLM----------///////////////////
        // $data3['SLMnameHR'] = 'RECURSOS HUMANOS SLM';
        // $data3['SLMemailHR'] = 'paulina.gaona@yopmail.com';#'paulina.gaona@yopmail.com';


        // $data3['CC1SLMemailHR'] = 'francisco.peguero@yopmail.com';
        
        
        // $data3['CC2SLMemailHR'] = 'slm.vigilancia@yopmail.com';
        
        // // // ///////--------RESERVACION DE VUELOS Y HOTEL SLM-----////////////
        // $data3['SLMnameBuyF'] = 'RESERVACIONES Y VUELOS SLM';
        // $data3['SLMemailBuyF'] = 'ericka.belman@yopmail.com';#
        
        // $data3['CCSLMemailBuyF'] = 'paulina.gaona@yopmail.com';
        
        // $data3['MTYemailCxP'] = 'jorge.garcia@yopmail.com';
        // $data3['MTYnameCxP'] = 'CUENTAS X PAGAR MTY';
        // $data3['CCMTYemailCxP'] = 'angel.fuentes@yopmail.com';
        
        // $data3['QROemailCxP'] = 'coral.medereos@yopmail.com';
        // $data3['QROnameCxP'] = 'CUENTAS X PAGAR QRO';
        // $data3['CCQROemailCxP'] = 'pablo.resendiz@yopmail.com';
        
        // $data3['SLMemailCxP'] = 'coral.mederos@yopmail.com';
        // $data3['SLMnameCxP'] = 'CUENTAS X PAGAR SLM';
        // $data3['CCSLMemailCxP'] = 'pablo.resendiz@yopmail.com';
        
        // /////////////////////////////////////////////////////////

        //Mail::Send('mails.advanceTransfer', ['folioMail'=> $folioMail], function($mail) use($folioMail, $data3){
        //    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
        //    $mail->to($data3['emailAdvT'], $data3['nameAdvT'])
        //    ->cc($data3['CCemailAdvT']);
        //});

        Mail::Send('mails.replyRequestApprobSi', ['folioMail'=> $folioMail, 'foliosPend' => $foliosPend], function($mail) use($folioMail){
            $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
            $mail->to($folioMail->emailU, $folioMail->name);
        });

        if ($folioMail->company=='MTY') {

            if (count($foliosPend)>0) {
                
                Mail::Send('mails.preAdvanceTransfer', ['folioMail'=> $folioMail,'foliosPend' => $foliosPend], function($mail) use($folioMail, $data3){
                    $mail->subject('SOLICITUD DE PRE-ANTICIPO: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                    $mail->to($data3['MTYemailCxP'], $data3['MTYnameCxP'])
                    ->cc($data3['CCMTYemailCxP']);
                });
                
                if($folioMail->anticipo>0)
                {
                    $trans = new Transfer;
                    $trans->company=$folioMail->company;
                    $trans->id_header_folio=$folioMail->id_header_folio;
                    $trans->deposito='0';
                    $trans->save();
                }
            } 
            else{

                Mail::Send('mails.advanceTransfer', ['folioMail'=> $folioMail], function($mail) use($folioMail, $data3){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                    $mail->to($data3['emailAdvT'],$data3['nameAdvT'])
                    ->cc($data3['CCemailAdvT'])
                    ->bcc('enedelia.alanis@summitmx.com');
                });

                if($folioMail->anticipo>0)
                {
                    $trans = new Transfer;
                    $trans->company=$folioMail->company;
                    $trans->id_header_folio=$folioMail->id_header_folio;
                    $trans->deposito='1';
                    $trans->save();
                }
            }
                                        
            Mail::Send('mails.buyFligth', ['folioMail'=> $folioMail], function($mail) use($folioMail, $data3){
                $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                $mail->to($data3['MTYemailBuyF'], $data3['MTYnameBuyF'])
                ->cc($data3['CCMTYemailBuyF']);
            });

            Mail::Send('mails.humanResources', ['folioMail'=> $folioMail], function($mail) use($folioMail, $data3){
                $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                $mail->to($data3['MTYemailHR'], $data3['MTYnameHR'])
                ->cc($data3['CC1MTYemailHR'])
                ->cc($data3['CC2MTYemailHR']);
            });

        }elseif ($folioMail->company=='QRO') {
            
            if (count($foliosPend)>0) {
                
                Mail::Send('mails.preAdvanceTransfer', ['folioMail'=> $folioMail,'foliosPend' => $foliosPend], function($mail) use($folioMail, $data3){
                    $mail->subject('ADVIATICOS SOLICITUD DE PRE-ANTICIPO: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                    $mail->to($data3['QROemailCxP'], $data3['QROnameCxP'])
                    ->cc($data3['CCQROemailCxP'], 'laura.rosas@summitmx.com', 'pablo.resendiz@summitmx.com');
                });

                if($folioMail->anticipo>0)
                {
                    $trans = new Transfer;
                    $trans->company=$folioMail->company;
                    $trans->id_header_folio=$folioMail->id_header_folio;
                    $trans->deposito='0';
                    $trans->save();
                }
            } 
            else{
 
                Mail::Send('mails.advanceTransfer', ['folioMail'=> $folioMail], function($mail) use($folioMail, $data3){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                    $mail->to($data3['emailAdvT'],$data3['nameAdvT'])
                    ->cc($data3['CCemailAdvT'])
                    ->bcc('enedelia.alanis@summitmx.com');
                });

                if($folioMail->anticipo>0)
                {
                    $trans = new Transfer;
                    $trans->company=$folioMail->company;
                    $trans->id_header_folio=$folioMail->id_header_folio;
                    $trans->deposito='1';
                    $trans->save();
                }
            }

            Mail::Send('mails.buyFligth', ['folioMail'=> $folioMail], function($mail) use($folioMail, $data3){
                $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                $mail->to($data3['QROemailBuyF'], $data3['QROnameBuyF'])
                ->cc($data3['CCQROemailBuyF']);
            });

            Mail::Send('mails.humanResources', ['folioMail'=> $folioMail], function($mail) use($folioMail, $data3){
                $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                $mail->to($data3['QROemailHR'], $data3['QROnameHR'])
                ->cc( $data3['CC1QROemailHR'])
                ->cc( $data3['CC2QROemailHR']);#'francisco.peguero@summitmx.com'
            });
        }
        elseif($folioMail->company=='SLM'){

            if (count($foliosPend)>0) {

                Mail::Send('mails.preAdvanceTransfer', ['folioMail'=> $folioMail,'foliosPend' => $foliosPend], function($mail) use($folioMail, $data3){
                    $mail->subject('ADVIATICOS SOLICITUD DE PRE-ANTICIPO: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                    $mail->to($data3['SLMemailCxP'], $data3['SLMnameCxP'])
                    ->cc($data3['CCSLMemailCxP'], 'laura.rosas@summitmx.com', 'pablo.resendiz@summitmx.com');
                });

                if($folioMail->anticipo>0)
                {
                    $trans = new Transfer;
                    $trans->company=$folioMail->company;
                    $trans->id_header_folio=$folioMail->id_header_folio;
                    $trans->deposito='0';
                    $trans->save();
                }

            } 
            else{
 
                Mail::Send('mails.advanceTransfer', ['folioMail'=> $folioMail], function($mail) use($folioMail, $data3){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                    $mail->to($data3['emailAdvT'],$data3['nameAdvT'])
                    ->cc($data3['CCemailAdvT'])
                    ->bcc('enedelia.alanis@summitmx.com');
                });

                if($folioMail->anticipo>0)
                {
                    $trans = new Transfer;
                    $trans->company=$folioMail->company;
                    $trans->id_header_folio=$folioMail->id_header_folio;
                    $trans->deposito='1';
                    $trans->save();
                }
            }
            
            Mail::Send('mails.buyFligth', ['folioMail'=> $folioMail], function($mail) use($folioMail, $data3){
                $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                $mail->to($data3['SLMemailBuyF'], $data3['SLMnameBuyF'])
                ->cc( $data3['CCSLMemailBuyF']);;
            });

            Mail::Send('mails.humanResources', ['folioMail'=> $folioMail], function($mail) use($folioMail, $data3){
                $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                $mail->to($data3['SLMemailHR'], $data3['SLMnameHR'])
                ->cc($data3['CC1SLMemailHR'])
                // ->cc($data3['CC2SLMemailHR'])
                ->cc($data3['CC3SLMemailHR']);
            });

        }

        if (Mail::failures()) 
        {
        // return response showing failed emails
            dd(Mail::failures());
        }       
    }

    public function replyRequestApprobSi($id, $token, $TrabajadorID)
    {
        $folio=Folio::where('_token','=',$token)
        ->where('id_header_folio','=', $id)
        ->first();

        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_INFO_USERS as p','u.numeroNom','=','p.TrabajadorID')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','a.TrabajadorID','=',DB::raw($TrabajadorID))
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f._token','u.name','u.company','f.correo_solicitante as emailU','p.BancoCuenta','p.CLABE','p.Banco','a.TrabajadorID','a.NombreAuto','a.email as emailA','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('p.compania','LIKE','%'.$folio->company.'%')
        ->first();

        Mail::Send('mails.replyRequestApprobSi', ['folioMail'=> $folioMail], function($mail) use($folioMail){
            $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
            $mail->to($folioMail->emailU, $folioMail->name);
        });     
    }

    public function replyRequestApprobNo($id, $token, $TrabajadorID)
    {
        $folio=Folio::where('_token','=',$token)
        ->where('id_header_folio','=', $id)
        ->first();
        
        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_INFO_USERS as p','u.numeroNom','=','p.TrabajadorID')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','a.TrabajadorID','=',DB::raw($TrabajadorID))
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f._token','u.name','u.company','f.correo_solicitante as emailU','p.BancoCuenta','p.CLABE','p.Banco','a.TrabajadorID','a.NombreAuto','a.email as emailA','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('p.compania','LIKE','%'.$folio->company.'%')
        ->first();

        Mail::Send('mails.replyRequestApprobNo', ['folioMail'=> $folioMail], function($mail) use($folioMail){
                $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                $mail->to($folioMail->emailU, $folioMail->name);
        });     
    }

    public function Auto1($id, $token)
    {
        $Autorizador=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        //->join('user_profile as p','f.id_solicitante','=','p.numeroNom')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','f.correo_auto1','=','a.email')
        ->select('u.company','a.TrabajadorID','a.email')
        ->where('f.id_header_folio','=',$id)
        ->where('_token','=',$token)
        ->first();

        return $Autorizador;
    }

    public function Auto2($id, $token)
    {
        $Autorizador=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        //->join('user_profile as p','f.id_solicitante','=','p.numeroNom')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','f.correo_auto2','=','a.email')
        ->select('u.company','a.TrabajadorID','a.email')
        ->where('f.id_header_folio','=',$id)
        ->where('_token','=',$token)
        ->first();

        return $Autorizador;
    }

}
