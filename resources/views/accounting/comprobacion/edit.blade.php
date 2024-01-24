@extends ('layout.admin')
@section ('contenido')

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<a href="" data-target='#modal-delete-{{$detalle->id_detalle_folio}}' data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
	</div>
	@include('travel.gasto.modal')
</div>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<h3>Editar Solicitud: {{$detalle->id_detalle_folio}} </h3>

	</div>
</div>


{!!Form::model($detalle,['method'=>'PATCH','route'=>['gasto.update',$detalle->id_detalle_folio]])!!}
{{Form::token()}}
<div class="row">
	<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
		<div class="form-group">
			<label for="proveedor">PROVEEDOR</label>
			<input type="text" name="proveedor" value="{{ $detalle->proveedor }}" class="form-control">
		</div>
	</div>
	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
		<div class="form-group">
			<label for="fecha_factura">FECHA / DATE</label>
			<input type="date" name="fecha_factura" value="{{date('Y-m-d', strtotime($detalle->fecha_factura))}}" class="form-control">
		</div>
	</div>
	<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
		<div class="form-group">
			<label for="gasto_tarjeta">GASTOS EN TARJETA</label>
			<input type="number" name="gasto_tarjeta" required value ="{{$detalle->gasto_tarjeta}}" class="form-control" placeholder="GASTOS EN TARJETA...">
		</div>
	</div>
	<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
		<div class="form-group">
			<label for="gasto_efectivo">GASTOS EN EFECTIVO</label>
			<input type="number" name="gasto_efectivo" required value ="{{$detalle->gasto_efectivo}}" class="form-control" placeholder="GASTOS EN EFECTIVO...">
		</div>
	</div>
	<div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
		<div class="form-group">
			<label for="viatico">VIATICO</label>
			<input type="number" name="viatico" required value ="{{$detalle->viatico}}" class="form-control" placeholder="vIATICO...">
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" id="guardar">
		<div class="form-group">
			<input type="hidden" name="_token" value="{{ csrf_token() }}"></input>
			<button class="btn btn-primary" type="submit">Guardar</button>
		</div>
	</div>
</div>

{!!Form::close()!!}		
@endsection