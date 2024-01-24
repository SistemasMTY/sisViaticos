@extends ('layout.admin')
@section('contenido')
	<div class="row">
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
			<h3>Listado de Solicitudes</h3>
			<h3>
				<a href="solicitud/create"><button class="btn btn-success">Nuevo</button></a>
			</h3>			
			@include('travel.solicitud.search')
		</div>				
	</div>
	@if ($conteoProceso[0]->Anticipos >= 2)
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
			<h4><i class="fa fa-exclamation-triangle"></i> Folios abiertos</h4>
			Se ha encontrado que cuenta con 2 Folios abiertos, por lo cual no podra enviar a autorizacion las solicitudes que esten creadas
			</div>
		</div>
	</div>
	@endif
	<div class="row">
		
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

			<div class="table-responsive">
				<table class="table table-striped table-bordered table-condensed table-hover">
					
					<thead>
						<th>No. Folio</th>
						<th>Fecha</th>
						<th>Tipo</th>
						<th>Destino</th>
						<th>Fecha Salida</th>
						<th>Fecha Llegada</th>
						<th>Anticipo</th>
						<th>Status</th>
					</thead>
					@foreach ($folios as $fol)
					<tr>
						<td>{{ $fol->folio }}</td>
						<td>{{ date('d-m-Y', strtotime($fol->fecha)) }}</td>
						<td>{{ $fol->tipo }}</td>	
						<td>{{ $fol->destino }}</td>
						<td>{{ date('d-m-Y', strtotime($fol->fecha_salida)) }}</td>
						<td>{{ date('d-m-Y', strtotime($fol->fecha_llegada)) }}</td>
						<td>{{ $fol->anticipo}} </td>
						<td>{{ $fol->descripcion }} {{ $fol->autorizador}}</td>
						<td>
							<!--<a href="#"><button disabled class="btn btn-info">Editar</button></a>-->
							@if ($fol->status=='C' || $fol->status=='1SR' || $fol->status=='2SR' || $fol->status=='3SR')
								<a href="{{URL::action('FolioController@edit', $fol->folio)}}"><button class="btn btn-info">Editar</button></a>
							@else								
							@endif

							@if (($fol->status=='C' || $fol->status=='1SR' || $fol->status=='2SR' || $fol->status=='3SR') && $conteoProceso[0]->Anticipos < 2)
								<a href="{{URL::action('FolioController@show', $fol->folio)}}"><button class="btn btn-success">Confirmar</button></a>
								
							@endif

							@if ($conteoProceso[0]->Anticipos >= 2 && ($fol->status=='C' || $fol->status=='1SR' || $fol->status=='2SR' || $fol->status=='3SR'))
							<a href="{{URL::action('FolioController@show', $fol->folio)}}"><button disabled class="btn btn-success">Confirmar</button></a>
							@endif

							@if($fol->status=='C' || $fol->status=='1SR' || $fol->status=='2SR' || $fol->status=='3SR')
								<a href="" data-target='#modal-delete-{{$fol->folio}}' data-toggle="modal"><button class="btn btn-danger">Eliminar</button></a>								
							@endif
						</td>
					</tr>
					
					@include('travel.solicitud.modal')
					
					@endforeach
				</table>
			</div>
			{{$folios->render()}}
		</div>
	</div>
@endsection