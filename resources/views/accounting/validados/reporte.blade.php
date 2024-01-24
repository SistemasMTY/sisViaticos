@extends ('layout.admin')
@section ('contenido')
<title>Reporte de gastos de viaje: {{$folio->id_header_folio}} </title>
<link rel="stylesheet" href="{{asset('DataTables\DataTables-1.10.18\css\dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('DataTables\Buttons-1.5.6\css\buttons.bootstrap4.min.css')}}">

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
		<h3>Reporte de gastos de viaje: {{$folio->id_header_folio}}</h3>
        <input type="hidden" id="id_header_folio" value="{{$folio->id_header_folio}}" readonly>
	</div>
</div>
@if($count > 0)
	<div class="row" id="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="display: flex; justify-content: flex-end">
			<a><button id="{{$folio->id_header_folio}}" class="btn btn-warning btn-warning"><i class="fa fa-list-alt"></i> Detalles AMEX</button></a>	
		</div>
	</div>
@endif
<div class="row">	
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="table-responsive">
			<table id="datatable" class="table table-striped table-bordered table-condensed table-hover">
				<thead>
                <tr>
					<th>OPCIONES</th>
                	<th>CUENTA</th>
					<th>   </th>
                	<th>DEBE</th>
                	<th>HABER</th>
					<th>   </th>
					<th>   </th>
					<th>RFC</th>
                	<th>PROVEEDOR</th>  
					<th>CONCEPTO</th> 
					<th>FACTURA</th>  
					<th>UUID</th> 
                </tr>
            	</thead>
				<tfoot>
					<th></th>
					<th></th>			
					<th></th>
					<th><h4 id="total">{{number_format($suma->total,2)}}</h4><input type="hidden" name="total_venta" id="total_venta"></th>
					<th><h4 id="total"></h4><input type="hidden" name="total_venta" id="total_venta"></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
				</tfoot>
				<tbody>
				@foreach($reporte as $reporte)
					@if($reporte->MetodoPago == 'AMEX')
						<tr style="background-color:#CFCFCF">	
							<td align="center">
								<a><button id="{{$reporte->id_rep_gasto}}" class="btn btn-warning btn-warning"><i class="fa fa-credit-card"></i> Editar</button></a>
							</td>
							<td>{{$reporte->CuentaS}}</td>
							<td>   </td>
							<td>{{$reporte->debe}}</td>
							<td></td>
							<td>   </td>
							<td>   </td>
							<td>{{$reporte->RFC}}</td>
							<td>{{$reporte->proveedor}}</td>
							<td>{{$reporte->gasto}}</td>
							<td>{{$reporte->noFactura}}</td>
							<td>{{$reporte->UUID}}</td>
						</tr>
					@else
						<tr style="background-color:#FFFFFF">	
							<td align="center"><a><button id="{{$reporte->id_rep_gasto}}" class="btn btn-warning btn-warning"><i class="fa fa-credit-card"></i> Editar</button></a></td>
							<td>{{$reporte->CuentaS}}</td>
							<td>   </td>
							<td>{{$reporte->debe}}</td>
							<td></td>
							<td>   </td>
							<td>   </td>
							<td>{{$reporte->RFC}}</td>
							<td>{{$reporte->proveedor}}</td>
							<td>{{$reporte->gasto}}</td>
							<td>{{$reporte->noFactura}}</td>
							<td>{{$reporte->UUID}}</td>
						</tr>
					@endif
				@endforeach	
			</table>
		</div>
		<a href="{{ url('accounting/validados')}}" class="btn btn-danger">Atras</a>
		<a><button id="{{$folio->id_header_folio}}" class="btn btn-sap btn-success">Guardar y pasar a SAP</button></a>
	</div>
</div>
<!-- The Modal -->
<!-- MODAL PARA REALIZAR EL UPDATE DE LAS CUENTAS-->
<div class="modal fade" id="modal-cuenta" tabindex="-1" role="dialog" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>				
                <h4 class="modal-title" >Editar Cuenta</h4>                
            </div>
            <div class="modal-body">
                <form autocomplete="off">					
                    <div class="form-group">
						<label>Cuenta:</label>
						<select name="cuenta" id= "cuenta" class="form-control selectpicker" data-live-search="true">
							@foreach($cuentas as $cuenta)
								<option value="{{$cuenta->AcctCode}}">{{$cuenta->NameCuenta}}</option>
							@endforeach
						</select>
					</div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="modalguardar" type="button" class="btn btn-success">Guardar</button>
            </div>
        </div>
    </div>
</div>
<!-- MODAL PARA INGRESAR LOS DATOS DE INICIO DE SESION DE SAP-->
<div class="modal fade" id="modal-SAP" tabindex="-1" role="dialog"  aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>				
                <h4 class="modal-title" >Inicio de Sesion SAP</h4>                
            </div>
            <div class="modal-body">
                <form autocomplete="off" id="basic-form">	
					<h5>Ingresar los datos de Inicio de Sesión en SAP</h5>				
                    <div class="form-group" >						      
                        <label for="usuarioSAP">Usuario:</label>
                        <input type="text" class="form-control" name="usuarioSAP" id="usuarioSAP" placeholder="usuario" required>   
						<br>
                        <label for="ClaveS">Clave:</label>
                        <input type="password" class="form-control" name="ClaveS" id="ClaveS" placeholder="contraseña" required>   
					</div>
                </form>
				<br>
            </div>
            <div class="modal-footer">
                <button id="modaliniciarSesion" type="button" class="btn btn-success">Iniciar Sesion</button>
            </div>
        </div>
    </div>
</div>
<!-- MODAL PARA REVISAR EL DETALLE DE AMEX Y HACER CAMBIOS EN LA CUENTA RETENCION DE SER NECESARIO-->
<div class="modal fade" id="modal-AMEX" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog" style="width:1250px;" role="document">
        <div class="modal-content">
            <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>				
                <h4 class="modal-title" >Detalles de factura AMEX</h4>                
            </div>
            <div class="modal-body">				
                <div class="form-group">
					<div class="table-responsive">
						<table id="datatableAMEX" class="table table-striped table-bordered table-condensed table-hover">
							<thead>
							<tr>
								<th>Agregar Cuenta</th>
								<th>Line</th>
								<th>ProveedorCFD</th>
								<th>Cantidad</th>
								<th>PU</th>
								<th>Valor unitario</th>
								<th>Importe</th>
								<th>Descripcion</th>
								<th>Tasa Cuota</th>
								<th>Clave</th>  
								<th>Clave Proveedor</th> 
								<th>Descuento I</th>  
								<th>Iva</th> 
								<th>Iva SAP</th>  
								<th>Tipo Factor</th> 
								<th>Importe item</th>  
								<th>Clave Unidad</th>
								<th>Retencion</th>
								<th>Tipo Retencion (Agregar)</th>
							</tr>
							</thead>							
							<tbody>
							@foreach($detallesAMEX as $detalleAMEX)								
								<tr>	
									@IF(!$detalleAMEX->CuentaS)		
									<td align="center"><a><button id="{{$detalleAMEX->id_detalle_AMEX}}" class="btn btn-success btn-success"><i class="fa fa-credit-card"></i> Agregar</button></a></td>					
									@ELSE
										<td>{{$detalleAMEX->CuentaS}}</td>
									@ENDIF
									<td>{{$detalleAMEX->line}}</td>
									<td>{{$detalleAMEX->id_ProveedorCFD}}</td>
									<td>{{$detalleAMEX->cantidad}}</td>
									<td>{{$detalleAMEX->PU}}</td>
									<td>{{$detalleAMEX->valor_unitario}}</td>
									<td>{{$detalleAMEX->importe}}</td>
									<td>{{$detalleAMEX->descripcion}}</td>	
									<td>{{$detalleAMEX->tasa_cuota}}</td>
									<td>{{$detalleAMEX->clave}}</td>
									<td>{{$detalleAMEX->clave_prod_serv}}  {{$detalleAMEX->desc_clave_prod_serv}}</td>	
									<td>{{$detalleAMEX->descuento_i}}</td>
									<td>{{$detalleAMEX->iva}}</td>
									<td>{{$detalleAMEX->iva_sap}}</td>
									<td>{{$detalleAMEX->tipo_factor}}</td>
									<td>{{$detalleAMEX->importe_itm}}</td>
									<td>{{$detalleAMEX->clave_unidad}}  {{$detalleAMEX->unidad_itm}}</td>	
									<td>{{$detalleAMEX->retencion}} Tasa de Retencion: {{$detalleAMEX->tasa_retencion}}</td>
									@IF(!$detalleAMEX->WTCode && $detalleAMEX->retencion > 0)
										<td align="center"><a><button id="{{$detalleAMEX->id_detalle_AMEX}}" class="btn btn-warning btn-warning"><i class="fa fa-credit-card"></i> Agregar Tipo</button></a>
                                        <a><button id="{{$detalleAMEX->id_detalle_AMEX}}" class="btn btn-danger btn-danger"><i class="fa fa-credit-card"></i> Eliminar retencion</button></a></td>
									@ELSE
										<td>{{$detalleAMEX->WTCode}}</td>
									@ENDIF
								</tr>
							@endforeach	
						</table>
					</div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- MODAL PARA AGREGAR LA CUENTA DEL ITEM -->
<div class="modal fade" id="modal-cuentaAMEX" tabindex="-1" role="dialog" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>				
                <h4 class="modal-title" >Editar Cuenta</h4>                
            </div>
            <div class="modal-body">
                <form autocomplete="off">					
                    <div class="form-group">
						<label>Cuenta:</label>
						<select name="cuenta2" id= "cuenta2" class="form-control selectpicker" data-live-search="true">
							@foreach($cuentas as $cuenta)
								<option value="{{$cuenta->AcctCode}}">{{$cuenta->NameCuenta}}</option>
							@endforeach
						</select>
					</div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="modalguardaritemAMEX" type="button" class="btn btn-success">Guardar</button>
            </div>
        </div>
    </div>
</div>
<!-- MODAL PARA AGREGAR EL TIPO DE RETENCION -->
<div class="modal fade" id="modal-AddTipoRetencion" tabindex="-1" role="dialog" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>				
                <h4 class="modal-title" >Añadir el tipo de Retencion</h4>                
            </div>
            <div class="modal-body">
                <form autocomplete="off">					
                    <div class="form-group">
						<label>Retencion:</label>
						<select name="retencion" id= "retencion" class="form-control selectpicker" data-live-search="true">
							@foreach($tiposRetenciones as $tiposRetencion)
								<option value="{{$tiposRetencion->WTCode}}">{{$tiposRetencion->WTName}}</option>
							@endforeach
						</select>
					</div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="modalguardarAMEX" type="button" class="btn btn-success">Guardar</button>
            </div>
        </div>
    </div>
</div>
@endsection

<!--SCRIPTS PARA BOTONES -->

<script src="{{asset('DataTables\jQuery-3.3.1\jquery-3.3.1.js')}}"></script>
<script src="{{asset('DataTables\DataTables-1.10.18\js\jquery.dataTables.min.js')}}"></script>
<script src="{{asset('DataTables\Buttons-1.5.6\js\dataTables.buttons.min.js')}}"></script>
<script src="{{asset('DataTables\Buttons-1.5.6\js\buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('DataTables\DataTables-1.10.18\js\dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('DataTables\JSZip-2.5.0\jszip.min.js')}}"></script>
<script src="{{asset('DataTables\pdfmake-0.1.36\pdfmake.min.js')}}"></script>
<script src="{{asset('DataTables\pdfmake-0.1.36\vfs_fonts.js')}}"></script>
<script src="{{asset('DataTables\Buttons-1.5.6\js\buttons.html5.min.js')}}"></script>

<script>

$(document).ready(function() {
	var docDate = $('#folio');
	$('.btn-sap').click(function(){
		$('#modal-SAP').modal('show');	
	});
	$('#modaliniciarSesion').click(function() {
        $("#modaliniciarSesion").html('aaaa');
        var button =  $(this);
		if(!$('#usuarioSAP').val() || !$('#ClaveS').val() ){
            alert('El usuario o clave no han sido ingresados');
            return false;
        }
		$.ajax({
            async: false,
            type: "POST",
            url: "/subirSAP",
            dataType: "json",
            data: {
                "_token": "{{csrf_token()}}",
                'id_folio' : $('#id_header_folio').val(),
				'username' : $('#usuarioSAP').val(),
				'password' : $('#ClaveS').val()
            },
            beforeSend: function(){
                console.log('SE INICIO');
                $("#modaliniciarSesion").html('<br>Registrando <img src="http://www.summitmx.com:8001/fletes/images/loading-buffering.gif" width="20px" />');
            },
            success: function(data) {
                console.log(data);
                $("#modaliniciarSesion").html('Iniciar Sesion');
                if (data.status === false) {
                    alert(data.jsonData);
                    return false;
                }
                alert(data.jsonData);
                setInterval(function() {
                    location.reload();
                }, 2000);
            },
        });
    });

	//Modal para ver una pantalla para temas AMEX
	$('#row .btn-warning').click(function(){
		$id_header_folio = (this.id);
		function getData(){
            return $.ajax({
                async: false,
                type: "POST",
                url: "/detalleAccountAMEX",
                data: {
                    "_token": "{{csrf_token()}}",
                    'id_header_folio' : $id_header_folio
                },
                dataType: "json"
            });     
        }

        getData().done(function(result) {              
			$('#modal-AMEX').modal('show');	
        })
        .fail(function() {
            swal.fire({
                text: "Ocurrio un error al momento de traer los datos ",
                type: "error",
                confirmButtonColor: '#d33',
                confirmButtonText: "Ok",
                closeOnConfirm: true
            });
        });	
	});

    // Modal para eliminar la retencion 
    $('#datatableAMEX .btn-danger').click(function(){
        $.ajax({		
            type: "POST",
            url: "/DeleteRetencion",
            data: {
                "_token": "{{csrf_token()}}",
                "id_detalleAmex": $id_detalleAmex
            },
            dataType: "json",
            success: function(data){
                console.log(data);
                if ((data.errors))
                {
                    Swal.fire({
                        position: 'top-center',
                        type: 'error',
                        title: 'Ocurrio un error al momento de guardar',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
                else
                {
                    Swal.fire({
                        position: 'top-center',
                        type: 'success',
                        title: 'Se actualizo la informacion correctamente',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#modal-AddTipoRetencion').modal('hide');
                    location.reload();
                }
            }
        });
    });

	//Modal para habilitar el tipo de retencion 
	$('#datatableAMEX .btn-warning').click(function() {		
		$id_detalleAmex = (this.id);
		$('#modal-AddTipoRetencion').modal('show');	
    });

	//Modal para guardar el tipo de retencion y mostrar la info en la tabla
	$('#modalguardarAMEX').click(function() {
		$.ajax({		
            type: "POST",
            url: "/updateTipoAMEX",
            data: {
                "_token": "{{csrf_token()}}",
                "WTCode" : $('#retencion').val(),
                "id_detalleAmex": $id_detalleAmex
            },
            dataType: "json",
            success: function(data){
                console.log(data);
                if ((data.errors))
                {
                    Swal.fire({
                        position: 'top-center',
                        type: 'error',
                        title: 'Ocurrio un error al momento de guardar',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
                else
                {
                    Swal.fire({
                        position: 'top-center',
                        type: 'success',
                        title: 'Se actualizo la informacion correctamente',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#modal-AddTipoRetencion').modal('hide');
                    location.reload();
                }
            }
        });
    });

	//Modal para habilitar el tipo de retencion 
	$('#datatableAMEX .btn-success').click(function() {		
		$id_detalleAmex = (this.id);
		$('#modal-cuentaAMEX').modal('show');	
    });

	$('#modalguardaritemAMEX').click(function() {
		$.ajax({
            type: "POST",
            url: "/updatecuentaitemAMEX",
            data: {
                "_token": "{{csrf_token()}}",
                "AcctCode" : $('#cuenta2').val(),
                "id_detalleAmex": $id_detalleAmex
            },
            dataType: "json",
            success: function(data){
                console.log(data);
                if ((data.errors))
                {
                    Swal.fire({
                        position: 'top-center',
                        type: 'error',
                        title: 'Ocurrio un error al momento de guardar',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
                else
                {
                    Swal.fire({
                        position: 'top-center',
                        type: 'success',
                        title: 'Se actualizo la informacion correctamente',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#modal-cuentaAMEX').modal('hide');
                    location.reload();
                }
            }
        });
    });

	//Modal para aclaracion de informacion 
	$('#datatable .btn-warning').click(function() {		
		$id_rep_gasto = (this.id);
		function getData(){
            return $.ajax({
                async: false,
                type: "POST",
                url: "/detalleAccount",
                data: {
                    "_token": "{{csrf_token()}}",
                    'id_rep_gasto' : $id_rep_gasto
                },
                dataType: "json"
            });     
        }

        getData().done(function(result) {              
            $bom = result.detalles[0].AccountCode;
            $('#cuenta').val($bom).change();
			// alert($bom);	
			//Traer la informacion de la tabla de SAP 
			$('#modal-cuenta').modal('show');	
        })
        .fail(function() {
            swal.fire({
                text: "Ocurrio un error al momento de traer los datos ",
                type: "error",
                confirmButtonColor: '#d33',
                confirmButtonText: "Ok",
                closeOnConfirm: true
            });
        });		
    });

	$('#modalguardar').click(function() {
        // alert($id_rep_gasto);
		// alert($('#cuenta').val());
		$.ajax({
			// 			
            type: "POST",
            url: "/updatecuenta",
            data: {
                "_token": "{{csrf_token()}}",
                "AcctCode" : $('#cuenta').val(),
                "id_rep_gasto": $id_rep_gasto
            },
            dataType: "json",
            success: function(data){
                console.log(data);
                if ((data.errors))
                {
                    Swal.fire({
                        position: 'top-center',
                        type: 'error',
                        title: 'Ocurrio un error al momento de guardar',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
                else
                {
                    Swal.fire({
                        position: 'top-center',
                        type: 'success',
                        title: 'Se actualizo la informacion correctamente',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#modal-cuenta').modal('hide');
                    location.reload();
                }
            }
        });
    });
	
    $('#datatable').DataTable( {
		// Configure Export Buttons
		dom: 'Bfrtip',
		bAutoWidth: false,
		"order": [[ 7, "asc" ]],
		lengthChange: false,
		"searching": false,
		"info": false,
		"language": {
    	"paginate": {
      		"next": ">",
			"previous": "<"
    	}
  	},
    buttons: [
		{
        extend: 'excelHtml5',
		className: 'btn btn-success',
        title: null,
		footer: true,
		autoFilter: false,
		extension: '.xlsx',
		filename: 'Reporte de gastos de viaje '+ '{{$folio->id_header_folio}}',
		customize: function( xlsx ) {
			var sheet = xlsx.xl.worksheets['sheet1.xml'];
    		var col = $('col', sheet);
    		col.each(function () {
				$(col[2]).attr('width', 1.8	);
				$(col[5]).attr('width', 1.8);
				$(col[6]).attr('width', 1.8);
   			});
            }
        },
		{
        extend: 'csvHtml5',
        title: null,
		className: 'btn btn-success',
		footer: true,
		filename: 'Reporte de gastos de viaje '+ '{{$folio->id_header_folio}}',
	
        }    
    ]
	 } );
} );
</script>
