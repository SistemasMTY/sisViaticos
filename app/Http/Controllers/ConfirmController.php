<?php

namespace sisViaticos\Http\Controllers;

use Illuminate\Http\Request;
use sisViaticos\Folio;
use Illuminate\Support\Facades\Redirect;
use sisViaticos\Http\Requests\FolioFormRequest;
use Illuminate\Support\Str;
use Mail;

use DB;
use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;

class ConfirmController extends Controller
{
    //

    public function __construct()
    {

    }
    
    public function show($token, $id, $option)
    {
        $folio=Folio::where('_token','=',$token)
        ->where('id_header_folio','=', $id)
        ->first();
        if ($folio->id_status<'6') {

            if ($option=='si') 
            {

                if ($folio->tipo=='Nacional') 
                {

                    $folio->id_status='8';
                    $folio->update();

                    $data1=DB::table('ssm_viat_header_folio as f')
                    ->join('users as u','f.id_solicitante','=','u.id')
                    ->join('user_profile as p','f.id_solicitante','=','p.id_user')
                    ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
                    ->join('ssm_viat_autorizadores as a','p.id_gerenteGral','=','a.id_autorizador')
                    ->select('f.id_header_folio','f.fecha','f.tipo','u.name','f.destino','f.proposito','f.fecha_salida','f.fecha_llegada','f.criterio','p.banco','p.clabe','p.cuenta','m.moneda','f.anticipo','a.autorizador','a.autorizador_email as emailA','u.email as emailU','f._token')
                    ->where('f.id_header_folio','=',$id)
                    ->where('_token','=',$token)
                    ->first();

                    $data2['emailBF'] = 'sistemas_mty@summitmx.com';
                    $data2['name'] = 'RECURSOS HUMANOS';
                    $data3['emailCXP'] = 'sistemas_mty@summitmx.com';
                    $data3['name'] = 'CUENTAS X PAGAR';

                    Mail::Send('mails.replyDirGeneralSi', ['data1'=> $data1], function($mail) use($data1){
                        $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$data1->name.', Folio: '.$data1->id_header_folio);
                        $mail->to($data1->emailU, $data1->name);
                    });

                    Mail::Send('mails.buyFligth', ['data1'=> $data1, 'data2'=>$data2], function($mail) use($data1, $data2){
                        $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$data1->name.', Folio: '.$data1->id_header_folio);
                        $mail->to($data2['emailBF'], $data2['name']);
                    });

                    Mail::Send('mails.advanceTransfer', ['data1'=> $data1], function($mail) use($data1, $data3){
                        $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$data1->name.', Folio: '.$data1->id_header_folio);
                        $mail->to($data3['emailCXP'], $data3['name']);
                    });

                    return Redirect::to('mails/optionSi');
                }
                elseif ($folio->tipo=='Internacional') 
                {

                    $folio->id_status='6';
                    $folio->update();

                    $data1=DB::table('ssm_viat_header_folio as f')
                    ->join('users as u','f.id_solicitante','=','u.id')
                    ->join('user_profile as p','f.id_solicitante','=','p.id_user')
                    ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
                    ->join('ssm_viat_autorizadores as a','p.id_directorGral','=','a.id_autorizador')
                    ->select('f.id_header_folio','f.fecha','f.tipo','u.name','f.destino','f.proposito','f.fecha_salida','f.fecha_llegada','m.moneda','f.anticipo','a.autorizador','a.autorizador_email as emailA','u.email as emailU','f._token')
                    ->where('f.id_header_folio','=',$id)
                    ->where('_token','=',$token)
                    ->first();

                    $data2=DB::table('ssm_viat_header_folio as f')
                    ->join('users as u','f.id_solicitante','=','u.id')
                    ->join('user_profile as p','f.id_solicitante','=','p.id_user')
                    ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
                    ->join('ssm_viat_autorizadores as a','p.id_gerenteGral','=','a.id_autorizador')
                    ->select('f.id_header_folio','f.fecha','f.tipo','u.name','f.destino','f.proposito','f.fecha_salida','f.fecha_llegada','m.moneda','f.anticipo','a.autorizador','a.autorizador_email as emailA','u.email as emailU','f._token')
                    ->where('f.id_header_folio','=',$id)
                    ->where('_token','=',$token)
                    ->first();

                    Mail::Send('mails.authorize', ['data1'=> $data1], function($mail) use($data1){
                        $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$data1->name.', Folio: '.$data1->id_header_folio);
                        $mail->to($data1->emailA, $data1->autorizador);
                    });

                    Mail::Send('mails.replyDirectorSi', ['data2'=> $data2], function($mail) use($data2){
                        $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$data2->name.', Folio: '.$data2->id_header_folio);
                        $mail->to($data2->emailU, $data2->name);
                    });

                    return Redirect::to('mails/optionSi');
                }
            }
            elseif($option=='no')
            {
                $folio->id_status='5';
                $folio->update();

                $data1=DB::table('ssm_viat_header_folio as f')
                ->join('users as u','f.id_solicitante','=','u.id')
                ->join('user_profile as p','f.id_solicitante','=','p.id_user')
                ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
                ->join('ssm_viat_autorizadores as a','p.id_gerenteGral','=','a.id_autorizador')
                ->select('f.id_header_folio','f.fecha','f.tipo','u.name','f.destino','f.proposito','f.fecha_salida','f.fecha_llegada','m.moneda','f.anticipo','a.autorizador','a.autorizador_email as emailA','u.email as emailU','f._token')
                ->where('f.id_header_folio','=',$id)
                ->where('_token','=',$token)
                ->first();

                $data2=DB::table('ssm_viat_header_folio as f')
                ->join('users as u','f.id_solicitante','=','u.id')
                ->join('user_profile as p','f.id_solicitante','=','p.id_user')
                ->join('ssm_viat_autorizadores as a','p.id_gerente','=','a.id_autorizador')
                ->select('u.name','a.autorizador','a.autorizador_email as emailA','u.email as emailU','f._token')
                ->where('f.id_header_folio','=',$id)
                ->where('_token','=',$token)
                ->first();


                Mail::Send('mails.replyDirectorNo', ['data1'=> $data1,'data2'=>$data2], function($mail) use($data1, $data2){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$data1->name.', Folio: '.$data1->id_header_folio);
                    $mail->to($data1->emailU, $data1->name)->cc($data2->emailA);
                });

                //Mail::Send('mails.replyDirectorNo', ['data1'=> $data1,'data2'=>$data2], function($mail) use($data1, $data2){
                //    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$data1->name.', Folio: '.$data1->id_header_folio);
                //    $mail->to($data2->emailA, $data2->autorizador);
                //});

                return Redirect::to('mails/optionNo');

            }
            
        }
        else
        {
            return Redirect::to('mails/optionFail');
        }
    }

    public function update($token, $id, $option)
    {
        $folio=Folio::where('_token','=',$token)
        ->where('id_header_folio','=', $id)
        ->first();

        if ($folio->id_status<'8')
        {

            if ($option=='si')
            {

                $folio->id_status='8';
                $folio->update();

                $data1=DB::table('ssm_viat_header_folio as f')
                ->join('users as u','f.id_solicitante','=','u.id')
                ->join('user_profile as p','f.id_solicitante','=','p.id_user')
                ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
                ->join('ssm_viat_autorizadores as a','p.id_directorGral','=','a.id_autorizador')
                ->select('f.id_header_folio','f.fecha','f.tipo','u.name','f.destino','f.proposito','f.fecha_salida','f.fecha_llegada','f.criterio','p.banco','p.clabe','p.cuenta','m.moneda','f.anticipo','a.autorizador','a.autorizador_email as emailA','u.email as emailU','f._token')
                ->where('f.id_header_folio','=',$id)
                ->where('_token','=',$token)
                ->first();

                $data2['emailBF'] = 'sistemas_mty@summitmx.com';
                $data2['name'] = 'RECURSOS HUMANOS';
                $data3['emailCXP'] = 'sistemas_mty@summitmx.com';
                $data3['name'] = 'CUENTAS X PAGAR';

                Mail::Send('mails.replyDirGeneralSi', ['data1'=> $data1], function($mail) use($data1){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$data1->name.', Folio: '.$data1->id_header_folio);
                    $mail->to($data1->emailU, $data1->name);
                });

                Mail::Send('mails.buyFligth', ['data1'=> $data1, 'data2'=>$data2], function($mail) use($data1, $data2){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$data1->name.', Folio: '.$data1->id_header_folio);
                    $mail->to($data2['emailBF'], $data2['name']);
                });

                Mail::Send('mails.advanceTransfer', ['data1'=> $data1], function($mail) use($data1, $data3){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$data1->name.', Folio: '.$data1->id_header_folio);
                    $mail->to($data3['emailCXP'], $data3['name']);
                });

                return Redirect::to('mails/optionSi');

            }
            else
            {
                $folio->id_status='7';
                $folio->update();

                $data1=DB::table('ssm_viat_header_folio as f')
                ->join('users as u','f.id_solicitante','=','u.id')
                ->join('user_profile as p','f.id_solicitante','=','p.id_user')
                ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
                ->join('ssm_viat_autorizadores as a','p.id_directorGral','=','a.id_autorizador')
                ->select('f.id_header_folio','f.fecha','f.tipo','u.name','f.destino','f.proposito','f.fecha_salida','f.fecha_llegada','m.moneda','f.anticipo','a.autorizador','a.autorizador_email as emailA','u.email as emailU','f._token')
                ->where('f.id_header_folio','=',$id)
                ->where('_token','=',$token)
                ->first();

                $data2=DB::table('ssm_viat_header_folio as f')
                ->join('users as u','f.id_solicitante','=','u.id')
                ->join('user_profile as p','f.id_solicitante','=','p.id_user')
                ->join('ssm_viat_autorizadores as a','p.id_gerenteGral','=','a.id_autorizador')
                ->select('a.autorizador','a.autorizador_email as emailA')
                ->where('f.id_header_folio','=',$id)
                ->where('_token','=',$token)
                ->first();

                $data3=DB::table('ssm_viat_header_folio as f')
                ->join('users as u','f.id_solicitante','=','u.id')
                ->join('user_profile as p','f.id_solicitante','=','p.id_user')
                ->join('ssm_viat_autorizadores as a','p.id_gerente','=','a.id_autorizador')
                ->select('a.autorizador','a.autorizador_email as emailA')
                ->where('f.id_header_folio','=',$id)
                ->where('_token','=',$token)
                ->first();


                Mail::Send('mails.replyDirGeneralNo', ['data1'=> $data1,'data2'=> $data2,'data3'=> $data3], function($mail) use($data1,$data2,$data3){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$data1->name.', Folio: '.$data1->id_header_folio);
                    $mail->to($data1->emailU, $data1->name)
                    ->cc($data2->emailA)
                    ->cc($data3->emailA);
                });

                return Redirect::to('mails/optionNo');


            }
        }
        else
        {
            return Redirect::to('mails/optionFail');
        }

    }
}
