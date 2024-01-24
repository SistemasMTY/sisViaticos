@extends ('layout.admin')
@section ('contenido')

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<h3>Lista de Gastos</h3>
	</div>
</div>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		@if(session()->has('msg'))

			<div class=" col-lg-6 col-md-6 col-sm-6 col-xs-12 alert alert-danger" role="alert" >
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true" >&times;</button>
					<strong>{{ session()->get('msg') }}</strong>
			</div>
		@endif
	</div>
</div>
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
	<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
	        <div class="form-group">
	         	<label for="eq_computo">EQUIPO DE COMPUTO / COMPUTER EQUIPMENT</label>
	         	<p>{{$folio->eq_computo}}</p>
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
			<label for="fecha_llegada">A / TO</label>
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
	<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
		<div class="form-group">
			<label for="evidencia">EVIDENCIA DE VIAJE / TRAVEL EVIDENCE</label>
		</div>
	</div>
	<div class="col-lg-4 col-sm-4 col-md-4 col-xs-4">
		<div class="form-group">
			<label for="comentarios">COMENTARIOS DE VIAJE / TRIP COMMENTS</label>
			<p>{{$folio->evidencia_viaje}}</p>
		</div>
	</div>
	<div class="col-lg-4 col-sm-4 col-md-4 col-xs-4">
		<div class="form-group">
			<label for="archivo de evidencia">DOCUMENTO EVIDENCIA/ EVIDENCE DOCUMENT</label>
			<p>
			@if(isset($folio->pdfevidencia))
				<a target="_blank" href="{{asset('imagenes/folios/'.$folio->id_header_folio.'/'.$folio->pdfevidencia)}}" onclick=""><img src="{{asset('imagenes/folios/unnamed.png')}}" alt="{{$folio->pdfevidencia}}" height="30px" width="30px" class="img-thumbnail">{{$folio->pdfevidencia}}</a>
			@else
				<img src="{{asset('imagenes/folios/noFile.png')}}" alt="{{$folio->pdfevidencia}}" height="30px" width="30px" class="img-thumbnail">
			@endif
			</p>
		</div>
	</div>
</div>
<div class="row">
	<div class="panel panel-primary">
		<div class="panel-body">
			

			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="table-responsive">
					<table id="nacional" class="table table-striped table-bordered table-condensed table-hover">
						<thead style="background-color:#A9D0F5">
							<th>Fecha Factura</th>
							<th>Proveedor</th>
							<th>No Factura</th>
							<th>Gasto</th>
							<th>Metodo de Pago</th>
							<th>Importe</th>
							<th>I.V.A.</th>
							<th>Otros Impuestos</th>
							<th width="100px">Subtotal</th>
							<th>Comentario</th>
						</thead>
						<tfoot>
							<th>TOTAL</th>			
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th><h4>$ {{number_format($folio->all_total,2)}}</h4></th>
							<th></th>
						</tfoot>
						<tbody>
								@foreach($detalles as $detalle)
									<tr>				
										<td>{{$detalle->fecha_factura}}</td>
										<td>{{$detalle->proveedor}}</td>
										<td>{{$detalle->noFactura}}</td>
										<td>{{$detalle->nomGasto}}</td>
										<td>{{$detalle->metodoPago}}</td>
										<td>{{$detalle->importe}}</td>
										<td>{{$detalle->IVA}}</td>
										<td>{{$detalle->otro_impuesto}}</td>
										<td>{{$detalle->subtotal}}</td>
										<td>{{$detalle->comentarios}}</td>
									</tr>
								@endforeach
						</tbody>
					</table>
					<table id="internacional" class="table table-striped table-bordered table-condensed table-hover"style="display: none;">
						<thead style="background-color:#A9D0F5">
							<th>Opciones</th>
							<th>Fecha Factura</th>
							<th>Proveedor</th>
							<th>No Factura</th>
							<th>Gasto</th>
							<th>Metodo de Pago</th>
							<th>Moneda</th>
							<th>Importe</th>
							<th>I.V.A.</th>
							<th>Otros Impuestos</th>
							<th>Subtotal</th>
							<th width="100px">Subtotal en pesos</th>
							<th>Comentarios</th>
						</thead>
						<tfoot>
						<tr>
							<th>TOTAL</th>			
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th><h4><strong>$ {{number_format($folio->all_total,2)}}</strong></h4></th>
							<th></th>
						</tr>
						</tfoot>
						<tbody>				
								@foreach($detallesint as $detalle)
									<tr>				
										<td><a href="" data-target='#modal-delete-{{$detalle->id_detalle_folio}}' data-toggle="modal" class="btn btn-danger fa fa-eraser"></a>
										</td>
										<td>{{$detalle->fecha_factura}}</td>
										<td>{{$detalle->proveedor}}</td>
										<td>{{$detalle->noFactura}}</td>
										<td>{{$detalle->nomGasto}}</td>
										<td>{{$detalle->metodoPago}}</td>
										<td>{{$detalle->moneda}}</td>
										<td>{{$detalle->importe}}</td>
										<td>{{$detalle->IVA}}</td>
										<td>{{$detalle->otro_impuesto}}</td>
										<td>{{$detalle->subtotalotro}}</td>
										<td>{{$detalle->subtotal}}</td>
										<td>{{$detalle->comentarios}}</td>
									</tr>
									@include('travel.gasto.modal')
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
			@if(isset($FlightPTE->id_header_folio))
			<label for="NoVuelo">El departamento de Administración no ha agregado el comprobante del gasto de vuelo, 
				por lo cual no puede enviarse a autorización. Favor de comunicarse con el departamento para mas información</label>			
			@elseif(isset($folio->evidencia_viaje) && $folio->anticipo <= $folio->all_total)
			<a href="{{ route('expensefolio', ['id' => $folio->id_header_folio,'token' => $folio->_token]) }}"><button class="btn btn-primary" type="submit">Enviar</button></a>
			@elseif($folio->anticipo > $folio->all_total && isset($folio->evidencia_viaje))
			<label for="Noevidencia">El anticipo de viaje es mayor a lo comprobado, por favor, verifique su información</label>			
			@else
			<label for="Noevidencia">No se agrego la evidencia de viaje o el anticipo es mayor a lo comprobado, por favor, verifique su información</label>
			@endif
			<a href="{{ url('travel/gasto') }}" class="btn btn-danger">Cancelar</a>
		</div>
	</div>
</div>

{!!Form::close()!!}
@push ('scripts')

<script >

	var cont=0;
	total={{$folio->all_total}};
	$("#total").html("$/ " + total);
	subtotal=[];
	$(document).ready(function(){
	$Tipo = "{{$folio->tipo}}";
	$fechall = "{{$folio->fecha_llegada}}";
		tablan = document.getElementById("nacional");
		tablai = document.getElementById("internacional");
		if ($Tipo == "Internacional" && $fechall >= '2019-10-05 00:00:00'){
				tablai.style.display='block';

				tablan.style.display='none';
			}
			else{
				tablai.style.display='none';

				tablan.style.display='block';
			}
	});


	</script>

@endpush
@endsection