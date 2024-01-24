<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	$exitCode = Artisan::call('cache:clear');
    return view('auth/login');
});


Route::resource('seguridad/usuario','UsuarioController');

Route::resource('seguridad/profile','UserProfileController');

Route::resource('travel/solicitud','FolioController');

Route::resource('travel/gasto','DetalleFolioController');

Route::resource('travel/xml','XmlController');

Route::resource('administracion/vuelos','FlightsController');

Route::resource('reports/reportes','ReportesMensualesController');

Route::resource('accounting/comprobacion','CuentasXPagarController');

Route::resource('accounting/validados','CuentasXPagarValidadosController');

Route::resource('accounting/reportes','ReportesController');

Route::resource('accounting/preAnticipo','PreAnticipoController');

Route::resource('treasury/anticipo','TransferController');

Route::resource('treasury/rembolso','RepaymentController');

Route::resource('authorizers/approbation','AutorizadoresController');

Route::resource('send/reminder','SendReminderController');

Route::resource('send/transfer','SendTransferController');

Route::get('trevel/gasto/{id}/confirm','DetalleFolioController@confirm')->name('trevel/gasto/{id}/confirm');



Route::get('reviewfolio/{id}/{toke}', 'ReviewController@sendAuto')->name('reviewfolio');

Route::get('reviewGet/{token}/token/{id}/folio/{option}', 'ReviewController@getAuto1')->name('reviewGet/{token}/token/{folio}/folio/{option}');
//Route::get('reviewGet/{token}/token/{id}/folio/{option}', 'ReviewController@show')->name('reviewGet/{token}/token/{folio}/folio/{option}');

Route::get('confirmGet/{token}/token/{id}/folio/{option}', 'ReviewController@getAuto2')->name('confirmGet/{token}/token/{folio}/folio/{option}');
//Route::get('confirmGet/{token}/token/{id}/folio/{option}', 'ConfirmController@show')->name('confirmGet/{token}/token/{folio}/folio/{option}');


Route::get('authorizeGet/{token}/token/{id}/folio/{option}', 'ReviewController@getAuto3')->name('authorizeGet/{token}/token/{folio}/folio/{option}');
#Route::get('authorizeGet/{token}/token/{id}/folio/{option}', 'ConfirmController@update')->name('authorizeGet/{token}/token/{folio}/folio/{option}');

Route::get('expensefolio/{id}/{token}', 'ExpenseController@sendcxp')->name('expensefolio');

Route::get('ReporteMensuUsuario/{FechaI}/{FechaF}/{id}/{status}', 'ReportesMensualesController@show')->name('ReporteMensuUsuario');

Route::get('reviewExpenseGet/{token}/token/{id}/folio/{option}', 'ExpenseController@getAuto1')->name('reviewExpenseGet/{token}/token/{folio}/folio/{option}');
#Route::get('reviewExpenseGet/{token}/token/{id}/folio/{option}', 'ExpenseController@sendGerenciaGral')->name('reviewExpenseGet/{token}/token/{folio}/folio/{option}');

Route::get('confirmExpenseGet/{token}/token/{id}/folio/{option}', 'ExpenseController@getAuto2')->name('confirmExpenseGet/{token}/token/{folio}/folio/{option}');

Route::get('authorizeExpenseGet/{token}/token/{id}/folio/{option}', 'ExpenseController@getAuto3')->name('authorizeExpenseGet/{token}/token/{folio}/folio/{option}');

Route::get('terminarGuardado/{id}/{id_folio}/{token}', 'FlightsController@terminarFacturas')->name('terminarGuardado');

Route::get('mails/optionNo', function () {
    return view('mails/optionNo');
});

Route::get('mails/optionSi', function () {
    return view('mails/optionSi');
});

Route::get('mails/optionFail', function () {
    return view('mails/optionFail');
});

Route::get('travel/print/index','PDFController@index');

Route::get('travel/reportes/reporte','ReportesController@index');

Route::get('accounting/statusFolios','CuentasXPagarValidadosController@statusFolios');

Route::get('authorizers/evidencias','AutorizadoresController@evidenciareporte');

// Correos de Recordatorio
Route::get('reminders/SendDiasPasados','SendReminderController@SendDiasPasados');
Route::get('reminders/SendComprobacionesporVencer','SendReminderController@SendComprobacionesporVencer');

//REPORTE DE CONTABILIDAD
Route::get('accounting/validados/reporte/{id}', 'CuentasXPagarValidadosController@report');
Route::get('accounting/comprobacion/reporte/{id}', 'CuentasXPagarController@report');
Route::get('accounting/reportetotal', 'CuentasXPagarValidadosController@reporttotales');

Route::get('pdf/travel/{id}', 'PDFController@view')->name('pdf/travel/{id}');

Route::get('/accounting/entregado/{id}/{token}', 'CuentasXPagarController@entregado')->name('accounting/entregado/{id}/{token}');

Route::get('/accounting/denegado/{id}/{token}', 'CuentasXPagarController@denegado')->name('accounting/denegado/{id}/{token}');

Route::get('/authorizers/approbation/anticipo/{id}/{token}', 'AutorizadoresController@autorizaAnticipo')->name('authorizers/approbation/anticipo/{id}/{token}');

Route::get('/authorizers/approbation/gasto/{id}/{token}', 'AutorizadoresController@autorizaGasto')->name('authorizers/approbation/gasto/{id}/{token}');

Auth::routes();

Route::get('/logout', 'Auth\LoginController@logout');

Route::post('/tipocambio','DetalleFolioController@cambio');

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/{slug}', 'HomeController@index')->name('home');

//Rutas de Prueba para Alertas
Route::post('/enviarautorizador','PruebaController@enviarautorizador');

Route::get('/log/logintest','Auth\LoginController@logintest');

Route::post('/updatecuenta','CuentasXPagarValidadosController@updatecuenta');

Route::post('/detalleAccount','CuentasXPagarValidadosController@detalleAccount');

Route::post('/detalleAccountAMEX','CuentasXPagarValidadosController@detalleAccountAMEX');

Route::post('/updateTipoAMEX','CuentasXPagarValidadosController@updateTipoAMEX');

Route::post('/updatecuentaitemAMEX','CuentasXPagarValidadosController@updatecuentaitemAMEX');

Route::post('/DeleteRetencion','CuentasXPagarValidadosController@DeleteRetencion');

Route::post('/subirSAP','CuentasXPagarValidadosController@subirSAP');