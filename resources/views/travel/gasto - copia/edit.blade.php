@extends ('layout.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<h3>Capturar Solicitud: {{ $folio->id_header_folio}}</h3>
		@if (count($errors)>0)
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
				<li>{{$error}}</li>
				@endforeach
			</ul>
		</div>
		@endif
	</div>
</div>

{!!Form::model($folio,['method'=>'PATCH','route'=>['gasto.update',$folio->id_header_folio]])!!}
{{Form::token()}}
<div class="row">
	<input type="hidden" name="id" value="{{$folio->id_header_folio}}">
	<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
		<div class="form-group">
			<label for="tipo">TIPO / TYPE </label>
			<p>{{$folio->tipo}}</p>
		</div>
	</div>
	<div class="col-lg-4 col-sm-4 col-md-4 col-xs-4">
		<div class="form-group">
			<label for="name">NOMBRE COMPLETO / FULL NAME</label>
			<p>{{$folio->name}}</p>
		</div>
	</div>
	<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
		<div class="form-group">
			<label for="destino">DESTINO / DESTINATION</label>
			<p>{{$folio->destino}}</p>
		</div>
	</div>
	<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
		<div class="form-group">
			<label for="proposito">PROPOSITO / PURPOSES</label>
			<p>{{$folio->proposito}}</p>
		</div>
	</div>
	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
		<div class="form-group">
			<label for="periodo">PERIODO / PERIOD</label>
		</div>
	</div>
	<div class="col-lg-4 col-sm-4 col-md-4 col-xs-4">
		<div class="form-group">
			<label for="fecha_salida">DE / FROM</label>
			<p>{{ date('d-m-Y', strtotime($folio->fecha_salida)) }}</p>
		</div>
	</div>
	<div class="col-lg-4 col-sm-4 col-md-4 col-xs-4">
		<div class="form-group">
			<label for="fecha_salida">A / TO</label>
			<p>{{date('d-m-Y', strtotime($folio->fecha_llegada))}}</p>
		</div>
	</div>
	<div class="col-lg-4 col-sm-4 col-md-4 col-xs-4">
		<div class="form-group">
			<label for="fecha_salida">DIAS / DAYS</label>
			<p>{{$folio->dias}}</p>
		</div>
	</div>
	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
		<div class="form-group">
			<label for="fecha_salida">DETALLES DEL VUELO/ FLIGHT DETAILS</label>
			<p>{{$folio->criterio}}</p>
		</div>
	</div>
	<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
		<div class="form-group">
			<label for="id_moneda">ANTICIPO DE VIAJE / TRIP ADVANCE</label>
			<p>{{$folio->moneda}}</p>
		</div>
	</div>
	<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
		<div class="form-group">
			<label for="anticipo">ANTICIPO / ADVANCE PAYMET</label>
			<p>{{$folio->anticipo}}</p>
		</div>
	</div>

</div>
<div class="row">
	<div class="panel panel-primary">
		<div class="panel-body">
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
				<div class="form-group">
					<label for="fecha_gasto">Fecha</label>
					<input type="date" name="pfecha_gasto" id="pfecha_gasto" class="form-control">
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label for="gasto_tarjeta">Gastos con tarjeta</label>
					<input type="number" name="pgasto_tarjeta" id="pgasto_tarjeta" class="form-control" placeholder="Expenses with card">
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label for="gasto_efectivo">Gastos en efectivo</label>
					<input type="number" name="pgasto_efectivo" id="pgasto_efectivo" class="form-control" placeholder="Expenses in cash">
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label for="viatico">Viatico</label>
					<input type="number" name="pviatico" id="pviatico" class="form-control" placeholder="Food quote">
				</div>
			</div>
			
			<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
				<div class="form-group">
					<button type="button" id="bt_add" class="btn btn-primary">Agregar</button>
				</div>
			</div>

			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="table-responsive">
					<table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
						<thead style="background-color:#A9D0F5">
							<th>Opciones</th>
							<th>Fecha</th>
							<th>Gastos con tarjeta</th>
							<th>Gastos en efectivo</th>
							<th>Viatico</th>
							<th>Subtotal</th>
						</thead>
						<tfoot>
							<th>TOTAL</th>			
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th><h4 id="total">S/. 0.00</h4><input type="hidden" name="total_venta" id="total_venta"></th>
						</tfoot>
						<tbody>
							
								@foreach($detalles as $detalle)
									<tr>				
										<td>Opcion</td>
										<td>{{$detalle->fecha_gasto}}</td>
										<td>{{$detalle->gasto_tarjeta}}</td>
										<td>{{$detalle->gasto_efectivo}}</td>
										<td>{{$detalle->viatico}}</td>
										<td>subtotal</td>
									</tr>
								@endforeach
								
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" id="guardar">
		<div class="form-group">
			<input type="hidden" name="_token" value="{{ csrf_token() }}"></input>
			<button class="btn btn-primary" type="submit">Guardar</button>
			<button class="btn btn-danger" type="reset">Cancelar</button>
		</div>
	</div>
</div>

{!!Form::close()!!}		

@push ('scripts')

	<script >

		$(document).ready(function(){
			$('#bt_add').click(function(){
				agregar();
			});	
		});
		
		var cont=0;
		total=0;
		subtotal=[];
		$("#guardar").hide();

		function agregar()
		{
			//datosArticulo=document.getElementById('pidarticulo').value.split('_');

			//idarticulo=datosArticulo[0];
			//articulo=$("#pidarticulo option:selected").text();
			fecha_gasto=$("#pfecha_gasto").val();

			gasto_tarjeta=(parseFloat($("#pgasto_tarjeta").val()) || 0);
			//gasto_tarjeta=parseInt($("#pgasto_tarjeta").val());
			gasto_efectivo=(parseFloat($("#pgasto_efectivo").val()) || 0);
			//gasto_efectivo=parseInt($("#pgasto_efectivo").val());
			viatico=(parseFloat($("#pviatico").val()) || 0);
			//viatico=parseInt($("#pviatico").val());

			if (fecha_gasto!="" && gasto_efectivo!="" && viatico!="") 
			{
				
					subtotal[cont]=(gasto_efectivo+gasto_tarjeta+viatico);
					total=total+subtotal[cont];

					//var fila='<tr class="selected" id="fila'+cont+'"><td><button type="button" class="btn btn-warning" onclick="eliminar('+cont+');">X</button></td><td><input type="hidden" name="idarticulo[]" value="'+idarticulo+'">'+articulo+'</td><td><input type"number" name="cantidad[]" value="'+cantidad+'"></td><td><input type"number" name="precio_venta[]" value="'+precio_venta+'"></td><td><input type"number" name="descuento[]" value="'+descuento+'"></td><td>'+subtotal[cont]+'</td></tr>';

					var fila='<tr class="selected" id="fila'+cont+'"><td><button type="button" class="btn btn-warning" onclick="eliminar('+cont+');">X</button></td><td><input type="date" name="fecha_gasto[]" value="'+fecha_gasto+'"></td><td><input type"number" name="gasto_tarjeta[]" value="'+gasto_tarjeta+'"></td><td><input type"number" name="gasto_efectivo[]" value="'+gasto_efectivo+'"></td><td><input type"number" name="viatico[]" value="'+viatico+'"></td><td>'+subtotal[cont]+'</td></tr>';

					cont++;
					limpiar();
					$("#total").html("S/ " + total);
					$("#total_venta").val(total);
					evaluar();
					$('#detalles').append(fila);
				

			}else
			{
				alert("Error al ingresar el detalle de la venta, revise los datos")
			}
		}

		function limpiar(){
			$('#pfecha_gasto').val("");
			$("#pgasto_tarjeta").val("");
			$("#pgasto_efectivo").val("");
			$("#pviatico").val("");
		}

		function evaluar(){

			if (total>0) {
				$("#guardar").show();
			}
		}
		function eliminar(index){
			total=total-subtotal[index];
			$("#total").html("S/. "+ total);
			$("#total_venta").val(total);
			$("#fila" + index).remove();
			evaluar();
		}

	</script>

@endpush

@endsection