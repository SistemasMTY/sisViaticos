@extends ('layout.admin')
@section ('contenido')

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<h3>Lista de Gastos</h3>
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
</div>
<div class="row">
	<div class="panel panel-primary">
		<div class="panel-body">
			

			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="table-responsive">
					<table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
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
			<a href="{{ route('expensefolio', ['id' => $folio->id_header_folio]) }}"><button class="btn btn-primary" type="submit">Enviar</button></a>
			<a href="{{ url()->previous() }}" class="btn btn-danger">Cancelar</a>
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

	</script>

@endpush
@endsection