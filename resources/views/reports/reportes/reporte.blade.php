@extends ('layout.admin')
@section ('contenido')
<title>Reporte de gastos de viaje: {{$folio->id_header_folio}} </title>
<link rel="stylesheet" href="{{asset('DataTables\DataTables-1.10.18\css\dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{asset('DataTables\Buttons-1.5.6\css\buttons.bootstrap4.min.css')}}">

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3>Reporte de gastos de viaje: {{$folio->id_header_folio}} </h3>
	
	</div>
</div>
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
            	<tr>	
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
		if($('#usuarioSAP').val() && $('#ClaveS').val() ){
			$.ajax({
                async: false,
                type: "POST",
                url: "/subirSAP",
                data: {
                    "_token": "{{csrf_token()}}",
                    'id_folio' : {{$folio->id_header_folio}},
					'username' : $('#usuarioSAP').val(),
					'password' : $('#ClaveS').val()
                },
                dataType: "json",
				success: function(data){
                	console.log(data);
					alert(data.jsonData);
				}
            });

		}
		else{
			alert('El usuario o clave no han sido ingresados');
		}
		location.reload();
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
