@extends ('layout.admin')
@section('contenido')
	<div class="row">
		<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
			<h3>Listado de Solicitudes</h3>
			
			@include('treasury.anticipo.search')
		</div>		

	</div>



	<div class="tabbable"> <!-- Only required for left/right tabs -->
	  <ul class="nav nav-pills">
	    <li class="active">
	    	<a href="#tab1" data-toggle="tab">QRO<!--  <span class="badge">?</span> --></a>
	    </li>
	    <li>
	    	<a href="#tab2" data-toggle="tab">MTY<!--  <span class="badge">?</span> --></a>
	    </li>
	    <li>
	    	<a href="#tab3" data-toggle="tab">SLM<!--  <span class="badage">?</span> --></a>
	    </li>
	  </ul>
	  <div class="tab-content">
	    <div class="tab-pane active" id="tab1">
	      	<div class="row">
		
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

					<div class="table-responsive">
						<table class="table table-striped table-bordered table-condensed table-hover">
							
							<thead>
								<!-- <th>BRH</th> -->
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
								@if($fol->company=="QRO")
									<tr>
										<!-- <td>{{ $fol->company }}</td> -->
										<td>{{ $fol->folio }}</td>
										<td>{{ date('d-m-Y', strtotime($fol->fecha_salida)) }}</td>
										<td>{{ date('d-m-Y', strtotime($fol->fecha_llegada)) }}</td>
										<td>{{ $fol->name }}</td>
										<td>{{ $fol->tipo }}</td>	
										<td>{{ $fol->destino }}</td>
										<td>{{ $fol->anticipo}} </td>
										<td>{{ $fol->descripcion }}</td>
										<td>
												<a href="{{URL::action('TransferController@edit', $fol->folio)}} " target="_blank"><button class="btn btn-info">PDF</button></a>
												<a href="" data-target='#modal-delete-{{$fol->folio}}' data-toggle="modal"><button class="btn btn-primary">Transferencia realizada</button></a>
											
										</td>
									</tr>
									
									@include('treasury.anticipo.modal')
								@endif
							@endforeach
						</table>
					</div>
					{{$folios->render()}}
				</div>
			</div>
	    </div>
	    <div class="tab-pane" id="tab2">
	      <div class="row">
		
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

					<div class="table-responsive">
						<table class="table table-striped table-bordered table-condensed table-hover">
							
							<thead>
								<!-- <th>BRH</th> -->
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
								@if($fol->company=="MTY")
									<tr>
										<!-- <td>{{ $fol->company }}</td> -->
										<td>{{ $fol->folio }}</td>
										<td>{{ date('d-m-Y', strtotime($fol->fecha_salida)) }}</td>
										<td>{{ date('d-m-Y', strtotime($fol->fecha_llegada)) }}</td>
										<td>{{ $fol->name }}</td>
										<td>{{ $fol->tipo }}</td>	
										<td>{{ $fol->destino }}</td>
										<td>{{ $fol->anticipo}} </td>
										<td>{{ $fol->descripcion }}</td>
										<td>
												<a href="{{URL::action('TransferController@edit', $fol->folio)}} " target="_blank"><button class="btn btn-info">PDF</button></a>
												<a href="" data-target='#modal-delete-{{$fol->folio}}' data-toggle="modal"><button class="btn btn-primary">Transferencia realizada</button></a>
											
										</td>
									</tr>
									
									@include('treasury.anticipo.modal')
								@endif
							@endforeach
						</table>
					</div>
					{{$folios->render()}}
				</div>
			</div>
	    </div>
	    <div class="tab-pane" id="tab3">
	      <div class="row">
		
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

					<div class="table-responsive">
						<table class="table table-striped table-bordered table-condensed table-hover">
							
							<thead>
								<!-- <th>BRH</th> -->
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
								@if($fol->company=="SLM")
									<tr>
										<!-- <td>{{ $fol->company }}</td> -->
										<td>{{ $fol->folio }}</td>
										<td>{{ date('d-m-Y', strtotime($fol->fecha_salida)) }}</td>
										<td>{{ date('d-m-Y', strtotime($fol->fecha_llegada)) }}</td>
										<td>{{ $fol->name }}</td>
										<td>{{ $fol->tipo }}</td>	
										<td>{{ $fol->destino }}</td>
										<td>{{ $fol->anticipo}} </td>
										<td>{{ $fol->descripcion }}</td>
										<td>
												<a href="{{URL::action('TransferController@edit', $fol->folio)}} " target="_blank"><button class="btn btn-info">PDF</button></a>
												<a href="" data-target='#modal-delete-{{$fol->folio}}' data-toggle="modal"><button class="btn btn-primary">Transferencia realizada</button></a>
											
										</td>
									</tr>
									
									@include('treasury.anticipo.modal')
								@endif
							@endforeach
						</table>
					</div>
					{{$folios->render()}}
				</div>
			</div>
	    </div>
	  </div>
	</div>






	<!-- <div class="row">
		
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
								<a href="{{URL::action('TransferController@edit', $fol->folio)}} " target="_blank"><button class="btn btn-info">PDF</button></a>
								<a href="" data-target='#modal-delete-{{$fol->folio}}' data-toggle="modal"><button class="btn btn-primary">Transferencia realizada</button></a>
							
						</td>
					</tr>
					
					@include('treasury.anticipo.modal')
					
					@endforeach
				</table>
			</div>
			{{$folios->render()}}
		</div>
	</div> -->
@endsection