@extends ('layout.admin')
@section('contenido')
	<div class="row">
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
			<h3>Solicitudes pendientes de facturas de vuelo</h3>
			@include('administracion.vuelos.search')
		</div>				
	</div>
	<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
		@if(session()->has('msg'))    		
			<div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12 alert alert-success" role="alert" >
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true" >&times;</button>
	  			<strong>{{ session()->get('msg') }}</strong>
			</div>
		@endif
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="table-responsive">
				<table class="table table-striped table-bordered table-condensed table-hover">					
					<thead>
						<th>No. Folio</th>
						<th>Fecha Salida</th>
						<th>Fecha Llegada</th>
						<th>Nombre</th>
						<th>Tipo</th>
						<th>Destino</th>
						<th>Criterio</th>
					</thead>
					@foreach ($folios as $fol)
					<tr>
						<td>{{ $fol->folio }}</td>
						<td>{{ date('d-m-Y', strtotime($fol->fecha_salida)) }}</td>
						<td>{{ date('d-m-Y', strtotime($fol->fecha_llegada)) }}</td>
						<td>{{ $fol->name }}</td>
						<td>{{ $fol->tipo }}</td>	
						<td>{{ $fol->destino }}</td>
						<td>{{ $fol->criterio }}</td>
						<td>
							<a href="{{URL::action('FlightsController@show', $fol->folio)}}"><button class="btn btn-success">Adjuntar factura</button></a>

							<a href="" data-target='#modal-delete-{{$fol->folio}}' data-toggle="modal"><button class="btn btn-danger">Descartar solicitud</button></a>								
						</td>
					</tr>					
					@include('administracion.vuelos.modal')					
					@endforeach
				</table>
			</div>
			{{$folios->render()}}
		</div>
	</div>
@endsection