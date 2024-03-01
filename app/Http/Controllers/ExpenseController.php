<?php

namespace sisViaticos\Http\Controllers;

use Illuminate\Http\Request;
use sisViaticos\Folio;
use sisViaticos\DetalleFolio;
use sisViaticos\FirmaFolio;
use sisViaticos\FirmaGasto;
use sisViaticos\Repayment;
use sisViaticos\Transfer;
use sisViaticos\Moneda;
use sisViaticos\Status;
use sisViaticos\User;
use Illuminate\Support\Facades\Redirect;
use sisViaticos\Http\Requests\FolioFormRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Mail;

use Session;
use DB;
use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;

class ExpenseController extends Controller
{
    public function __construct()
    {

    }

    public function sendcxp($id, $token)
    {
        //Condicional para poder enviar correo a cuentas por pagar
        $folioUpdate=Folio::findOrFail($id);
        if($folioUpdate->id_status<'15' || $folioUpdate->id_status=='21'|| $folioUpdate->id_status=='18'){
            if($folioUpdate->anticipo>0){
                if($folioUpdate->anticipo == $folioUpdate->all_total or $folioUpdate->anticipo < $folioUpdate->all_total){
                    $folioUpdate->id_status='19';
                    $folioUpdate->update();

                    $mytime = Carbon::now('America/Monterrey');

                    $firmaUser=FirmaGasto::where('id_header_folio','=',$folioUpdate->id_header_folio)
                    ->where('id_user','=',Auth::user()->id)
                    ->first();
                    $firmaUser->status='2';
                    $firmaUser->gasto=$mytime;
                    $firmaUser->update();

                    //Envia el correo a cuentas por pagar
                    $this->sendMailCxP($id, $token);

                    return Redirect::to('travel/gasto');
                }
                else{
                    $folio=DB::table('ssm_viat_header_folio as f')
                    ->join('users as u','f.id_solicitante','=','u.id')
                    ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
                    ->select('f.id_header_folio','f.fecha','u.name','tipo','f.destino','f.proposito', 'f.eq_computo','f.evidencia_viaje','f.pdfevidencia','f.fecha_salida','f.fecha_llegada','f.dias','criterio','m.moneda','f.anticipo','f.all_total','f._token')
                    ->where('f.id_header_folio','=',$id)
                    ->where('f.id_solicitante','=', Auth::user()->id)
                    ->where('f.company','=',Auth::user()->company)
                    ->first();

                    $folioi=DB::table('ssm_viat_header_folio as f')
                    ->join('users as u','f.id_solicitante','=','u.id')
                    ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
                    ->join('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
                    ->select('f.id_header_folio','f.fecha','u.name','tipo','f.destino','f.proposito', 'f.eq_computo','f.evidencia_viaje','f.pdfevidencia','f.fecha_salida','f.fecha_llegada','f.dias','criterio','m.moneda','f.anticipo', 't.montopesos','f.all_total','f._token')
                    ->where('f.id_header_folio','=',$id)
                    ->where('f.id_solicitante','=', Auth::user()->id)
                    ->where('f.company','=',Auth::user()->company)
                    ->first();

                    $detalles=DB::table('VIEW_SSM_DETALLE_FOLIO')
                    ->select('id_detalle_folio', 'id_header_folio','id_gasto','nomGasto','metodoPago', 'subtotal', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'comentarios', 'xml', 'pdf', 'fecha_factura' )
                    ->where('id_header_folio','=',$id)
                    ->get();
            
                    //AGREGAR A LOS DETALLES EL IMPORTE EN PESOS, EL TIPO DE MONEDA, PARA PODER DIFERENCIAR ENTRE LOS GASTOS CREADOS             
                    $detallesint=DB::table('VIEW_SSM_DETALLE_FOLIO_INT')
                    ->select('id_detalle_folio', 'id_header_folio','id_gasto','nomGasto','metodoPago', 'moneda','tipomoneda','subtotal', 'Subtotalint', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'xml', 'pdf', 'comentarios', 'importeint', 'pdfint', 'fecha_factura', 'subtotalotro')
                    ->where('id_header_folio','=',$id)
                    ->get();

                    $usernom = Auth::user()->numeroNom;
                    $branch = Auth::user()->company;
            
                    $company = substr($branch,0,1);                
                    $valuesUser = $company.$usernom;
            
                    $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

                    Session()->flash('msg','Lo sentimos pero el anticipo no coincide con lo comprobado, favor de verificarlo.');

                    return view("travel.gasto.confirm",["userClaims"=>$userClaims, "folio"=>$folio, "folioi"=>$folioi, "detalles"=>$detalles, "detallesint"=>$detallesint]);
                }
            }
            if ($folioUpdate->anticipo==0) 
            {        
                $folioUpdate->id_status='19';
                $folioUpdate->update();

                $mytime = Carbon::now('America/Monterrey');

                $firmaUser=FirmaGasto::where('id_header_folio','=',$folioUpdate->id_header_folio)
                ->where('id_user','=',Auth::user()->id)
                ->first();
                $firmaUser->status='2';
                $firmaUser->gasto=$mytime;
                $firmaUser->update();

                $this->sendMailCxP($id, $token);
                return Redirect::to('travel/gasto');
            }
        }
    }
    
    public function sendAuto($id ,$token)
    {
        $folioUpdate=Folio::findOrFail($id);

        if ($folioUpdate->id_status<'11') {
            
            if ($folioUpdate->anticipo>0) {
                # Condicional donde se revisa si lo comprobado es igual o mayor al anticipo
                if ($folioUpdate->anticipo == $folioUpdate->all_total OR $folioUpdate->anticipo < $folioUpdate->all_total) {
                    # code...
                    $folioUpdate->id_status='9';
                    $folioUpdate->update();

                    $mytime = Carbon::now('America/Monterrey');

                    $firmaUser=FirmaGasto::where('id_header_folio','=',$folioUpdate->id_header_folio)
                    ->where('id_user','=',Auth::user()->id)
                    ->first();
                    $firmaUser->status='1';
                    $firmaUser->gasto=$mytime;
                    $firmaUser->update();

                    $this->sendMailAuto1($id, $token);

                    return Redirect::to('travel/gasto');
                }
                else
                {
                    $folio=DB::table('ssm_viat_header_folio as f')
                    ->join('users as u','f.id_solicitante','=','u.id')
                    ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
                    ->select('f.id_header_folio','f.fecha','u.name','tipo','f.destino','f.proposito','f.evidencia_viaje','f.pdfevidencia','f.fecha_salida','f.fecha_llegada','f.dias','criterio','m.moneda','f.anticipo','f.all_total','f._token')
                    ->where('f.id_header_folio','=',$id)
                    ->where('f.id_solicitante','=', Auth::user()->id)
                    ->where('f.company','=',Auth::user()->company)
                    ->first();

                    $folioi=DB::table('ssm_viat_header_folio as f')
                    ->join('users as u','f.id_solicitante','=','u.id')
                    ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
                    ->join('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
                    ->select('f.id_header_folio','f.fecha','u.name','tipo','f.destino','f.proposito','f.evidencia_viaje','f.pdfevidencia','f.fecha_salida','f.fecha_llegada','f.dias','criterio','m.moneda','f.anticipo', 't.montopesos','f.all_total','f._token')
                    ->where('f.id_header_folio','=',$id)
                    ->where('f.id_solicitante','=', Auth::user()->id)
                    ->where('f.company','=',Auth::user()->company)
                    ->first();

                    $detalles=DB::table('VIEW_SSM_DETALLE_FOLIO')
                    ->select('id_detalle_folio', 'id_header_folio','id_gasto','nomGasto','metodoPago', 'subtotal', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'comentarios', 'xml', 'pdf', 'fecha_factura' )
                    ->where('id_header_folio','=',$id)
                    ->get();
            
                    //AGREGAR A LOS DETALLES EL IMPORTE EN PESOS, EL TIPO DE MONEDA, PARA PODER DIFERENCIAR ENTRE LOS GASTOS CREADOS 
            
                    $detallesint=DB::table('VIEW_SSM_DETALLE_FOLIO_INT')
                    ->select('id_detalle_folio', 'id_header_folio','id_gasto','nomGasto','metodoPago', 'moneda','tipomoneda','subtotal', 'Subtotalint', 'proveedor', 'RFC', 'noFactura', 'id_cuenta', 'importe', 'IVA', 'otro_impuesto', 'xml', 'pdf', 'comentarios', 'importeint', 'pdfint', 'fecha_factura', 'subtotalotro')
                    ->where('id_header_folio','=',$id)
                    ->get();

                    Session()->flash('msg','Lo sentimos pero el anticipo no coincide con lo comprobado, favor de verificarlo.');

                    return view("travel.gasto.confirm",["folio"=>$folio, "folioi"=>$folioi, "detalles"=>$detalles, "detallesint"=>$detallesint]);
                }
            }
            if ($folioUpdate->anticipo==0) 
            {        
                $folioUpdate->id_status='9';
                $folioUpdate->update();

                $mytime = Carbon::now('America/Monterrey');

                $firmaUser=FirmaGasto::where('id_header_folio','=',$folioUpdate->id_header_folio)
                ->where('id_user','=',Auth::user()->id)
                ->first();
                $firmaUser->status='1';
                $firmaUser->gasto=$mytime;
                $firmaUser->update();

                $this->sendMailAuto1($id, $token);
                return Redirect::to('travel/gasto');
            }
        }
        //Envia la solicitud cuando esta a sido denagada por Auto2
        if ($folioUpdate->id_status=='12')
        {
            $this->sendMailAuto2($id, $token);

            return Redirect::to('travel/gasto');
        }

        if ($folioUpdate->id_status=='18')
        {
            $folioUpdate->id_status='9';
            $folioUpdate->update();

            $mytime = Carbon::now('America/Monterrey');
            $firmaUser=FirmaGasto::where('id_header_folio','=',$folioUpdate->id_header_folio)
            ->where('id_user','=',Auth::user()->id)
            ->first();
            $firmaUser->status='1';
            $firmaUser->gasto=$mytime;
            $firmaUser->update();

            $this->sendMailAuto1($id, $token);

            return Redirect::to('travel/gasto');
        }
    }

    public function getAuto1($token, $id, $option)
    {
        $folio=Folio::where('_token','=',$token)
        ->where('id_header_folio','=', $id)
        ->first();

        if ($folio->id_status<'11')
        {
            $Auto1=$this->Auto1($id, $token);
            $Auto2=$this->Auto2($id, $token);

            if($option=='si')
            {
                $folio->id_status='11';
                $folio->update();

                //Regista en la tabla firma_folio el auto, fecha y hora de la aprobacion.
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
                    $this->sendMailStaff($id, $token, $Auto2->TrabajadorID);
                    //$this->sendMailRemb($id, $token);

                    return Redirect::to('mails/optionSi');
                    

                }
                else
                {
                    $this->sendMailAuto2($id, $token);
                    $this->replyRequestApprobSi($id, $token, $Auto1->TrabajadorID);

                    return Redirect::to('mails/optionSi');
                }
            }
            if($option=='no')
            {
                if ($folio->id_status<'10') 
                {
                    $folio->id_status='10';
                    $folio->update();

                    $firmasAuto=FirmaGasto::where('id_header_folio','=',$folio->id_header_folio)
                    ->where('id_autorizador','>','0')
                    ->delete();
                    //Edita el status del usuario, lo regresa a status 0 para volver a enviar 
                    $firmaUser=FirmaGasto::where('id_header_folio','=',$folio->id_header_folio)
                    ->where('id_user','>','0')
                    ->where('status','=','1')
                    ->first();
                    $firmaUser->status='0';
                    $firmaUser->gasto=null;
                    $firmaUser->save();
    

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

        if ($folio->id_status<'13') 
        {
            $Auto1=$this->Auto1($id, $token);
            $Auto2=$this->Auto2($id, $token);

            if ($option=='si')
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
                //$folio->id_status='16';
                //$folio->update();

                $mytime = Carbon::now('America/Monterrey');

                $firmaAuto2=FirmaGasto::where('id_header_folio','=',$folio->id_header_folio)
                ->where('id_autorizador','>','0')
                ->where('status','=','0')
                ->first();
                $firmaAuto2->status='1';
                $firmaAuto2->gasto=$mytime;
                $firmaAuto2->update();

                $this->sendMailStaff($id, $token, $Auto2->TrabajadorID);
                // $this->sendMailRemb($id, $token);

                return Redirect::to('mails/optionSi');
            }
            elseif($option=='no')
            {
                if ($folio->id_status<'12') {
                    
                    $folio->id_status='12';
                    $folio->update();

                    $firmasAuto=FirmaGasto::where('id_header_folio','=',$folio->id_header_folio)
                    ->where('id_autorizador','>','0')
                    ->delete();
                    //Edita el status del usuario, lo regresa a status 0 para volver a enviar (Rechazado por el autorizador 2)
                    $firmaUser=FirmaGasto::where('id_header_folio','=',$folio->id_header_folio)
                    ->where('id_user','>','0')
                    ->where('status','=','1')
                    ->first();
                    $firmaUser->status='0';
                    $firmaUser->gasto=null;
                    $firmaUser->save();


                    $folioMail=DB::table('ssm_viat_header_folio as f')
                    ->join('users as u','f.id_solicitante','=','u.id')
                    ->join('VIEW_SSM_GET_AUTHORIZERS as a','a.TrabajadorID','=',DB::raw($Auto2->TrabajadorID))
                    ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
                    ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','f.evidencia_viaje','f.pdfevidencia','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.name','u.company','f.correo_solicitante as emailU','a.TrabajadorID','a.email as emailA','m.moneda')
                    ->where('f.id_header_folio','=',$id)
                    ->where('f._token','=',$token)
                    ->first();

                    $folioMaill=DB::table('ssm_viat_header_folio as f')
                    ->join('users as u','f.id_solicitante','=','u.id')
                    ->join('VIEW_SSM_GET_AUTHORIZERS as a','a.TrabajadorID','=',DB::raw($Auto2->TrabajadorID))
                    ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
                    ->join('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
                    ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','t.montopesos','f.evidencia_viaje','f.pdfevidencia','f.destino','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.name','u.company','f.correo_solicitante as emailU','a.TrabajadorID','a.email as emailA','m.moneda')
                    ->where('f.id_header_folio','=',$id)
                    ->where('f._token','=',$token)
                    ->first();

                    $detalles=DB::table('ssm_viat_detalle_folio as d')
                    ->join('ssm_viat_gastos as g','d.id_gasto','=','g.id_gasto')
                    ->select('d.id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago',DB::raw('sum( d.importe + d.IVA + d.otro_impuesto ) as Subtotal'))
                    ->where('id_header_folio','=',$id)
                    ->groupBy('id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago')
                    ->get();

                    //AGREGAR DETALLES EN CASO DE QUE EL GASTO SEA EN DOLLARES/JENES
                    $detalle=DB::table('ssm_viat_detalle_folio as d')
                    ->join('ssm_viat_gastos as g','d.id_gasto','=','g.id_gasto')
                    ->join('ssm_viat_moneda as m','d.tipomoneda','=','m.id_moneda')
                    ->select('d.id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago', 'm.moneda',DB::raw('sum( d.importe + d.IVA + d.otro_impuesto ) as Subtotal'), DB::raw('sum( d.importeint + d.IVAint + d.otro_impuesto ) as Subtotalint'))
                    ->where('id_header_folio','=',$id)
                    ->groupBy('id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago', 'm.moneda')
                    ->get();

                    $tipomoneda=DB::table('ssm_viat_detalle_folio as d')
                    ->join('ssm_viat_moneda as m','d.tipomoneda','=','m.id_moneda')
                    ->select('d.id_gasto', 'm.moneda')
                    ->where('id_header_folio','=',$id)
                    ->groupBy('d.id_gasto','m.moneda')
                    ->get();

                    Mail::Send('mails.replyRequestExpenseNo', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail, $Auto1){
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

    public function sendMailCxP($id, $token){

        //Se manda el folio primeramente a cuentas por pagar para revisar los gastos

        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','f.evidencia_viaje','f.pdfevidencia','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.name','u.company', 'u.numeroNom','f.correo_solicitante as emailU','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('f._token','=',$token)
        ->first();

        $folioMaill=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','t.montopesos','f.evidencia_viaje','f.pdfevidencia','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.name','u.company','f.correo_solicitante as emailU','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('f._token','=',$token)
        ->first();

        //Se Obtiene la informacion de Company del Usuario 

        $usernom = $folioMail->numeroNom;
        $branch = $folioMail->company;

        $company = substr($branch,0,1);                
        $valuesUser = $company.$usernom;

        $userClaims = DB::select('EXEC Sp_Get_RH7_Info_Users ?', Array($valuesUser));

        $detalles=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_gastos as g','d.id_gasto','=','g.id_gasto')
        ->select('d.id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago',DB::raw('sum( d.importe + d.IVA + d.otro_impuesto ) as Subtotal'))
        ->where('id_header_folio','=',$id)
        ->groupBy('id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago')
        ->get();

        //AGREGAR DETALLES EN CASO DE QUE EL GASTO SEA EN DOLLARES/JENES

        $detalle=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_gastos as g','d.id_gasto','=','g.id_gasto')
        ->join('ssm_viat_moneda as m','d.tipomoneda','=','m.id_moneda')
        ->select('d.id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago', 'm.moneda',DB::raw('sum( d.importe + d.IVA + d.otro_impuesto ) as Subtotal'), DB::raw('sum( d.importeint + d.IVAint + d.otro_impuesto ) as Subtotalint'))
        ->where('id_header_folio','=',$id)
        ->groupBy('id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago', 'm.moneda')
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
        
        if ($userClaims[0]->compania=='MTY') {
                            
            Mail::Send('mails.advanceTransferExpenses', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail, $data3){
                $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                $mail->to($data3['MTYemailCxP'])
                ->cc($data3['CCMTYemailCxP']);
            });
        }
        if ($userClaims[0]->compania=='QRO,SLM,MTY') {
            
            Mail::Send('mails.advanceTransferExpenses', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail, $data3){
                $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                $mail->to([$data3['CCQROemailCxP']])
                ->cc(['andres.salinas@summitmx.com']);
            });
        }
        if($userClaims[0]->compania=='SLM'){

            Mail::Send('mails.advanceTransferExpenses', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail, $data3){
                $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                $mail->to($data3['SLMemailCxP'])
                ->cc([$data3['CCSLMemailCxP'], 'pablo.resendiz@summitmx.com', 'andres.salinas@summitmx.com']);
            });
        }
        if($userClaims[0]->compania=='QRO'){

            Mail::Send('mails.advanceTransferExpenses', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail, $data3){
                $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                $mail->to($data3['SLMemailCxP'])
                ->cc([$data3['CCSLMemailCxP'], 'pablo.resendiz@summitmx.com', 'andres.salinas@summitmx.com']);
            });
        }         
        if($userClaims[0]->compania=='QRO,SLM'){

            Mail::Send('mails.advanceTransferExpenses', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail, $data3){
                $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                $mail->to($data3['SLMemailCxP'])
                ->cc(['pablo.resendiz@summitmx.com', 'andres.salinas@summitmx.com']);
            });
        } 
    }
   
   
    public function sendMailAuto1($id ,$token)
    {
        $folio=Folio::where('_token','=',$token)
        ->where('id_header_folio','=', $id)
        ->first();
        
        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_INFO_USERS as p','u.numeroNom','=','p.TrabajadorID')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','f.correo_auto1','=','a.email')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.destino','f.proposito','f.evidencia_viaje','f.pdfevidencia','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.anticipo','f.all_total','m.moneda','u.name','f.correo_solicitante as emailU','a.TrabajadorID','a.email as emailA','f._token')
        ->where('f.id_header_folio','=',$id)
        ->where('_token','=',$token)
        ->where('p.compania','LIKE','%'.$folio->company.'%')
        ->first();

        $folioMaill=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_INFO_USERS as p','u.numeroNom','=','p.TrabajadorID')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','f.correo_auto1','=','a.email')
        ->join('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.destino','f.proposito','f.evidencia_viaje','f.pdfevidencia','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.anticipo','f.all_total','t.montopesos','m.moneda','u.name','f.correo_solicitante as emailU','a.TrabajadorID','a.email as emailA','f._token')
        ->where('f.id_header_folio','=',$id)
        ->where('_token','=',$token)
        ->where('p.compania','LIKE','%'.$folio->company.'%')
        ->first();

        $detalles=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_gastos as g','d.id_gasto','=','g.id_gasto')
        ->select('d.id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago',DB::raw('sum( d.importe + d.IVA + d.otro_impuesto ) as Subtotal'))
        ->where('id_header_folio','=',$id)
        ->groupBy('id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago')
        ->get();

        //AGREGAR DETALLES EN CASO DE QUE EL GASTO SEA EN DOLLARES/JENES

        $detalle=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_gastos as g','d.id_gasto','=','g.id_gasto')
        ->join('ssm_viat_moneda as m','d.tipomoneda','=','m.id_moneda')
        ->select('d.id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago', 'm.moneda',DB::raw('sum( d.importe + d.IVA + d.otro_impuesto ) as Subtotal'), DB::raw('sum( d.importeint + d.IVAint + d.otro_impuesto ) as Subtotalint'))
        ->where('id_header_folio','=',$id)
        ->groupBy('id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago', 'm.moneda')
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
            ->join('VIEW_SSM_INFO_USERS as p','u.numeroNom','=','p.TrabajadorID')
            ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
            ->join('VIEW_SSM_GET_AUTHORIZERS as a','f.correo_auto1','=','a.email')
            ->select('f.id_header_folio','f.fecha','f.tipo','f.destino','f.proposito','f.evidencia_viaje','f.pdfevidencia','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.anticipo','f.all_total','m.moneda','u.name','f.correo_solicitante as emailU','u.company','a.TrabajadorID','a.email as emailA','f._token')
            ->where('f.id_header_folio','=',$id)
            ->where('_token','=',$token)
            ->where('p.compania','LIKE','%'.$folio->company.'%')
            ->first();
    
            $firmaAuto = new FirmaGasto;
            $firmaAuto->company=Auth::user()->company;
            $firmaAuto->id_autorizador=$folioMail->TrabajadorID;
            $firmaAuto->id_header_folio=$folioMail->id_header_folio;
            $firmaAuto->status='0';
            $firmaAuto->save();
        }
        else{

        }


        Mail::Send('mails.requestExpenseAuto1', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles,'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail){
            $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
            $mail->to($folioMail->emailA);
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
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','f.correo_auto2','=','a.email')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.destino','f.proposito','f.evidencia_viaje','f.pdfevidencia','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.anticipo','f.all_total','m.moneda','u.name','f.correo_solicitante as emailU','u.company','a.TrabajadorID','a.email as emailA','f._token')
        ->where('f.id_header_folio','=',$id)
        ->where('_token','=',$token)
        ->first();

        $folioMaill=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','f.correo_auto2','=','a.email')
        ->join('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.destino','f.proposito','f.evidencia_viaje','f.pdfevidencia','f.eq_computo','t.montopesos','f.fecha_salida','f.fecha_llegada','f.anticipo','f.all_total','m.moneda','u.name','f.correo_solicitante as emailU','u.company','a.TrabajadorID','a.email as emailA','f._token')
        ->where('f.id_header_folio','=',$id)
        ->where('_token','=',$token)
        ->first();

        $detalles=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_gastos as g','d.id_gasto','=','g.id_gasto')
        ->select('d.id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago',DB::raw('sum( d.importe + d.IVA + d.otro_impuesto ) as Subtotal'))
        ->where('id_header_folio','=',$id)
        ->groupBy('id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago')
        ->get();

        //AGREGAR DETALLES EN CASO DE QUE EL GASTO SEA EN DOLLARES/JENES
        $detalle=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_gastos as g','d.id_gasto','=','g.id_gasto')
        ->join('ssm_viat_moneda as m','d.tipomoneda','=','m.id_moneda')
        ->select('d.id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago', 'm.moneda',DB::raw('sum( d.importe + d.IVA + d.otro_impuesto ) as Subtotal'), DB::raw('sum( d.importeint + d.IVAint + d.otro_impuesto ) as Subtotalint'))
        ->where('id_header_folio','=',$id)
        ->groupBy('id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago', 'm.moneda')
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
            $firmaAuto->company=$folioMai->company;
            $firmaAuto->id_autorizador=$folioMai->TrabajadorID;
            $firmaAuto->id_header_folio=$folioMai->id_header_folio;
            $firmaAuto->status='0';
            $firmaAuto->save();
        }
        else{

        }

        Mail::Send('mails.requestExpenseAuto2', ['folioMail'=> $folioMail, 'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail){
            $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
            $mail->to($folioMail->emailA);
        });

        if (Mail::failures()) 
        {
        // return response showing failed emails
            dd(Mail::failures());
        }
         //dd(Mail::failures());
    }

    public function sendMailStaff($id ,$token, $TrabajadorID)
    {

        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','a.TrabajadorID','=',DB::raw($TrabajadorID))
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','f.evidencia_viaje','f.pdfevidencia','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.name','u.company','f.correo_solicitante as emailU','a.TrabajadorID','a.NombreAuto','a.email as emailA','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('f._token','=',$token)
        ->first();

        $folioMaill=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','a.TrabajadorID','=',DB::raw($TrabajadorID))
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','t.montopesos','f.evidencia_viaje','f.pdfevidencia','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.name','u.company','f.correo_solicitante as emailU','a.TrabajadorID','a.NombreAuto','a.email as emailA','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('f._token','=',$token)
        ->first();

        $detalles=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_gastos as g','d.id_gasto','=','g.id_gasto')
        ->select('d.id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago',DB::raw('sum( d.importe + d.IVA + d.otro_impuesto ) as Subtotal'))
        ->where('id_header_folio','=',$id)
        ->groupBy('id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago')
        ->get();

        //AGREGAR DETALLES EN CASO DE QUE EL GASTO SEA EN DOLLARES/JENES

        $detalle=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_gastos as g','d.id_gasto','=','g.id_gasto')
        ->join('ssm_viat_moneda as m','d.tipomoneda','=','m.id_moneda')
        ->select('d.id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago', 'm.moneda',DB::raw('sum( d.importe + d.IVA + d.otro_impuesto ) as Subtotal'), DB::raw('sum( d.importeint + d.IVAint + d.otro_impuesto ) as Subtotalint'))
        ->where('id_header_folio','=',$id)
        ->groupBy('id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago', 'm.moneda')
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

        //Se realiza una condicional, en donde se verifica el estatus del folio, para enviar el correo a cuentas por pagar o directamente a rembolso
        if($folioMail->id_status =='15'){
            $this->sendMailCxP($id, $token);   
        }
        else{
            $this->sendMailRemb($id, $token);
        }
        //Se realiza la condicional para verificar si el usuario requiere de un rembolso (Ya se realizo primero por cuentas por pagar la autorizacion)
       

    }

    public function sendMailRemb($id, $token){
        
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

        $detalles=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_gastos as g','d.id_gasto','=','g.id_gasto')
        ->select('d.id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago',DB::raw('sum( d.importe + d.IVA + d.otro_impuesto ) as Subtotal'))
        ->where('id_header_folio','=',$folioMail->id_header_folio)
        ->groupBy('id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago')
        ->get();

        //AGREGAR DETALLES EN CASO DE QUE EL GASTO SEA EN DOLLARES/JENES

        $detalle=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_gastos as g','d.id_gasto','=','g.id_gasto')
        ->join('ssm_viat_moneda as m','d.tipomoneda','=','m.id_moneda')
        ->select('d.id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago', 'm.moneda',DB::raw('sum( d.importe + d.IVA + d.otro_impuesto ) as Subtotal'), DB::raw('sum( d.importeint + d.IVAint + d.otro_impuesto ) as Subtotalint'))
        ->where('id_header_folio','=',$folioMail->id_header_folio)
        ->groupBy('id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago', 'm.moneda')
        ->get();

        $tipomoneda=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_moneda as m','d.tipomoneda','=','m.id_moneda')
        ->select('d.id_gasto', 'm.moneda')
        ->where('id_header_folio','=',$id)
        ->groupBy('d.id_gasto','m.moneda')
        ->get();
        

        // dd($folioPendiente,$trans,$folioUpdate,$folioMail,$detalles);

        // dd($folioPendiente);

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
                // ->cc('gerardo.castro@summitmx.com')
                ->cc($folioMail->emailU);
                // $mail->to('gerardo.castro@yopmail.com', 'GERARDO CASTRO')
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
                    // ->cc('gerardo.castro@summitmx.com')
                    ->cc($folioMail->emailU);
                    // $mail->to('gerardo.castro@yopmail.com', 'GERARDO CASTRO')
                    // ->cc($folioMail->emailU);
                    
                });
                
            }
            else
            {
                Mail::Send('mails.repaymentUser', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                    $mail->to($folioMail->emailU, $folioMail->name)
                    ->cc('gerardo.castro@summitmx.com');
                    // ->cc('gerardo.castro@yopmail.com');
                    
                    
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
                    // $mail->to('gerardo.castro@yopmail.com', 'GERARDO CASTRO')
                    // ->cc($folioMail->emailU);
                    
                });
                
            }
            else
            {
                Mail::Send('mails.repaymentUser', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail){
                    $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
                    $mail->to($folioMail->emailU, $folioMail->name)
                    ->cc('gerardo.castro@summitmx.com');
                    // ->cc('gerardo.castro@yopmail.com');
                    
                    
                });
            }
            
        }
    }

    public function replyRequestApprobSi($id, $token, $TrabajadorID)
    {
        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','a.TrabajadorID','=',DB::raw($TrabajadorID))
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','f.evidencia_viaje','f.pdfevidencia','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.name','u.company','f.correo_solicitante as emailU','a.TrabajadorID','a.NombreAuto','a.email as emailA','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('f._token','=',$token)
        ->first();

        $folioMaill=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','a.TrabajadorID','=',DB::raw($TrabajadorID))
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','t.montopesos','f.evidencia_viaje','f.pdfevidencia','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.name','u.company','f.correo_solicitante as emailU','a.TrabajadorID','a.NombreAuto','a.email as emailA','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('f._token','=',$token)
        ->first();

        $detalles=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_gastos as g','d.id_gasto','=','g.id_gasto')
        ->select('d.id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago',DB::raw('sum( d.importe + d.IVA + d.otro_impuesto ) as Subtotal'))
        ->where('id_header_folio','=',$id)
        ->groupBy('id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago')
        ->get();

        //AGREGAR DETALLES EN CASO DE QUE EL GASTO SEA EN DOLLARES/JENES

        $detalle=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_gastos as g','d.id_gasto','=','g.id_gasto')
        ->join('ssm_viat_moneda as m','d.tipomoneda','=','m.id_moneda')
        ->select('d.id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago', 'm.moneda',DB::raw('sum( d.importe + d.IVA + d.otro_impuesto ) as Subtotal'), DB::raw('sum( d.importeint + d.IVAint + d.otro_impuesto ) as Subtotalint'))
        ->where('id_header_folio','=',$id)
        ->groupBy('id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago', 'm.moneda')
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

    public function replyRequestApprobNo($id, $token, $TrabajadorID)
    {


        $folioMaill=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','a.TrabajadorID','=',DB::raw($TrabajadorID))
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->join('ssm_viat_tc_cambio as t','t.id_header_folio','=','f.id_header_folio')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','t.montopesos','f.evidencia_viaje','f.pdfevidencia','f.proposito','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.name','u.company','f.correo_solicitante as emailU','a.TrabajadorID','a.NombreAuto','a.email as emailA','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('f._token','=',$token)
        ->first();

        $folioMail=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','a.TrabajadorID','=',DB::raw($TrabajadorID))
        ->join('ssm_viat_moneda as m','f.id_moneda','=','m.id_moneda')
        ->select('f.id_header_folio','f.fecha','f.tipo','f.id_status','f.destino','f.proposito','f.evidencia_viaje','f.pdfevidencia','f.eq_computo','f.fecha_salida','f.fecha_llegada','f.criterio','f.anticipo','f.all_total','f._token','u.name','u.company','f.correo_solicitante as emailU','a.TrabajadorID','a.NombreAuto','a.email as emailA','m.moneda')
        ->where('f.id_header_folio','=',$id)
        ->where('f._token','=',$token)
        ->first();

        $detalles=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_gastos as g','d.id_gasto','=','g.id_gasto')
        ->select('d.id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago',DB::raw('sum( d.importe + d.IVA + d.otro_impuesto ) as Subtotal'))
        ->where('id_header_folio','=',$id)
        ->groupBy('id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago')
        ->get();

        //AGREGAR DETALLES EN CASO DE QUE EL GASTO SEA EN DOLLARES/JENES

        $detalle=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_gastos as g','d.id_gasto','=','g.id_gasto')
        ->join('ssm_viat_moneda as m','d.tipomoneda','=','m.id_moneda')
        ->select('d.id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago', 'm.moneda',DB::raw('sum( d.importe + d.IVA + d.otro_impuesto ) as Subtotal'), DB::raw('sum( d.importeint + d.IVAint + d.otro_impuesto ) as Subtotalint'))
        ->where('id_header_folio','=',$id)
        ->groupBy('id_header_folio','d.id_gasto','g.nomGasto','d.metodoPago', 'm.moneda')
        ->get();

        $tipomoneda=DB::table('ssm_viat_detalle_folio as d')
        ->join('ssm_viat_moneda as m','d.tipomoneda','=','m.id_moneda')
        ->select('d.id_gasto', 'm.moneda')
        ->where('id_header_folio','=',$id)
        ->groupBy('d.id_gasto','m.moneda')
        ->get();

        Mail::Send('mails.replyRequestExpenseNo', ['folioMail'=> $folioMail,'folioMaill'=> $folioMaill,'detalles'=>$detalles, 'detalle'=>$detalle, 'tipomoneda'=>$tipomoneda], function($mail) use($folioMail){
            $mail->subject('SOLICITUD Y REPORTE DE VIAJE: '.$folioMail->name.', Folio: '.$folioMail->id_header_folio);
            $mail->to($folioMail->emailU, $folioMail->name);
        });
    }

    public function Auto1($id, $token)
    {
        $Autorizador=DB::table('ssm_viat_header_folio as f')
        ->join('users as u','f.id_solicitante','=','u.id')
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
        ->join('VIEW_SSM_GET_AUTHORIZERS as a','f.correo_auto2','=','a.email')
        ->select('u.company','a.TrabajadorID','a.email')
        ->where('f.id_header_folio','=',$id)
        ->where('_token','=',$token)
        ->first();

        return $Autorizador;

    }

}