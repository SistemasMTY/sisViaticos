@extends ('layout.admin')
@section('contenido')
	<div class="row">
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
			<h3>Listado de Solicitudes 
			
			</h3>
			@include('travel.gasto.search')
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
							<!--<a href="#"><button disabled class="btn btn-info">Editar</button></a>-->
							@if ($fol->status=='3SA' || $fol->status=='1SR' || $fol->status=='2SR' || $fol->status=='3SR')
								<a href="{{URL::action('DetalleFolioController@edit', $fol->folio)}}"><button class="btn btn-info">Captura</button></a>
							@else
								<a href="{{URL::action('DetalleFolioController@edit', $fol->folio)}}"><button disabled class="btn btn-info">Captura</button></a>
							@endif

							@if ($fol->status=='3SA' || $fol->status=='1SR' || $fol->status=='2SR' || $fol->status=='3SR')
								<a href="{{URL::action('DetalleFolioController@show', $fol->folio)}}"><button class="btn btn-success">Confirmar</button></a>
							@else
								<a href="{{URL::action('DetalleFolioController@show', $fol->folio)}}"><button disabled class="btn btn-success">Confirmar</button></a>
							@endif

							<!--@if($fol->status=='C' || $fol->status=='1SR' || $fol->status=='2SR' || $fol->status=='3SR')
								<a href="" data-target='#modal-delete-{{$fol->folio}}' data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>
							@else
								<a href="" data-target='#modal-delete-{{$fol->folio}}' data-toggle="modal"><button disabled class="btn btn-danger">Eliminar</button></a>
							@endif
							-->
						</td>
					</tr>
					
					@include('travel.gasto.modal')
					
					@endforeach
				</table>
			</div>
			{{$folios->render()}}
		</div>
	</div>
@endsection