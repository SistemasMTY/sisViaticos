@extends ('layout.admin')
@section('contenido')
	<div class="row">
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
			<h3>Listado de Solicitudes 
			
			</h3>
			@include('travel.print.search')
		</div>		

	</div>

	<div class="row">
		
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

			<div class="table-responsive">
				<table class="table table-striped table-bordered table-condensed table-hover">
					
					<thead>
						<th>No. Folio</th>
						<th>Fecha</th>
						<th>Tipo</th>
						<th>Destino</th>
						<th>Anticipo</th>
						<th>Status</th>
					</thead>
					@foreach ($folios as $fol)
					<tr>
						<td>{{ $fol->folio }}</td>
						<td>{{ date('d-m-Y', strtotime($fol->fecha)) }}</td>
						<td>{{ $fol->tipo }}</td>	
						<td>{{ $fol->destino }}</td>
						<td>{{ $fol->anticipo}} </td>
						<td>{{ $fol->descripcion }}</td>
						<td>
							@if ($fol->status=='6SA' || $fol->status=='E' || $fol->status=='3SA')
								<a href="{{URL::action('PDFController@view', $fol->folio)}}" target="_blank" class="btn btn-info" role="button">Ver</a>
							@else
								<a href="#" class="btn btn-info" role="button" disabled>Ver</a>
							@endif
						</td>
					</tr>
					
					@endforeach
				</table>
			</div>
			{{$folios->render()}}
		</div>
	</div>
@endsection