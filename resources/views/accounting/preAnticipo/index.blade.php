@extends ('layout.admin')
@section('contenido')
	<div class="row">
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
			<h3>Listado de Anticipo por Autorizar</h3>
			
			@include('accounting.preAnticipo.search')
		</div>		

	</div>

	<div class="row">
		
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

			<div class="table-responsive">
				<table class="table table-striped table-bordered table-condensed table-hover">
					
					<thead>
						<th>BRH</th>
						<th>No. Folio</th>
						<th>Fecha Salida</th>
						<th>Fecha Llegada</th>
						<th>Nombre</th>
						<th>Tipo</th>
						<th>Destino</th>
						<th>Anticipo</th>
						<th>Status</th>
					</thead>
					@foreach ($folios as $fol)
					<tr>
						<td>{{ $fol->company }}</td>
						<td>{{ $fol->folio }}</td>
						<td>{{ date('d-m-Y', strtotime($fol->fecha_salida)) }}</td>
						<td>{{ date('d-m-Y', strtotime($fol->fecha_llegada)) }}</td>
						<td>{{ $fol->name }}</td>
						<td>{{ $fol->tipo }}</td>	
						<td>{{ $fol->destino }}</td>
						<td>{{ $fol->anticipo}} </td>
						<td>{{ $fol->descripcion }}</td>
						<td>
								<a href="{{URL::action('PreAnticipoController@edit', $fol->folio)}} " target="_blank"><button class="btn btn-info">PDF</button></a>
								
								 <a href="" data-target='#modal-delete-{{$fol->folio}}' data-toggle="modal"><button class="btn btn-primary">Liberar Anticipo</button></a>
							
						</td>
					</tr>
					
					@include('accounting.preAnticipo.modal')
					
					@endforeach
				</table>
			</div>
			{{$folios->render()}}
		</div>
	</div>
@endsection