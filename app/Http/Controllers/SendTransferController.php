<?php

namespace sisViaticos\Http\Controllers;

use Illuminate\Http\Request;
use sisViaticos\Transfer;
use Mail;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SendTransferController extends Controller
{
    public function index(){
        $foliostransfer=DB::table('VIEW_SSM_FOLIOS_SIN_TRANSF')
        ->get();

        //////////------AVISO DE ANTICIPO TESORERIA ----//////////////
        $data3['nameAdvT'] = 'GERARDO CASTRO';
        $data3['emailAdvT'] = 'gerardo.castro@summitmx.com';
        
        $data3['CCemailAdvT'] = 'coral.mederos@summitmx.com';
               
        // /////////---------CORREO A RH MTY----------///////////////////
        $data3['MTYnameHR'] = 'RECURSOS HUMANOS MTY';
        $data3['MTYemailHR'] = 'wendy.garza@summitmx.com';

        $data3['CC1MTYemailHR'] = 'juan.barron@summitmx.com';
        
        $data3['CC2MTYemailHR'] = 'mty.vigilancia@summitmx.com';

        // ///////--------RESERVACION DE VUELOS Y HOTEL MTY-----////////////
        $data3['MTYnameBuyF'] = 'RESERVACIONES Y VUELOS MTY';
        $data3['MTYemailBuyF'] = 'wendy.garza@summitmx.com';
        
        $data3['CCMTYemailBuyF'] = 'mildred.asueta@summitmx.com';

        // /////////---------CORREO A RH QRO----------///////////////////
        $data3['QROnameHR'] = 'RECURSOS HUMANOS QRO';
        $data3['QROemailHR'] = 'juan.hernandez@summitmx.com';

        $data3['CC1QROemailHR'] = 'francisco.peguero@summitmx.com';
        
        $data3['CC2QROemailHR'] = 'vigilancia.ssm@summitmx.com';

        // ///////--------RESERVACION DE VUELOS Y HOTEL QRO-----////////////
        $data3['QROnameBuyF'] = 'RESERVACIONES Y VUELOS QRO';
        $data3['QROemailBuyF'] = 'vanessa.gonzalez@summitmx.com';

        $data3['CCQROemailBuyF'] = 'alejandra.trujillo@summitmx.com';

        // /////////---------CORREO A RH SLM----------///////////////////
        $data3['SLMnameHR'] = 'RECURSOS HUMANOS SLM';
        $data3['SLMemailHR'] = 'reyna.soto@summitmx.com';

        $data3['CC1SLMemailHR'] = 'francisco.peguero@summitmx.com';
        
        // $data3['CC2SLMemailHR'] = 'slm.vigilancia@summitmx.com';

        $data3['CC3SLMemailHR'] = 'viviana.mercado@summitmx.com';

        // ///////--------RESERVACION DE VUELOS Y HOTEL SLM-----////////////
        $data3['SLMnameBuyF'] = 'RESERVACIONES Y VUELOS SLM';
        $data3['SLMemailBuyF'] = 'andrea.villagomez@summitmx.com';#

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
        
        ///////////////////////////////////////////////////////////////////////////////////////

        // ################################CORREOS DE PRUEBA
        //////////------AVISO DE ANTICIPO TESORERIA ----//////////////
        // $data3['nameAdvT'] = 'GERARDO CASTRO';
        // $data3['emailAdvT'] = 'gerardo.castro@summitmx.com';

        // // $data3['CCemailAdvT'] = 'gerardo.castro@summitmx.com';
               
        // // // /////////---------CORREO A RH MTY----------///////////////////
        // $data3['MTYnameHR'] = 'RECURSOS HUMANOS MTY';
        // $data3['MTYemailHR'] = 'wendy.garza@summitmx.com';

        // $data3['CC1MTYemailHR'] = 'juan.barron@summitmx.com';

        // $data3['CC2MTYemailHR'] = 'mty.vigilancia@summitmx.com';
        
        // // // ///////--------RESERVACION DE VUELOS Y HOTEL MTY-----////////////
        // $data3['MTYnameBuyF'] = 'RESERVACIONES Y VUELOS MTY';
        // $data3['MTYemailBuyF'] = 'wendy.garza@summitmx.com';

        // $data3['CCMTYemailBuyF'] = 'laura.delrio@summitmx.com';

        // // // /////////---------CORREO A RH QRO----------///////////////////
        // $data3['QROnameHR'] = 'RECURSOS HUMANOS QRO';
        // $data3['QROemailHR'] = 'juan.hernandez@summitmx.com';

        // $data3['CC1QROemailHR'] = 'francisco.peguero@summitmx.com';
        
        // $data3['CC2QROemailHR'] = 'vigilancia.ssm@summitmx.com';

        // // // ///////--------RESERVACION DE VUELOS Y HOTEL QRO-----////////////
        // $data3['QROnameBuyF'] = 'RESERVACIONES Y VUELOS QRO';
        // $data3['QROemailBuyF'] = 'laura.mendoza@summitmx.com';
        
        // $data3['CCQROemailBuyF'] = 'alejandra.trujillo@summitmx.com';
        
        // // /////////---------CORREO A RH SLM----------///////////////////
        // $data3['SLMnameHR'] = 'RECURSOS HUMANOS SLM';
        // $data3['SLMemailHR'] = 'paulina.gaona@summitmx.com';
        
        // $data3['CC1SLMemailHR'] = 'francisco.peguero@summitmx.com';
        
        
        // $data3['CC2SLMemailHR'] = 'slm.vigilancia@summitmx.com';
        
        // // ///////--------RESERVACION DE VUELOS Y HOTEL SLM-----////////////
        // $data3['SLMnameBuyF'] = 'RESERVACIONES Y VUELOS SLM';
        // $data3['SLMemailBuyF'] = 'ericka.belman@summitmx.com';#
        
        // $data3['CCSLMemailBuyF'] = 'paulina.gaona@summitmx.com';
        
        // $data3['MTYemailCxP'] = 'jorge.garcia@summitmx.com';
        // $data3['MTYnameCxP'] = 'CUENTAS X PAGAR MTY';
        // $data3['CCMTYemailCxP'] = 'angel.fuentes@summitmx.com';
        
        // $data3['QROemailCxP'] = 'coarl.mederos@summitmx.com';
        // $data3['QROnameCxP'] = 'CUENTAS X PAGAR QRO';
        // $data3['CCQROemailCxP'] = 'pablo.resendiz@summitmx.com';
        
        // $data3['SLMemailCxP'] = 'coral.mederos@summitmx.com';
        // $data3['SLMnameCxP'] = 'CUENTAS X PAGAR SLM';
        // $data3['CCSLMemailCxP'] = 'pablo.resendiz@summitmx.com';
        
        /////////////////////////////////////////////////////////

        $cont=0;
        while ($cont < count($foliostransfer)){
            $folioMail=DB::table('ssm_viat_header_folio as f')
            ->join('users as u','f.id_solicitante','=','u.id')
            ->join('VIEW_SSM_INFO_USERS as p','u.numeroNom','=','p.TrabajadorID')
            ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
            ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','u.numeroNom','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f._token','u.id','u.name','u.company','u.email as emailU','p.BancoCuenta','p.CLABE','p.Banco','m.moneda')
            ->where('f.id_header_folio','=',$foliostransfer[$cont]->FOLIO)
            ->where('p.compania','LIKE','%'.$foliostransfer[$cont]->company.'%')
            ->first();

            $usernom = $folioMail->numeroNom;
            $branch = $folioMail->company;
    
            $company = substr($branch,0,1);                
            $valuesUser = $company.$usernom;
    
            $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));


            $folioAuto=DB::table('VIEW_SSM_AUTORIZADOR_ANTICIPO')
            ->select('id_header_folio','NombreAuto as autorizador', 'status', 'company', 'anticipo')
            ->where('id_header_folio', '=', $folioMail->id_header_folio)
            ->orderby('anticipo', 'desc')
            ->first();

            $foliosPend=DB::table('ssm_viat_header_folio as f')
            ->where('company','=',$folioMail->company)
            ->whereBetween('id_status',[1,15])
            ->where('id_solicitante','=',$folioMail->id)
            ->where('id_header_folio','!=',$folioMail->id_header_folio)
            ->where('fecha_llegada','<',$folioMail->fecha_salida)
            ->where('anticipo','>','0')
            ->get();
            
            if ($userClaims[0]->compania=='MTY') {
                if(count($foliosPend)>0){
                    Mail::Send('mails.preAdvanceTransferS', ['folioMail'=> $folioMail, 'foliosPend'=>$foliosPend, 'folioAuto'=>$folioAuto], function($mail) use($folioMail, $data3){
                        $mail->subject('SOLICITUD DE PRE-ANTICIPO: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                        $mail->to($data3['MTYemailCxP'], $data3['MTYnameCxP'])
                        ->cc($data3['CCMTYemailCxP']);
                    });
                    
                    if($folioMail->anticipo>0){    
                        $trans = new Transfer;
                        $trans->company=$folioMail->company;
                        $trans->id_header_folio=$folioMail->id_header_folio;
                        $trans->deposito='0';
                        $trans->save();
                    }

                }
                else{
                    Mail::Send('mails.advanceTransferS',  ['folioMail'=> $folioMail, 'folioAuto'=>$folioAuto], function($mail) use($folioMail, $data3){
                        $mail->subject('SOLICITUD Y REPORTE DE VIAJE:'.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                        $mail->to($data3['emailAdvT'],$data3['nameAdvT'])
                        ->cc($data3['CCemailAdvT']);
                    });    

                    if($folioMail->anticipo>0){                
                        $trans = new Transfer;
                        $trans->company=$folioMail->company;
                        $trans->id_header_folio=$folioMail->id_header_folio;
                        $trans->deposito='1';
                        $trans->save();
                    }
                }
                Mail::Send('mails.buyFligthS', ['folioMail'=> $folioMail, 'folioAuto'=>$folioAuto], function($mail) use($folioMail, $data3){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                    $mail->to($data3['MTYemailBuyF'], $data3['MTYnameBuyF'])
                    ->cc($data3['CCMTYemailBuyF']);
                });
    
                Mail::Send('mails.humanResourcesS', ['folioMail'=> $folioMail, 'folioAuto'=>$folioAuto], function($mail) use($folioMail, $data3){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                    $mail->to($data3['MTYemailHR'], $data3['MTYnameHR'])
                    ->cc($data3['CC1MTYemailHR'])
                    ->cc($data3['CC2MTYemailHR']);
                });

            }
            elseif ($userClaims[0]->compania=='QRO,SLM,MTY') {
                if(count($foliosPend)>0){
                    Mail::Send('mails.preAdvanceTransferS', ['folioMail'=> $folioMail, 'foliosPend'=>$foliosPend, 'folioAuto'=>$folioAuto], function($mail) use($folioMail, $data3){
                        $mail->subject('ADVIATICOS SOLICITUD DE PRE-ANTICIPO: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                        $mail->to($data3['CCQROemailCxP'], $data3['QROnameCxP']);
                    });
                    if($folioMail->anticipo>0){    
                        $trans = new Transfer;
                        $trans->company=$folioMail->company;
                        $trans->id_header_folio=$folioMail->id_header_folio;
                        $trans->deposito='0';
                        $trans->save();
                    }
                }
                else{    
                    Mail::Send('mails.advanceTransferS', ['folioMail'=> $folioMail, 'folioAuto'=>$folioAuto], function($mail) use($folioMail, $data3){
                        $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                        $mail->to($data3['emailAdvT'],$data3['nameAdvT'])
                        ->cc($data3['CCemailAdvT']);
                    });

                    if($folioMail->anticipo>0){                
                        $trans = new Transfer;
                        $trans->company=$folioMail->company;
                        $trans->id_header_folio=$folioMail->id_header_folio;
                        $trans->deposito='1';
                        $trans->save();
                    }
                }

                //Condicional para verificar que el usuario sea de MTY
                if ($folioMail->company=='QRO'){
                    Mail::Send('mails.buyFligthS', ['folioMail'=> $folioMail, 'folioAuto'=>$folioAuto], function($mail) use($folioMail, $data3){
                        $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                        $mail->to($data3['QROemailBuyF'], $data3['QROnameBuyF'])
                        ->cc($data3['CCQROemailBuyF']);
                    });
        
                    Mail::Send('mails.humanResourcesS', ['folioMail'=> $folioMail, 'folioAuto'=>$folioAuto], function($mail) use($folioMail, $data3){
                        $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                        $mail->to($data3['QROemailHR'], $data3['QROnameHR'])
                        ->cc( $data3['CC1QROemailHR'])
                        ->cc( $data3['CC2QROemailHR']);#'francisco.peguero@summitmx.com'
                    });
                }

                elseif ($folioMail->company=='MTY'){
                    Mail::Send('mails.buyFligthS', ['folioMail'=> $folioMail, 'folioAuto'=>$folioAuto], function($mail) use($folioMail, $data3){
                        $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                        $mail->to($data3['MTYemailBuyF'], $data3['MTYnameBuyF'])
                        ->cc($data3['CCMTYemailBuyF']);
                    });
        
                    Mail::Send('mails.humanResourcesS', ['folioMail'=> $folioMail, 'folioAuto'=>$folioAuto], function($mail) use($folioMail, $data3){
                        $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                        $mail->to($data3['MTYemailHR'], $data3['MTYnameHR'])
                        ->cc($data3['CC1MTYemailHR'])
                        ->cc($data3['CC2MTYemailHR']);
                    });
                }
                
            }
            elseif($userClaims[0]->compania=='SLM' || $userClaims[0]->compania=='QRO' ||$userClaims[0]->compania=='QRO,SLM'){
                if(count($foliosPend)>0){
                    Mail::Send('mails.preAdvanceTransferS', ['folioMail'=> $folioMail, 'foliosPend'=>$foliosPend, 'folioAuto'=>$folioAuto], function($mail) use($folioMail, $data3){
                        $mail->subject('ADVIATICOS SOLICITUD DE PRE-ANTICIPO: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                        $mail->to($data3['SLMemailCxP'], $data3['SLMnameCxP'])
                        ->cc($data3['CCSLMemailCxP'], 'pablo.resendiz@summitmx.com');
                    });
                    if($folioMail->anticipo>0){    
                        $trans = new Transfer;
                        $trans->company=$folioMail->company;
                        $trans->id_header_folio=$folioMail->id_header_folio;
                        $trans->deposito='0';
                        $trans->save();
                    }
                }
                else{    
                    Mail::Send('mails.advanceTransferS', ['folioMail'=> $folioMail, 'folioAuto'=>$folioAuto], function($mail) use($folioMail, $data3){
                        $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                        $mail->to($data3['emailAdvT'],$data3['nameAdvT'])
                        ->cc($data3['CCemailAdvT']);
                    });
                    if($folioMail->anticipo>0){                
                        $trans = new Transfer;
                        $trans->company=$folioMail->company;
                        $trans->id_header_folio=$folioMail->id_header_folio;
                        $trans->deposito='1';
                        $trans->save();
                    }
                }
                if ($folioMail->company=='SLM'){
                    Mail::Send('mails.buyFligthS', ['folioMail'=> $folioMail, 'folioAuto'=>$folioAuto], function($mail) use($folioMail, $data3){
                        $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                        $mail->to($data3['SLMemailBuyF'], $data3['SLMnameBuyF'])
                        ->cc( $data3['CCSLMemailBuyF']);;
                    });
        
                    Mail::Send('mails.humanResourcesS', ['folioMail'=> $folioMail, 'folioAuto'=>$folioAuto], function($mail) use($folioMail, $data3){
                        $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                        $mail->to($data3['SLMemailHR'], $data3['SLMnameHR'])
                        ->cc($data3['CC1SLMemailHR'])
                        // ->cc($data3['CC2SLMemailHR'])
                        ->cc($data3['CC3SLMemailHR']);
                    });
                }
                elseif ($folioMail->company=='QRO'){
                    Mail::Send('mails.buyFligthS', ['folioMail'=> $folioMail, 'folioAuto'=>$folioAuto], function($mail) use($folioMail, $data3){
                        $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                        $mail->to($data3['QROemailBuyF'], $data3['QROnameBuyF'])
                        ->cc($data3['CCQROemailBuyF']);
                    });
        
                    Mail::Send('mails.humanResourcesS', ['folioMail'=> $folioMail, 'folioAuto'=>$folioAuto], function($mail) use($folioMail, $data3){
                        $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                        $mail->to($data3['QROemailHR'], $data3['QROnameHR'])
                        ->cc( $data3['CC1QROemailHR'])
                        ->cc( $data3['CC2QROemailHR']);#'francisco.peguero@summitmx.com'
                    });
                }


            }
            $cont=$cont+1;
        }
       

		return("Se han enviado ". $cont ." mensajes de recordatorio por correo" ); 

    }
}