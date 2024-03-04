<?php

namespace sisViaticos\Http\Controllers;

use Illuminate\Http\Request;
use sisViaticos\Http\Requests;
use sisViaticos\DetalleFolio;
use sisViaticos\FirmaAnticipo;
use sisViaticos\FirmaGasto;
use sisViaticos\Transfer;
use sisViaticos\Tipocambio;
use sisViaticos\Folio;
use sisViaticos\Moneda;
use sisViaticos\Status;
use sisViaticos\User;
use sisViaticos\Repayment;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

use DB;
use Carbon\Carbon;
use Mail;

class AutorizadoresController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

     public function index(Request $request)
    {
	    $foliosAnti = DB::table('ssm_viat_firma_anticipo as anti')
	    ->join('ssm_viat_header_folio as folio','anti.id_header_folio','=','folio.id_header_folio')
	    ->join('VIEW_SSM_GET_AUTHORIZERS as auto','anti.id_autorizador','=','auto.TrabajadorID')
	    ->join('users as u','folio.id_solicitante','=','u.id')
	    ->where('anti.status','=','0')
	    ->where('auto.TrabajadorID','=', Auth::user()->numeroNom)
        //->where('auto.division','<>','')
	    ->get();

	    $foliosGasto = DB::table('ssm_viat_firma_gasto as gasto')
	    ->join('ssm_viat_header_folio as folio','gasto.id_header_folio','=','folio.id_header_folio')
	    ->join('VIEW_SSM_GET_AUTHORIZERS as auto','gasto.id_autorizador','=','auto.TrabajadorID')
	    ->join('users as u','folio.id_solicitante','=','u.id')
	    ->where('gasto.status','=','0')
	    ->where('auto.TrabajadorID','=', Auth::user()->numeroNom)
        //->where('auto.division','<>','')
	    ->get();

	    // dd($foliosAnti,$foliosGasto);

        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;

        $company = substr($branch,0,1);                
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

	    return view('authorizers.approbation.index',["foliosAnti"=>$foliosAnti,"foliosGasto"=>$foliosGasto,"userClaims"=>$userClaims]);

    }

    public function evidenciareporte(Request $request)
    {


	    $foliosEvidencia = DB::table('VIEW_SSM_REPORTE_EVIDENCIAS')
	    ->select('id_header_folio', 'company', 'destino', 'proposito', 'fecha_salida', 'fecha_llegada', 'evidencia_viaje', 'pdfevidencia', 'NombreCompleto')
	    ->where('TrabajadorID','=', Auth::user()->numeroNom)
	    // ->get();
        ->paginate(7);

	    // dd($foliosAnti,$foliosGasto);

        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;

        $company = substr($branch,0,1);                
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

	    return view('authorizers.approbation.evidencias',["foliosEvidencia"=>$foliosEvidencia,"userClaims"=>$userClaims ]);

    }

    public function destroy($id)
    {
        $folio=Folio::findOrFail($id);

        if ($folio->id_status<8) 
        {
           $getAutos=FirmaAnticipo::where('id_header_folio','=',$folio->id_header_folio)
            ->where('Id_autorizador','>','0')
            ->where('status','=','1')
            ->get();

            $actualAuto=FirmaAnticipo::where('id_header_folio','=',$folio->id_header_folio)
            ->where('Id_autorizador','>','0')
            ->where('status','=','0')
            ->first();

            $folioMail=DB::table('ssm_viat_header_folio as f')
            ->join('users as u','f.id_solicitante','=','u.id')
            ->join('VIEW_SSM_GET_AUTHORIZERS as a','a.TrabajadorID','=',DB::raw($actualAuto->Id_autorizador))
            ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
            ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','f.proposito', 'f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.name','u.company','f.correo_solicitante as emailU','a.TrabajadorID','a.NombreAuto','a.email as emailA','m.moneda')
            ->where('f.id_header_folio','=',$folio->id_header_folio)
            // ->where('f._token','=',$folio->_token)
            ->first();

            if(count($getAutos)>0)
            {
                foreach ($getAutos as $getAuto) {
                    # code...
                    $id_autorizador=$getAuto->Id_autorizador;

                    $Autorizador=DB::table('VIEW_SSM_GET_AUTHORIZERS')
                    ->where('TrabajadorID','=',$id_autorizador)
                    ->first();

                    try{
                        Mail::Send('mails.replyRequestApprobNo', ['folioMail'=> $folioMail], function($mail) use($folioMail, $Autorizador){
                            $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                            $mail->to($Autorizador->email, $Autorizador->NombreAuto);
                        });
                    } catch (\Exception $e) {
                        logger()->error('Error al enviar el correo electrónico: ' . $e->getMessage());
                    }

                }
            }

            try{
                Mail::Send('mails.replyRequestApprobNo', ['folioMail'=> $folioMail], function($mail) use($folioMail){
                        $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                        $mail->to($folioMail->emailU, $folioMail->name);
                }); 
            } catch (\Exception $e) {
                logger()->error('Error al enviar el correo electrónico: ' . $e->getMessage());
            }  
            
            try{
                $folio=Folio::findOrFail($id);
                
                DB::beginTransaction();                

                $firmasAuto=FirmaAnticipo::where('id_header_folio','=',$folio->id_header_folio)
                ->where('Id_autorizador','>','0')
                ->delete();

                $firmaUser=FirmaAnticipo::where('id_header_folio','=',$folio->id_header_folio)
                ->where('id_user','>','0')
                ->where('status','=','1')
                ->first();
                $firmaUser->status='0';
                $firmaUser->anticipo=null;
                $firmaUser->save();                

                $folio->id_status='1';
                $folio->save();

                DB::commit();

            }catch(\Exception $e)
            {
                DB::rollback();

                Session()->flash('msgOK','Un error ha ocurrido, por favor comuniquese con sistemas');
                return Redirect::to('authorizers/approbation');
            }
            Session()->flash('msgDenegado','Se ha denegado la solicitud de viaje con el Folio No. '.$folio->id_header_folio.' correctamente.');
            return Redirect::to('authorizers/approbation');
            
        }
        else
        {
            $getAutos=FirmaGasto::where('id_header_folio','=',$folio->id_header_folio)
            ->where('id_autorizador','>','0')
            ->where('status','=','1')
            ->get();

            $actualAuto=FirmaGasto::where('id_header_folio','=',$folio->id_header_folio)
            ->where('Id_autorizador','>','0')
            ->where('status','=','0')
            ->first();

            $folioMail=DB::table('ssm_viat_header_folio as f')
            ->join('users as u','f.id_solicitante','=','u.id')
            ->join('VIEW_SSM_GET_AUTHORIZERS as a','a.TrabajadorID','=',DB::raw($actualAuto->Id_autorizador))
            ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
            ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','f.evidencia_viaje','f.pdfevidencia','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.name','u.company','f.correo_solicitante as emailU','a.TrabajadorID','a.NombreAuto','a.email as emailA','m.moneda')
            ->where('f.id_header_folio','=',$folio->id_header_folio)
            ->where('f._token','=',$folio->_token)
            ->first();

            $folioMaill=DB::table('ssm_viat_header_folio as f')
            ->join('users as u','f.id_solicitante','=','u.id')
            ->join('VIEW_SSM_GET_AUTHORIZERS as a','a.TrabajadorID','=',DB::raw($actualAuto->Id_autorizador))
            ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
            ->join('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
            ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','t.montopesos','f.evidencia_viaje','f.pdfevidencia','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.name','u.company','f.correo_solicitante as emailU','a.TrabajadorID','a.NombreAuto','a.email as emailA','m.moneda')
            ->where('f.id_header_folio','=',$folio->id_header_folio)
            ->where('f._token','=',$folio->_token)
            ->first();

            $detalles=DB::table('VIEW_SSM_DETALLE_FOLIO')
            ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'Subtotal', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'comentarios', 'xml', 'pdf' )
            ->where('id_header_folio','=',$id)
            ->get();
    
            //AGREGAR A LOS DETALLES EL IMPORTE EN PESOS, EL TIPO DE MONEDA, PARA PODER DIFERENCIAR ENTRE LOS GASTOS CREADOS 
            $detalle=DB::table('VIEW_SSM_DETALLE_FOLIO_INT')
            ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'moneda','tipomoneda','Subtotal', 'Subtotalint', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'xml', 'pdf', 'comentarios', 'importeint', 'pdfint')
            ->where('id_header_folio','=',$id)
            ->get();

            $tipomoneda=DB::table('ssm_viat_detalle_folio as d')
            ->join('ssm_viat_moneda as m','d.tipomoneda','=','m.id_moneda')
            ->select('d.id_gasto', 'm.moneda')
            ->where('id_header_folio','=',$folio->id_header_folio)
            ->groupBy('d.id_gasto','m.moneda')
            ->get();

            if(count($getAutos)>0)
            {
                foreach ($getAutos as $getAuto) {
                    # code...
                    $id_autorizador=$getAuto->Id_autorizador;

                    $Autorizador=DB::table('VIEW_SSM_GET_AUTHORIZERS')
                    ->where('TrabajadorID','=',$id_autorizador)
                    ->first();

                    try{
                        Mail::Send('mails.replyRequestExpenseNo', ['folioMail'=> $folioMail, 'folioMaill'=> $folioMaill, 'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail, $Autorizador){
                            $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                            $mail->to($Autorizador->email, $Autorizador->NombreAuto);
                        });
                    } catch (\Exception $e) {
                        logger()->error('Error al enviar el correo electrónico: ' . $e->getMessage());
                    }

                }
            }

            try{
                Mail::Send('mails.replyRequestExpenseNo', ['folioMail'=> $folioMail, 'folioMaill'=> $folioMaill, 'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                    $mail->to($folioMail->emailU, $folioMail->name);
                }); 
            } catch (\Exception $e) {
                logger()->error('Error al enviar el correo electrónico: ' . $e->getMessage());
            }

            try{
                DB::beginTransaction();            

                $firmasAuto=FirmaGasto::where('id_header_folio','=',$folio->id_header_folio)
                ->where('id_autorizador','>','0')
                ->delete();

                $firmaUser=FirmaGasto::where('id_header_folio','=',$folio->id_header_folio)
                ->where('id_user','>','0')
                ->where('status','=','1')
                ->first();
                $firmaUser->status='0';
                $firmaUser->gasto=null;
                $firmaUser->save();
                
                $folio->id_status='18';
                $folio->save();

                DB::commit();
                
            }catch(\Exception $e)
            {
                DB::rollback();

                Session()->flash('msgOK','Un error ha ocurrido, por favor comuniquese con sistemas');
                return Redirect::to('authorizers/approbation');
            }

            
            Session()->flash('msgDenegado','Se ha denegado la comprobación del Folio No. '.$folio->id_header_folio.'  correctamente.');
            return Redirect::to('authorizers/approbation');
        }
    }


    public function show($id)
    {
        $ValidaAuto = DB::table('VIEW_SSM_GET_AUTHORIZERS')
        ->where('TrabajadorID','=', Auth::user()->numeroNom)
        ->get();

        // if (count($ValidaAuto)==0) {
        //     # code...
        //     return Redirect('/home');

        // }

        $folio=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','u.name','f.id_status','f.tipo','f.destino','f.evidencia_viaje','f.pdfevidencia','f.eq_computo','f.proposito','f.fecha_salida','f.fecha_llegada','f.dias','criterio','m.moneda','f.anticipo','f.all_total','f._token')
        ->where('f.id_header_folio','=',$id)
        ->first();

        $folioi=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
        ->select('f.id_header_folio','f.fecha','u.name','f.id_status','t.montopesos','f.tipo','f.evidencia_viaje','f.pdfevidencia','f.destino','f.eq_computo','f.proposito','f.fecha_salida','f.fecha_llegada','f.dias','criterio','m.moneda','f.anticipo','f.all_total','f._token')
        ->where('f.id_header_folio','=',$id)
        ->first();

        $detalles=DB::table('VIEW_SSM_DETALLE_FOLIO')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'fecha_factura', 'subtotal', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'comentarios', 'xml', 'pdf' )
        ->where('id_header_folio','=',$id)
        ->get();

        //AGREGAR A LOS DETALLES EL IMPORTE EN PESOS, EL TIPO DE MONEDA, PARA PODER DIFERENCIAR ENTRE LOS GASTOS CREADOS 
        $detalle=DB::table('VIEW_SSM_DETALLE_FOLIO_INT')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'fecha_factura', 'moneda','tipomoneda','subtotal', 'Subtotalint', 'subtotalotro', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'xml', 'pdf', 'comentarios', 'importeint', 'pdfint')
        ->where('id_header_folio','=',$id)
        ->get();

        
        $usernom = Auth::user()->numeroNom;
        $branch = Auth::user()->company;

        $company = substr($branch,0,1);                
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));
        
        return view('authorizers.approbation.show',["userClaims"=>$userClaims, "folio"=>$folio, "folioi"=>$folioi,"detalles"=>$detalles, "detallesint"=>$detalle]);

    }



    public function autorizaAnticipo($id, $token)
    { 	

    	$folio=Folio::where('id_header_folio','=',$id)
        ->where('_token','=',$token)
        ->First();

        $Auto1=$this->Auto1($id, $token);
        $Auto2=$this->Auto2($id, $token);

        if ($folio->id_status=='2')
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

                $this->sendMailStaff($id, $token, $Auto2->TrabajadorID);

                Session()->flash('msgOK','El folio '.$folio->id_header_folio .' ha sido autorizado satisfactoriamente');
                return Redirect::to('authorizers/approbation');                
            }
            else
            {
            	$this->sendMailAuto2($id, $token);

            	$this->replyRequestApprobSi($id, $token, $Auto1->TrabajadorID);
                
            	Session()->flash('msgOK','El folio '.$folio->id_header_folio .' ha sido autorizado satisfactoriamente');

                return Redirect::to('authorizers/approbation');
            }

    	}
    	if ($folio->id_status == '4')
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
  
            Session()->flash('msgOK','El folio '.$folio->id_header_folio .' ha sido autorizado satisfactoriamente');

        	return Redirect::to('authorizers/approbation');
        
        }
    }

    public function autorizaGasto($id, $token)
    {
        $folio=Folio::where('id_header_folio','=',$id)
        ->where('_token','=',$token)
        ->First();

        $Auto1=$this->Auto1($id, $token);
        $Auto2=$this->Auto2($id, $token);

        // dd($folio,$Auto1,$Auto2,$Auto3);

        if ($folio->id_status=='9')
        {
            $folio->id_status='11';
            $folio->update();

            $mytime = Carbon::now('America/Monterrey');

            $firmaAuto1=FirmaGasto::where('id_header_folio','=',$folio->id_header_folio)
            ->where('id_autorizador','>','0')
            ->where('status','=','0')
            ->first();
            $firmaAuto1->status='1';
            $firmaAuto1->gasto=$mytime;
            $firmaAuto1->update();

            if ($Auto1->TrabajadorID==$Auto2->TrabajadorID) 
            {
            
                //Se realiza condicional, para verificar que el la fecha de la autorizacion
                //sea anterior a la fecha del cambio
                $firmaUsfecha=FirmaGasto::where('id_header_folio','=',$folio->id_header_folio)
                ->where('id_user','>','0')
                ->where('status','=','1')
                ->first();

                if($firmaUsfecha->gasto <='2020-06-02')
                {
                    $folio->id_status='15';
                    $folio->update();
                }
                else{
                    $folio->id_status='16';
                    $folio->update();
                }

               $this->sendMailStaffGasto($id, $token, $Auto2->TrabajadorID);

                

                Session()->flash('msgOK','La comprobacion del folio '.$folio->id_header_folio .' ha sido autorizado satisfactoriamente');
                return Redirect::to('authorizers/approbation');
               
            }
            else
            {
                $this->sendMailAuto2Gasto($id, $token);
                $this->replyRequestApprobSiGasto($id, $token, $Auto1->TrabajadorID);

                Session()->flash('msgOK','La comprobacion del folio '.$folio->id_header_folio .' ha sido autorizado satisfactoriamente');
                return Redirect::to('authorizers/approbation');
            }
        }

        if ($folio->id_status=='11') 
        {
            //Se realiza condicional, para verificar que el la fecha de la autorizacion
            //sea anterior a la fecha del cambio
            $firmaUsfecha=FirmaGasto::where('id_header_folio','=',$folio->id_header_folio)
            ->where('id_user','>','0')
            ->where('status','=','1')
            ->first();

            if($firmaUsfecha->gasto <='2020-05-30')
            {
                $folio->id_status='15';
                $folio->update();
            }
            else{
                $folio->id_status='16';
                $folio->update();
            }
            $mytime = Carbon::now('America/Monterrey');

            $firmaAuto2=FirmaGasto::where('id_header_folio','=',$folio->id_header_folio)
            ->where('id_autorizador','>','0')
            ->where('status','=','0')
            ->first();
            $firmaAuto2->status='1';
            $firmaAuto2->gasto=$mytime;
            $firmaAuto2->update();
            
            $this->sendMailStaffGasto($id, $token, $Auto2->TrabajadorID);


            Session()->flash('msgOK','La comprobacion del folio '.$folio->id_header_folio .' ha sido autorizado satisfactoriamente');
            return Redirect::to('authorizers/approbation');
        }
    }

    public function Auto1($id, $token)
    {
        $Autorizador=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','f.correo_auto1','=','a.email')
        ->select('u.name','u.company','a.TrabajadorID','a.email')
        ->where('f.id_header_folio','=',$id)
        ->where('_token','=',$token)
        ->first();

        return $Autorizador;
    }

    public function Auto2($id, $token)
    {
        $Autorizador=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','f.correo_auto2','=','a.email')
        ->select('u.name','u.company','a.TrabajadorID','a.email')
        ->where('f.id_header_folio','=',$id)
        ->where('_token','=',$token)
        ->first();

        return $Autorizador;
    }


    public function sendMailAuto2($id ,$token)
    {
        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','f.correo_auto2','=','a.email')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.anticipo','m.moneda','u.name','f.correo_solicitante as emailU','u.company','a.TrabajadorID','a.email as emailA','f._token')
        ->where('f.id_header_folio','=',$id)
        ->where('_token','=',$token)
        ->first();

        $firma=DB::table('ssm_viat_firma_anticipo')
        ->select(DB::raw('count( id_anticipo ) as anticipo'))
        ->where('id_header_folio','=',$id)
        ->where('id_autorizador','=',$folioMail->TrabajadorID)
        ->first();

        if ($firma->anticipo == 0)
        {
            $folioMail=DB::table('ssm_viat_header_folio as f')
            ->join('users as u','f.id_solicitante','=','u.id')
            ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
            ->join('VIEW_SSM_GET_AUTHORIZERS as a','f.correo_auto2','=','a.email')
            ->select('f.id_header_folio','f.fecha','f.tipo','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.anticipo','m.moneda','u.name','f.correo_solicitante as emailU','u.company','a.TrabajadorID','a.email as emailA','f._token')
            ->where('f.id_header_folio','=',$id)
            ->where('_token','=',$token)
            ->first();
    
            $firmaAuto = new FirmaAnticipo;
            $firmaAuto->company=$folioMail->company;
            $firmaAuto->id_autorizador=$folioMail->TrabajadorID;
            $firmaAuto->id_header_folio=$folioMail->id_header_folio;
            $firmaAuto->status='0';
            $firmaAuto->save();
        }
        else{

        }

        try{

            Mail::Send('mails.requestApprobAuto2', ['folioMail'=> $folioMail], function($mail) use($folioMail){
                $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                $mail->to($folioMail->emailA);
            });
        } catch (\Exception $e) {
            logger()->error('Error al enviar el correo electrónico: ' . $e->getMessage());
        }       
    }

    public function replyRequestApprobSi($id, $token, $TrabajadorID)
    {
        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','a.TrabajadorID','=',DB::raw($TrabajadorID))
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f._token','u.name','u.company','f.correo_solicitante as emailU','a.TrabajadorID','a.NombreAuto','a.email as emailA','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->first();

        Mail::Send('mails.replyRequestApprobSi', ['folioMail'=> $folioMail], function($mail) use($folioMail){
            $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
            $mail->to($folioMail->emailU, $folioMail->name);
        });     
    }

    public function sendMailStaff($id ,$token, $TrabajadorID)
    {
        $folio=Folio::where('id_header_folio','=',$id)
        ->where('_token','=',$token)
        ->First();
        
        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_INFO_USERS as p','u.numeroNom','=','p.TrabajadorID')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','a.TrabajadorID','=',DB::raw($TrabajadorID))
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','f.eq_computo','f.proposito','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f._token','u.id','u.name','u.company','f.correo_solicitante as emailU','p.BancoCuenta','p.CLABE','p.Banco','a.TrabajadorID','a.NombreAuto','a.email as emailA','m.moneda')
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

        ///////--------RESERVACION DE VUELOS Y HOTEL QRO-----////////////
        $data3['QROnameBuyF'] = 'RESERVACIONES Y VUELOS QRO';
        $data3['QROemailBuyF'] = 'vanessa.gonzalez@summitmx.com';
        $data3['CCQROemailBuyF'] = 'alejandra.trujillo@summitmx.com';

        /////////---------CORREO A RH SLM----------///////////////////
        $data3['SLMnameHR'] = 'RECURSOS HUMANOS SLM';
        $data3['SLMemailHR'] = 'reyna.soto@summitmx.com';
        $data3['CC1SLMemailHR'] = 'francisco.peguero@summitmx.com';
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
                ->cc( $data3['CC2QROemailHR']);
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
    }

    public function sendMailStaffGasto($id ,$token, $TrabajadorID)
    {
        
        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','a.TrabajadorID','=',DB::raw($TrabajadorID))
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','f.proposito','f.evidencia_viaje','f.pdfevidencia','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.name','u.company','f.correo_solicitante as emailU','a.TrabajadorID','a.NombreAuto','a.email as emailA','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('f._token','=',$token)
        ->first();

        $folioMaill=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','a.TrabajadorID','=',DB::raw($TrabajadorID))
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino', 't.montopesos','f.evidencia_viaje','f.pdfevidencia','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.name','u.company','f.correo_solicitante as emailU','a.TrabajadorID','a.NombreAuto','a.email as emailA','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('f._token','=',$token)
        ->first();
        

        $detalles=DB::table('VIEW_SSM_DETALLE_FOLIO')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'Subtotal', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'comentarios', 'xml', 'pdf' )
        ->where('id_header_folio','=',$id)
        ->get();

        //AGREGAR A LOS DETALLES EL IMPORTE EN PESOS, EL TIPO DE MONEDA, PARA PODER DIFERENCIAR ENTRE LOS GASTOS CREADOS 
        $detalle=DB::table('VIEW_SSM_DETALLE_FOLIO_INT')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'moneda','tipomoneda','Subtotal', 'Subtotalint', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'xml', 'pdf', 'comentarios', 'importeint', 'pdfint')
        ->where('id_header_folio','=',$id)
        ->get();

        $tipomoneda=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_moneda as m','d.tipomoneda','=','m.id_moneda')
        ->select('d.id_gasto', 'm.moneda')
        ->where('id_header_folio','=',$id)
        ->groupBy('d.id_gasto','m.moneda')
        ->get();

        $data3['MTYemailCxP'] = 'jorge.garcia@summitmx.com';
        $data3['MTYnameCxP'] = 'CUENTAS X PAGAR MTY';
        $data3['CCMTYemailCxP'] = 'angel.fuentes@summitmx.com';
        
        $data3['QROemailCxP'] = 'coral.mederos@summitmx.com';
        $data3['QROnameCxP'] = 'CUENTAS X PAGAR QRO';
        $data3['CCQROemailCxP'] = 'pablo.resendiz@summitmx.com';
        
        $data3['SLMemailCxP'] = 'coral.mederos@summitmx.com';
        $data3['SLMnameCxP'] = 'CUENTAS X PAGAR SLM';
        $data3['CCSLMemailCxP'] = 'pablo.resendiz@summitmx.com';
        
        $data3['emailAdvT'] = 'gerardo.castro@summitmx.com';
        $data3['nameAdvT'] = 'GERARDO CASTRO';

        Mail::Send('mails.replyRequestExpenseSi', ['folioMail'=> $folioMail, 'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail){
            $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
            $mail->to($folioMail->emailU, $folioMail->name);
        });

        if($folioMail->id_status == '16'){
            $this->sendMailStaffRembolso($id, $token);
        }
        else{
            if ($folioMail->company=='MTY') {
                            
                Mail::Send('mails.advanceTransferExpenses', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail, $data3){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                    $mail->to($data3['MTYemailCxP'], $data3['MTYnameCxP'])
                    ->cc($data3['CCMTYemailCxP']);
                });
            }
            if ($folioMail->company=='QRO') {
            
                Mail::Send('mails.advanceTransferExpenses', ['folioMail'=> $folioMail,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail, $data3){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                    $mail->to($data3['QROemailCxP'], $data3['QROnameCxP'])
                    ->cc($data3['CCQROemailCxP'], 'laura.rosas@summitmx.com', 'pablo.resendiz@summitmx.com');
                });
            }
            if($folioMail->company=='SLM'){

                Mail::Send('mails.advanceTransferExpenses', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail, $data3){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                    $mail->to($data3['SLMemailCxP'], $data3['SLMnameCxP'])
                    ->cc($data3['CCSLMemailCxP'], 'laura.rosas@summitmx.com', 'pablo.resendiz@summitmx.com');
                });
            }
        }
    }

    public function sendMailStaffRembolso($id ,$token)
    {
        
        $folio=Folio::where('_token','=',$token)
        ->where('id_header_folio','=', $id)
        ->first();
        
        
        $folioPendiente=DB::table('ssm_viat_header_folio as f')
        ->join('ssm_viat_transfer as t','f.id_header_folio','=','t.id_header_folio')
        ->select('f.*','t.company as Com','t.id_transfer','t.id_header_folio as IDFOLIO','t.deposito')
        ->where('f.company','=',$folio->company)
        ->whereBetween('f.id_status',[2,15,19])
        ->where('f.id_solicitante','=',$folio->id_solicitante)
        ->where('f.id_header_folio','!=',$folio->id_header_folio)
        ->where('t.deposito','=','0')
        ->orderBy('fecha_salida','asc')
        ->limit(1)
        ->get();

        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_INFO_USERS as p','u.numeroNom','=','p.TrabajadorID')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','f.proposito','f.evidencia_viaje','f.pdfevidencia','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.id','u.name','u.company','f.correo_solicitante as emailU','p.BancoCuenta','p.CLABE','p.Banco','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('p.compania','LIKE','%'.$folio->company.'%')
        ->where('f._token','=',$token)
        ->first();

        $folioMaill=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_INFO_USERS as p','u.numeroNom','=','p.TrabajadorID')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','t.montopesos','f.destino','f.evidencia_viaje','f.pdfevidencia','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.id','u.name','u.company','f.correo_solicitante as emailU','p.BancoCuenta','p.CLABE','p.Banco','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('p.compania','LIKE','%'.$folio->company.'%')
        ->where('f._token','=',$token)
        ->first();

        $detalles=DB::table('VIEW_SSM_DETALLE_FOLIO')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'Subtotal', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'comentarios', 'xml', 'pdf' )
        ->where('id_header_folio','=',$id)
        ->get();

        //AGREGAR A LOS DETALLES EL IMPORTE EN PESOS, EL TIPO DE MONEDA, PARA PODER DIFERENCIAR ENTRE LOS GASTOS CREADOS 
        $detalle=DB::table('VIEW_SSM_DETALLE_FOLIO_INT')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'moneda','tipomoneda','Subtotal', 'Subtotalint', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'xml', 'pdf', 'comentarios', 'importeint', 'pdfint')
        ->where('id_header_folio','=',$id)
        ->get();

        $tipomoneda=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_moneda as m','d.tipomoneda','=','m.id_moneda')
        ->select('d.id_gasto', 'm.moneda')
        ->where('id_header_folio','=',$id)
        ->groupBy('d.id_gasto','m.moneda')
        ->get();

        if(count($folioPendiente)>0)
        {
            foreach ($folioPendiente as $folioPend) {
                 # code...
                $folioID = $folioPend->id_header_folio;
                $folioCompany = $folioPend->company;
                $folioToken = $folioPend->_token;

            }

            $trans=Transfer::where('id_header_folio','=',$folioID)->first();

            $folioUpdate=Folio::where('id_header_folio','=',$folioID)
            ->where('company','=',$folioCompany)
            ->where('_token','=',$folioToken)
            ->first();

            try{
                DB::beginTransaction();

                $trans->deposito='1';
                $trans->save();

                DB::commit();

            }catch(\Exception $e)
            {
               
            }   

            Mail::Send('mails.preTransferCXC', ['folioMail'=> $folioMail,'detalles'=>$detalles, 'detalle'=>$detalle,'folioUpdate'=>$folioUpdate], function($mail) use($folioMail){
                $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                $mail->to('gerardo.castro@summitmx.com', 'GERARDO CASTRO','coral.mederos@summitmx.com')
                // ->cc('coral.mederos@summitmx.com')
                ->cc($folioMail->emailU);
                // $mail->to('gerardo.castro@summitmx.com', 'GERARDO CASTRO')
                // ->cc($folioMail->emailU);
            });

        }

        if($folio->tipo =='Nacional' ||$folio->id_moneda == 1)
        {
            if ($folio->anticipo < $folio->all_total) 
            {
                $rembolso = new Repayment;
                $rembolso->company=$folioMail->company;
                $rembolso->id_header_folio=$folioMail->id_header_folio;
                $rembolso->rembolso='0';
                $rembolso->save();

                Mail::Send('mails.repayment', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                    $mail->to('gerardo.castro@summitmx.com', 'GERARDO CASTRO','coral.mederos@summitmx.com')
                    // ->cc('coral.mederos@summitmx.com')
                    ->cc($folioMail->emailU);
                    // $mail->to('gerardo.castro@summitmx.com', 'GERARDO CASTRO')
                    // ->cc($folioMail->emailU);
                    
                });
                
            }
            else
            {
                Mail::Send('mails.repaymentUser', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                    $mail->to($folioMail->emailU, $folioMail->name)
                    ->cc('gerardo.castro@summitmx.com');
                    // ->cc('gerardo.castro@summitmx.com');
                    
                    
                });
            }
           
        }
        else{
            if ($folioMaill->montopesos < $folioMaill->all_total) 
            {
                $rembolso = new Repayment;
                $rembolso->company=$folioMail->company;
                $rembolso->id_header_folio=$folioMail->id_header_folio;
                $rembolso->rembolso='0';
                $rembolso->save();

                Mail::Send('mails.repayment', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                    $mail->to('gerardo.castro@summitmx.com', 'GERARDO CASTRO','coral.mederos@summitmx.com')
                    // ->cc('gerardo.castro@summitmx.com')
                    ->cc($folioMail->emailU);
                    // $mail->to('gerardo.castro@summitmx.com', 'GERARDO CASTRO')
                    // ->cc($folioMail->emailU);
                    
                });
                
            }
            else
            {
                Mail::Send('mails.repaymentUser', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                    $mail->to($folioMail->emailU, $folioMail->name)
                    ->cc('gerardo.castro@summitmx.com');
                    // ->cc('gerardo.castro@summitmx.com');
                    
                    
                });
            }
            
        }
    }

    public function sendMailAuto2Gasto($id ,$token)
    {
        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','f.correo_auto2','=','a.email')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.destino','f.proposito','f.eq_computo','f.evidencia_viaje','f.pdfevidencia','f.fecha_salida','f.fecha_llegada','f.anticipo','f.all_total','m.moneda','u.name','f.correo_solicitante as emailU','u.company','a.TrabajadorID','a.NombreAuto','a.email as emailA','f._token')
        ->where('f.id_header_folio','=',$id)
        ->where('_token','=',$token)
        ->first();

        $folioMaill=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','f.correo_auto2','=','a.email')
        ->join('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.destino','f.proposito','f.eq_computo','f.evidencia_viaje','f.pdfevidencia','t.montopesos','f.fecha_salida','f.fecha_llegada','f.anticipo','f.all_total','m.moneda','u.name','f.correo_solicitante as emailU','u.company','a.TrabajadorID','a.NombreAuto','a.email as emailA','f._token')
        ->where('f.id_header_folio','=',$id)
        ->where('_token','=',$token)
        ->first();

        $detalles=DB::table('VIEW_SSM_DETALLE_FOLIO')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'Subtotal', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'comentarios', 'xml', 'pdf' )
        ->where('id_header_folio','=',$id)
        ->get();

        //AGREGAR A LOS DETALLES EL IMPORTE EN PESOS, EL TIPO DE MONEDA, PARA PODER DIFERENCIAR ENTRE LOS GASTOS CREADOS 
        $detalle=DB::table('VIEW_SSM_DETALLE_FOLIO_INT')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'moneda','tipomoneda','Subtotal', 'Subtotalint', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'xml', 'pdf', 'comentarios', 'importeint', 'pdfint')
        ->where('id_header_folio','=',$id)
        ->get();

        $tipomoneda=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_moneda as m','d.tipomoneda','=','m.id_moneda')
        ->select('d.id_gasto', 'm.moneda')
        ->where('id_header_folio','=',$id)
        ->groupBy('d.id_gasto','m.moneda')
        ->get();

        $firma=DB::table('ssm_viat_firma_gasto')
        ->select(DB::raw('count( id_gasto ) as gasto'))
        ->where('id_header_folio','=',$id)
        ->where('id_autorizador','=',$folioMail->TrabajadorID)
        ->first();

        if ($firma->gasto == 0)
        {
            $folioMai=DB::table('ssm_viat_header_folio as f')
            ->join('users as u','f.id_solicitante','=','u.id')
            ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
            ->join('VIEW_SSM_GET_AUTHORIZERS as a','f.correo_auto2','=','a.email')
            ->select('f.id_header_folio','f.fecha','f.tipo','f.destino','f.proposito','f.evidencia_viaje','f.pdfevidencia','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.anticipo','f.all_total','m.moneda','u.name','f.correo_solicitante as emailU','u.company','a.TrabajadorID','a.email as emailA','f._token')
            ->where('f.id_header_folio','=',$id)
            ->where('_token','=',$token)
            ->first();
    
            $firmaAuto = new FirmaGasto;
            $firmaAuto->company=$folioMail->company;
            $firmaAuto->id_autorizador=$folioMail->TrabajadorID;
            $firmaAuto->id_header_folio=$folioMail->id_header_folio;
            $firmaAuto->status='0';
            $firmaAuto->save();
        }
        else{

    }

        Mail::Send('mails.requestExpenseAuto2', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail){
            $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
            $mail->to($folioMail->emailA);
        });
    }

    public function replyRequestApprobSiGasto($id, $token, $TrabajadorID)
    {
        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','a.TrabajadorID','=',DB::raw($TrabajadorID))
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','f.proposito','f.evidencia_viaje','f.pdfevidencia','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.name','u.company','f.correo_solicitante as emailU','a.TrabajadorID','a.email as emailA','a.NombreAuto','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('f._token','=',$token)
        ->first();

        $folioMaill=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','a.TrabajadorID','=',DB::raw($TrabajadorID))
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','t.montopesos','f.evidencia_viaje','f.pdfevidencia','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.name','u.company','f.correo_solicitante as emailU','a.TrabajadorID','a.email as emailA','a.NombreAuto','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('f._token','=',$token)
        ->first();

        $detalles=DB::table('VIEW_SSM_DETALLE_FOLIO')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'Subtotal', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'comentarios', 'xml', 'pdf' )
        ->where('id_header_folio','=',$id)
        ->get();

        //AGREGAR A LOS DETALLES EL IMPORTE EN PESOS, EL TIPO DE MONEDA, PARA PODER DIFERENCIAR ENTRE LOS GASTOS CREADOS 
        $detalle=DB::table('VIEW_SSM_DETALLE_FOLIO_INT')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'moneda','tipomoneda','Subtotal', 'Subtotalint', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'xml', 'pdf', 'comentarios', 'importeint', 'pdfint')
        ->where('id_header_folio','=',$id)
        ->get();

        $tipomoneda=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_moneda as m','d.tipomoneda','=','m.id_moneda')
        ->select('d.id_gasto', 'm.moneda')
        ->where('id_header_folio','=',$id)
        ->groupBy('d.id_gasto','m.moneda')
        ->get();

        Mail::Send('mails.replyRequestExpenseSi', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail){
            $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
            $mail->to($folioMail->emailU, $folioMail->name);
        });
    }

    public function edit($id)
    {

        $folio=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','f.id_solicitante','f.id_status','u.name','u.numeroNom','u.company','u.id','tipo','f.destino','f.proposito','f.fecha_salida','f.fecha_llegada','f.dias','criterio','m.moneda','f.anticipo','f.all_total')
        ->where('f.id_header_folio','=',$id)
        ->first();

        $folioi=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->leftJoin('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
        ->select('f.id_header_folio','f.fecha','f.id_solicitante','t.montopesos','f.id_status','u.name','u.id', 'u.numeroNom', 'u.company','tipo','f.destino','f.proposito','f.fecha_salida','f.fecha_llegada','f.dias','criterio','m.moneda','f.anticipo','f.all_total')
        ->where('f.id_header_folio','=',$id)
        ->first();

        // $profile=DB::table('user_profile')->where('id_user','=',$folio->id_solicitante)->first();

        $profiles=DB::table('VIEW_SSM_INFO_USERS')->where('TrabajadorID','=',$folio->numeroNom)->where('compania','LIKE','%'.$folio->company.'%')->where('NombreCompleto','LIKE','%'.$folio->name.'%')->first();
        
        $detalles=DB::table('VIEW_SSM_DETALLE_FOLIO')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'subtotal', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'comentarios', 'xml', 'pdf' )
        ->where('id_header_folio','=',$id)
        ->get();

        //AGREGAR A LOS DETALLES EL IMPORTE EN PESOS, EL TIPO DE MONEDA, PARA PODER DIFERENCIAR ENTRE LOS GASTOS CREADOS 
        $detalle=DB::table('VIEW_SSM_DETALLE_FOLIO_INT')
        ->select('id_header_folio','id_gasto','nomGasto','metodoPago', 'moneda','tipomoneda','subtotal', 'Subtotalint', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'xml', 'pdf', 'comentarios', 'importeint', 'pdfint')
        ->where('id_header_folio','=',$id)
        ->get();
        

        //TIPO DE MONEDA

        $tipomoneda=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_moneda as m','d.tipomoneda','=','m.id_moneda')
        ->select('d.id_gasto', 'm.moneda')
        ->where('id_header_folio','=',$id)
        ->groupBy('d.id_gasto','m.moneda')
        ->get();

        $UserAnticipo=DB::table('ssm_viat_firma_anticipo as s')
        ->join('users as u','s.id_user','=','u.id')
        ->select('s.id_anticipo', 's.id_header_folio', 'u.id', 's.anticipo', 'u.name')
        ->where('s.id_header_folio','=', $id)
        ->whereNotNull('s.id_user')
        ->whereNotNull('s.anticipo')
        ->orderBy('s.anticipo','desc')
        ->take(1)
        ->get();

         $UserGasto=DB::table('ssm_viat_firma_gasto as s')
        ->join('users as u','s.id_user','=','u.id')
        ->select('s.id_gasto', 's.id_header_folio', 'u.id', 's.gasto', 'u.name')
        ->where('s.id_header_folio','=', $id)
        ->whereNotNull('s.id_user')
        ->whereNotNull('s.gasto')
        ->orderBy('s.gasto','desc')
        ->take(1)
        ->get();

        $Auto1Anticipo=DB::table('ssm_viat_firma_anticipo as s')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','s.id_autorizador','=','a.TrabajadorID')
        ->join('ssm_viat_header_folio as f', 'a.email', '=', 'f.correo_auto1')
        ->select('s.id_anticipo', 's.id_header_folio', 's.id_autorizador', 's.anticipo', 'a.email', 'a.NombreCompleto')
        ->where('s.id_header_folio','=', $id)
        ->whereNotNull('s.anticipo')
        ->whereNotNull('a.email')
        ->orderBy('s.anticipo','asc')
        ->take(1)
        ->get();

        $Auto1Gasto=DB::table('ssm_viat_firma_gasto as s')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','s.id_autorizador','=','a.TrabajadorID')
        ->join('ssm_viat_header_folio as f', 'a.email', '=', 'f.correo_auto1')
        ->select('s.id_gasto', 's.id_header_folio', 's.id_autorizador', 's.gasto', 'a.email','a.NombreCompleto')
        ->where('s.id_header_folio','=', $id)
        ->whereNotNull('s.gasto')
        ->whereNotNull('a.email')
        ->orderBy('s.gasto','asc')
        ->take(1)
        ->get();

        $Auto2Anticipo=DB::table('ssm_viat_firma_anticipo as s')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','s.id_autorizador','=','a.TrabajadorID')
        ->join('ssm_viat_header_folio as f', 'a.email', '=', 'f.correo_auto2')
        ->select('s.id_anticipo', 's.id_header_folio', 's.id_autorizador', 's.anticipo', 'a.email','a.NombreCompleto')
        ->where('s.id_header_folio','=', $id)
        ->whereNotNull('s.anticipo')
        ->orderBy('s.anticipo','desc')
        ->take(1)
        ->get();

        $Auto2Gasto=DB::table('ssm_viat_firma_gasto as s')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','s.id_autorizador','=','a.TrabajadorID')
        ->join('ssm_viat_header_folio as f', 'a.email', '=', 'f.correo_auto2')
        ->select('s.id_gasto', 's.id_header_folio', 's.id_autorizador', 's.gasto', 'a.email','a.NombreCompleto')
        ->where('s.id_header_folio','=', $id)
        ->whereNotNull('s.gasto')
        ->orderBy('s.gasto','desc')
        ->take(1)
        ->get();

        $Auto3Anticipo=DB::table('ssm_viat_firma_anticipo as s')
        ->join('ssm_viat_autorizadores as a','s.id_autorizador','=','a.id_autorizador')
        ->select('s.id_anticipo', 's.id_header_folio', 's.id_autorizador', 's.anticipo', 'a.autorizador')
        ->where('s.id_header_folio','=', $id)
        ->whereNotNull('s.anticipo')
        ->whereIn('s.id_autorizador',['5', '19'])
        ->orderBy('s.anticipo','desc')
        ->take(1)
        ->get();

        $Auto3Gasto=DB::table('ssm_viat_firma_gasto as s')
        ->join('ssm_viat_autorizadores as a','s.id_autorizador','=','a.id_autorizador')
        ->select('s.id_gasto', 's.id_header_folio', 's.id_autorizador', 's.gasto', 'a.autorizador')
        ->where('s.id_header_folio','=', $id)
        ->whereNotNull('s.gasto')
        ->whereIn('s.id_autorizador',['5', '19'])
        ->orderBy('s.gasto','desc')
        ->take(1)
        ->get();
        
        $view = view ('pdf.travel.folio',compact('folio','folioi','profiles','detalles','detalle', 'tipomoneda','Auto1Anticipo','Auto1Gasto','Auto2Anticipo','Auto2Gasto','Auto3Anticipo','Auto3Gasto','UserAnticipo','UserGasto'));
        
        
        $pdf = \App::make('dompdf.wrapper');
        $pdf->LoadHTML($view);
        return $pdf->stream('foliopdf-'.$folio->id_header_folio.'.pdf');
        
        #return view("pdf/travel/folio",['folio'=>$folio]);

    }
}
