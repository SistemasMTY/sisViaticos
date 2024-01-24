@extends ('layout.admin')
@section('contenido')
	<div class="row">
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
			<h3>Listado de Solicitudes</h3>
			
			@include('treasury.rembolso.search')
		</div>		

	</div>

	<div class="row">
		
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

			<div class="table-responsive">
				<table class="table table-striped table-bordered table-condensed table-hover">
					
					<thead>
						<th>BRH</th>
						<th>No. Folio</th>
						<th>Fecha</th>
						<th>Nombre</th>
						<th>Tipo</th>
						<th>Destino</th>
						<th>Rembolso</th>
						<th>Status</th>
					</thead>
					@foreach ($folios as $fol)
					<tr>
						<td>{{ $fol->company }}</td>
						<td>{{ $fol->folio }}</td>
						<td>{{ date('d-m-Y', strtotime($fol->fecha)) }}</td>
						<td>{{ $fol->name }}</td>
						<td>{{ $fol->tipo }}</td>	
						<td>{{ $fol->destino }}</td>
						<td>{{number_format(abs($fol->all_total-$fol->anticipo),2)}}</td>
						<td>{{ $fol->descripcion }}</td>
						<td>
								<a href="{{URL::action('RepaymentController@edit', $fol->folio)}} " target="_blank"><button class="btn btn-info">PDF</button></a>
								<a href="" data-target='#modal-delete-{{$fol->folio}}' data-toggle="modal"><button class="btn btn-primary">Rembolso realizado</button></a>
							
						</td>
					</tr>
					
					@include('treasury.rembolso.modal')
					
					@endforeach
				</table>
			</div>
			{{$folios->render()}}
		</div>
	</div>
@endsection