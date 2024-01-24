@extends ('layout.admin')
@section ('contenido')

<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<h3>Solicitud</h3>
	</div>
</div>
@include('authorizers.approbation.modal')
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
			<label for="anticipo">ANTICIPO / ADVANCE PAYMENT</label>
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
<div class="row" id="folioDetalle">
	<div class="panel panel-primary">
				
		<div class="panel-body">

			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="table-responsive">
					@if(count($detalles)==0)

					@else
						<table id="nacional" class="table table-striped table-bordered table-condensed table-hover"style="display: none;">
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
								<th>XML</th>
								<th>PDF</th>
								<th>Comentarios</th>
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
								<th></th>
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
										@if(isset($detalle->xml))
											<td>
												<a target="_blank" href="{{asset('imagenes/folios/'.$folio->id_header_folio.'/'.$detalle->xml)}}" onclick=""><img src="{{asset('imagenes/folios/xml.png')}}" alt="{{$detalle->xml}}" height="30px" width="30px" class="img-thumbnail"></a>
											
											</td>
										@else
											<td>
												<img src="{{asset('imagenes/folios/noFile.png')}}" alt="{{$detalle->xml}}" height="30px" width="30px" class="img-thumbnail">
											</td>
										@endif

										@if(isset($detalle->pdf))
											<td>
												<a target="_blank" href="{{asset('imagenes/folios/'.$folio->id_header_folio.'/'.$detalle->pdf)}}" onclick=""><img src="{{asset('imagenes/folios/pdf.png')}}" alt="{{$detalle->pdf}}" height="30px" width="30px" class="img-thumbnail"></a>
													
												</td>
											@else
												<td>
													<img src="{{asset('imagenes/folios/noFile.png')}}" alt="{{$detalle->pdf}}" height="30px" width="30px" class="img-thumbnail">
												</td>
											@endif
											<td>{{$detalle->comentarios}}</td>
										</tr>
									@endforeach
							</tbody>
						</table>
						<table id="internacional" class="table table-striped table-bordered table-condensed table-hover"style="display: none;">
						<thead style="background-color:#A9D0F5">
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
							<th>XML</th>
							<th>PDF</th>
							<th>PDF Cambio</th>
							<th>Comentarios</th>
						</thead>
						<tbody>
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
							<th><h4><strong>$ {{number_format($folio->all_total,2)}}</strong></h4></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</tr>
						</tfoot>				
								@foreach($detallesint as $detalle)
									<tr>				
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
										@if(isset($detalle->xml))
											<td>
												<a target="_blank" href="{{asset('imagenes/folios/'.$folio->id_header_folio.'/'.$detalle->xml)}}" onclick=""><img src="{{asset('imagenes/folios/xml.png')}}" alt="{{$detalle->xml}}" height="30px" width="30px" class="img-thumbnail"></a>												
											</td>
										@else
											<td>
												<img src="{{asset('imagenes/folios/noFile.png')}}" alt="{{$detalle->xml}}" height="30px" width="30px" class="img-thumbnail">
											</td>
										@endif

										@if(isset($detalle->pdf))
											<td>
												<a target="_blank" href="{{asset('imagenes/folios/'.$folio->id_header_folio.'/'.$detalle->pdf)}}" onclick=""><img src="{{asset('imagenes/folios/pdf.png')}}" alt="{{$detalle->pdf}}" height="30px" width="30px" class="img-thumbnail"></a>												
											</td>
										@else
											<td>
												<img src="{{asset('imagenes/folios/noFile.png')}}" alt="{{$detalle->pdf}}" height="30px" width="30px" class="img-thumbnail">
											</td>
										@endif
										@if(isset($detalle->pdfint))
											<td>
												<a target="_blank" href="{{asset('imagenes/folios/'.$folio->id_header_folio.'/'.$detalle->pdfint)}}" onclick=""><img src="{{asset('imagenes/folios/pdf.png')}}" alt="{{$detalle->pdfint}}" height="30px" width="30px" class="img-thumbnail"></a>												
											</td>
										@else
											<td>
												<img src="{{asset('imagenes/folios/noFile.png')}}" alt="{{$detalle->pdfint}}" height="30px" width="30px" class="img-thumbnail">
											</td>
										@endif
										<td>{{$detalle->comentarios}}</td>
									</tr>
									
								@endforeach
						</tbody>

					</table>
					@endif
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="form-group">
					@if($folio->id_status>8)
						<a href="{{URL::action('AutorizadoresController@autorizaGasto', [$folio->id_header_folio,$folio->_token])}}"><button class="btn btn-success"><i class="fa fa-check-square" aria-hidden="true"></i> Aprobar Comprobación</button></a>
						<a href="{{ url('authorizers/approbation')}}" class="btn btn-warning"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i>  Regresar</a>
						<a href="" data-target='#modal-delete-{{$folio->id_header_folio}}' data-toggle="modal" class="btn btn-danger"><i class="fa fa-ban" aria-hidden="true"></i> Denegar Comrobación</a>
					@else
						<a href="{{URL::action('AutorizadoresController@autorizaAnticipo', [$folio->id_header_folio,$folio->_token])}}"><button class="btn btn-success"><i class="fa fa-check-square" aria-hidden="true"></i> Aprobar Solicitud</button></a>

						<a href="{{ url('authorizers/approbation')}}" class="btn btn-warning"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i>  Regresar</a>
						<a href="" data-target='#modal-delete-{{$folio->id_header_folio}}' data-toggle="modal" class="btn btn-danger"><i class="fa fa-ban" aria-hidden="true"></i>  Denegar Solicitud</a>

					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@push ('scripts')

<script>

	$(document).ready(function(){
		$('html,body').animate({
        scrollTop: $("#folioDetalle").offset().top},
		'slow');
		tablan = document.getElementById("nacional");
		tablai = document.getElementById("internacional");
		$Tipo = "{{$folio->tipo}}";
		$fechall = "{{$folio->fecha_llegada}}";
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