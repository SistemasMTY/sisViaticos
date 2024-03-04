<?php

namespace sisViaticos\Http\Controllers;

use Illuminate\Http\Request;

use Mail;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SendReminderController extends Controller
{

	public function index()
    {
    	$totalEnviados = 0;
    	$dropTempTables = DB::unprepared(
    		DB::raw("
       			DROP TABLE IF EXISTS table_temp_a ;

       			DROP TABLE IF EXISTS table_temp_b ;

       			DROP TABLE IF EXISTS table_temp_c;
        		
    		")
		);

		$createTempTablesA = DB::unprepared(
		    DB::raw("
		        CREATE TEMPORARY TABLE table_temp_a AS 
					(SELECT h.*, u.name, u.email FROM ssm_viat_header_folio as h 
						LEFT JOIN users u on h.id_solicitante = u.id AND h.company = u.company WHERE h.id_status IN ('8') AND h.fecha_llegada < CURDATE());

		    ")
		);
		$createTempTablesB = DB::unprepared(
		    DB::raw("
		        CREATE TEMPORARY TABLE table_temp_b AS (SELECT h.*, u.name, u.email FROM ssm_viat_header_folio as h 
					LEFT JOIN users u on 
						h.id_solicitante = u.id AND h.company = u.company 
        				WHERE h.id_status IN ('1')
        				AND h.fecha_llegada < CURDATE());
		    ")
		);

		if ($createTempTablesB) {
			$foliosCreados = DB::
                select(
                    DB::raw("
                    	SELECT * FROM table_temp_b

                "));
				$cont = 0;
                while ($cont < count($foliosCreados)) 
    			{
    				$data1=DB::table('ssm_viat_header_folio as f')
		            ->join('users as u','f.id_solicitante','=','u.id')
		            ->join('user_profile as p','f.id_solicitante','=','p.id_user')
		            ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
		            ->join('ssm_viat_autorizadores as a','p.auto_1','=','a.id_autorizador')
		            ->select('f.id_header_folio','f.fecha','f.tipo','f.destino','f.proposito','f.fecha_salida','f.fecha_llegada','f.dias','m.moneda','f.anticipo','f._token','u.name','a.autorizador_email as email','a.autorizador','u.email as Uemail')
		            ->where('f.id_header_folio','=',$foliosCreados[$cont]->id_header_folio)
		            ->where('f._token','=',$foliosCreados[$cont]->_token)
		            ->first();

		            Mail::Send('mails.expirationCreated', ['data1'=> $data1], function($mail) use($data1){
		                $mail->subject(' Recordatorio ADViaticos/ADViaticos Reminder: '.$data1->name.', Folio: '.$data1->id_header_folio);
		                $mail->to($data1->Uemail, $data1->name);
		                // $mail->to('carlos.tovar@summitmx.com', $data1->name);
		            });
    				$cont=$cont+1;

    			}

    			$totalEnviados=$cont+$totalEnviados;
    		// return("Se han enviado ". $cont ." mensajes de recordatorio por correo" );

		}
		else {
		    $error = "ERROR MESSAGE";
		    dd($error);
		}        	


		if($createTempTablesA)
		{
			$foliosPendientes = DB::
                select(
                    DB::raw("
                    	SELECT * FROM table_temp_a

                "));

            $mytime = new  \DateTime(date("Y-m-d 00:00:00"));
            $cont = 0;

            while ($cont < count($foliosPendientes)) 
    		{

    			$fecha=  \DateTime::createFromFormat('Y-m-d H:i:s', $foliosPendientes[$cont]->fecha_llegada);
    			
    			$fechaMod = date_add($fecha,date_interval_create_from_date_string('7 days'));

    			if($mytime>$fecha)
    			{
    				$diff = $fecha->diff($mytime)->days;

    				$data1=DB::table('ssm_viat_header_folio as f')
		            ->join('users as u','f.id_solicitante','=','u.id')
		            ->join('user_profile as p','f.id_solicitante','=','p.id_user')
		            ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
		            ->join('ssm_viat_autorizadores as a','p.auto_1','=','a.id_autorizador')
		            ->select('f.id_header_folio','f.fecha','f.tipo','f.destino','f.proposito','f.fecha_salida','f.fecha_llegada','f.dias','m.moneda','f.anticipo','f._token','u.name','a.autorizador_email as email','a.autorizador','u.email as Uemail')
		            ->where('f.id_header_folio','=',$foliosPendientes[$cont]->id_header_folio)
		            ->where('f._token','=',$foliosPendientes[$cont]->_token)
		            ->first();

    				Mail::Send('mails.expirationReminder', ['data1'=> $data1,'diff'=>$diff], function($mail) use($data1){
		                $mail->subject('Recordatorio ADViaticos/ADViaticos Reminder: '.$data1->name.', Folio: '.$data1->id_header_folio);
		                $mail->to($data1->Uemail, $data1->name);
		                // $mail->to('carlos.tovar@summitmx.com', $data1->name);
		            });
    			}
    			else
    			{
    				$diff = $fecha->diff($mytime)->days + 1;

    				$data1=DB::table('ssm_viat_header_folio as f')
		            ->join('users as u','f.id_solicitante','=','u.id')
		            ->join('user_profile as p','f.id_solicitante','=','p.id_user')
		            ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
		            ->join('ssm_viat_autorizadores as a','p.auto_1','=','a.id_autorizador')
		            ->select('f.id_header_folio','f.fecha','f.tipo','f.destino','f.proposito','f.fecha_salida','f.fecha_llegada','f.dias','m.moneda','f.anticipo','f._token','u.name','a.autorizador_email as email','a.autorizador','u.email as Uemail')
		            ->where('f.id_header_folio','=',$foliosPendientes[$cont]->id_header_folio)
		            ->where('f._token','=',$foliosPendientes[$cont]->_token)
		            ->first();

    				Mail::Send('mails.checkReminder', ['data1'=> $data1,'diff'=>$diff], function($mail) use($data1){
		                $mail->subject('Recordatorio ADViaticos/ADViaticos Reminder: '.$data1->name.', Folio: '.$data1->id_header_folio);
		                $mail->to($data1->Uemail, $data1->name);
		                // $mail->to('carlos.tovar@summitmx.com', $data1->name);
		            });
    			}

    			$cont=$cont+1;

    		}

    		$totalEnviados=$cont+$totalEnviados;

		}
		else {
		    $error = "ERROR MESSAGE";
		    dd($error);
		}
  	
		$createTempTablesC = DB::unprepared(
		    DB::raw("
		        CREATE TEMPORARY TABLE table_temp_c AS 
		        	(SELECT company, id_header_folio, id_autorizador, status 
						FROM ssm_viat_firma_anticipo 
							where status = 0 And not id_autorizador is null);

				INSERT INTO table_temp_c 
					(SELECT company, id_header_folio, id_autorizador, status 
						FROM ssm_viat_firma_GASTO 
							where status = 0 And not id_autorizador is null);

		    ")
		);

		if ($createTempTablesC)
		{
			$listaAutorizadores = DB::
                select(
                   DB::raw("
                     	SELECT id_autorizador FROM table_temp_c
							GROUP BY id_autorizador;
               "));

            $cont=0;
            
            while ( $cont<count($listaAutorizadores)) {
            	$folios=DB::table('table_temp_c')
            	->where('id_autorizador','=',$listaAutorizadores[$cont]->id_autorizador)
            	->get();


            	$Autorizador=DB::table('ssm_viat_autorizadores')
            	->where('id_autorizador','=',$listaAutorizadores[$cont]->id_autorizador)
            	->first();

            	Mail::Send('mails.reminderAuthorizer', ['folios'=> $folios], function($mail) use($Autorizador){
	                $mail->subject('Recordatorio ADViaticos/ADViaticos Reminder: '.$Autorizador->autorizador);
	                $mail->to($Autorizador->autorizador_email, $Autorizador->autorizador);
	                //$mail->to('carlos.tovar@summitmx.com', $Autorizador->autorizador);
	            });

	            $cont=$cont+1;

            }
            $totalEnviados=$cont+$totalEnviados;

		}
		else {
		    $error = "ERROR MESSAGE";
		    dd($error);
		}       	


		return("Se han enviado ". $totalEnviados ." mensajes de recordatorio por correo" ); 
	}

	public function SendComprobacionesporVencer(){
		ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 4000);
		
		// Realiza el conteo de las solicitudes de las cuales haya pasado mas de 5 dias de la fecha de regreso
		$folios = DB::select('SP_Get_Folios_Por_Vencer');

		// Crea un foreach, para traer la info de la solicitud y poder enviar por correo
		foreach($folios as $folio)
		{			
	
			if($folio->fechaRest > 4){
				$data2=DB::table('ssm_viat_header_folio as f')
				->join('users as u','f.id_solicitante','=','u.id')
				->join('VIEW_SSM_INFO_USERS as p','u.numeroNomActual','=','p.TrabajadorIDM')
				->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
				->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','u.name','u.company','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','m.moneda','f.anticipo','f.correo_solicitante as emailU','f._token','p.BancoCuenta','p.CLABE','p.Banco', 'f.correo_auto1', 'f.correo_auto2')
				->where('f.id_header_folio','=',$folio->id_header_folio)
				->first();

				Mail::Send('mails.ComprobacionporVencer', ['data2'=> $data2], function($mail) use($data2){
					$mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$data2->name.', Folio: '.$data2->id_header_folio);
					$mail->to($data2->emailU, $data2->name)
					->cc([$data2->correo_auto1, $data2->correo_auto2, 'raymundo.lozano@summitmx.com', 'takehiko.gomi@summitmx.com', 'andres.salinas@summitmx.com'])
					->bcc('enedelia.alanis@summitmx.com');
				});
			}
			else{
				$data2=DB::table('ssm_viat_header_folio as f')
				->join('users as u','f.id_solicitante','=','u.id')
				->join('VIEW_SSM_INFO_USERS as p','u.numeroNomActual','=','p.TrabajadorIDM')
				->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
				->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','u.name','u.company','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','m.moneda','f.anticipo','f.correo_solicitante as emailU','f._token','p.BancoCuenta','p.CLABE','p.Banco', 'f.correo_auto1', 'f.correo_auto2')
				->where('f.id_header_folio','=',$folio->id_header_folio)
				->first();
				
				Mail::Send('mails.ComprobacionporVencer', ['data2'=> $data2], function($mail) use($data2){
					$mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$data2->name.', Folio: '.$data2->id_header_folio);
					$mail->to($data2->emailU, $data2->name)
					->bcc('enedelia.alanis@summitmx.com');
				});
			}
		}
	}

	public function SendDiasPasados(){
		ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 4000);
		
		// Realiza el conteo de las solicitudes de las cuales haya pasado mas de 5 dias de la fecha de regreso
		$folios = DB::select('SP_Get_Folios_Pasado_Comprobacion');

		// Crea un foreach, para traer la info de la solicitud y poder enviar por correo
		foreach($folios as $folio)
		{

			// Realiza el update de la solicitud 
			$values = [
				$folio->id_header_folio
			];

			$FoliUpdate = DB::update('SP_Update_Folio_CExtendida ?', $values);

			$data2=DB::table('ssm_viat_header_folio as f')
			->join('users as u','f.id_solicitante','=','u.id')
			->join('VIEW_SSM_INFO_USERS as p','u.numeroNomActual','=','p.TrabajadorIDM')
			->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
			->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','u.name','u.company','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','m.moneda','f.anticipo','f.correo_solicitante as emailU','f._token','p.BancoCuenta','p.CLABE','p.Banco', 'f.correo_auto1', 'f.correo_auto2')
			->where('f.id_header_folio','=',$folio->id_header_folio)
			->first();
	
			Mail::Send('mails.ComprobacionExt', ['data2'=> $data2], function($mail) use($data2){
				$mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$data2->name.', Folio: '.$data2->id_header_folio);
				// $mail->to('enedelia.alanis@summitmx.com')
				$mail->to($data2->emailU, $data2->name)
				->cc([$data2->correo_auto1, $data2->correo_auto2, 'raymundo.lozano@summitmx.com', 'takehiko.gomi@summitmx.com', 'andres.salinas@summitmx.com'])
				->bcc('enedelia.alanis@summitmx.com');
			});
		}

	}

}
