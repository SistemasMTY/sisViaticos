@extends ('layout.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<h3>Solicitud</h3>		
	</div>
</div>
{!!Form::open(array('url'=>'administracion/vuelos','method'=>'POST','autocomplete'=>'off','files'=>'true'))!!}
{{Form::token()}}
	<div class="row">
		<input type="hidden" name="id" value="{{$folio->id_flight}}">
		<input type="hidden" name="id_folio" value="{{$folio->id_header_folio}}">

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
	</div>
	<div class="row" >
		<div class="panel-primary">						
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<h4>Gastos de vuelo</h4>
			</div>		
			<div class="form-check" style="margin-left: 30px">
			    <input class="form-check-input" type="checkbox" name="check" id="check" value="1" onchange="javascript:showContent()" />
			    <label class="form-check-label" for="exampleCheck1">Captura sin XML</label>
			</div>			
			<div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
				@if(session()->has('msg'))    		
					<div class=" col-lg-6 col-md-6 col-sm-6 col-xs-12 alert alert-danger" role="alert" >
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true" >&times;</button>
	  					<strong>{{ session()->get('msg') }}</strong>
					</div>
				@endif
			</div>
			<div class="panel-body">
				<div id="content1">													
					<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
						<div class="form-group">
							<label for="xml">XML</label>
							<input type="file" name="xml" id="xml" class="form-control" accept=".xml" >
						</div>
					</div>
					<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
						<div class="form-group">
							<label for="xpdf">PDF</label>
							<input type="file" name="xpdf" id="xpdf" class="form-control" accept=".pdf" >
						</div>
					</div>
					<div class="col-lg-4 col-sm-4 col-md-6 col-xs-12">
						<div class="form-group">
							<label for="TarjetaPago">TARJETA PAGO</label>
							<select name="xTarjetaPago" class="form-control" id="xTarjetaPago" data-live-search="true">
								<option value="Jo Nagase">Jo Nagase</option>
								<option value="Takehiko Gomi">Takehiko Gomi</option>
								<option value="Kazuk iUehara">Kazuki Uehara</option>
							</select>				
						</div>
					</div>
					<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12" id ="xcontentmoneda" >
						<div class="form-group">
							<label>Moneda</label>
							<select name="xid_moneda" id= "moneda" class="form-control selectpicker">
								@foreach($monedas as $mon)
									<option value="{{$mon->id_moneda}}">{{$mon->moneda}}</option>
								@endforeach
							</select>
						</div>
					</div>	
					<div id="xcontentimporte" class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<div class="form-group">
							<label for="ximportemxn">Importe en pesos (En caso de que la factura no sea en Pesos Mexicanos</label>
							<input type="number" name="ximportemxn" id="ximportemxn" class="form-control" placeholder="Importe en pesos" step=".01">
						</div>
					</div>		  	
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="form-group">
							<input type="hidden" name="_token" value="{{ csrf_token() }}"></input>
							<button class="btn btn-primary" type="submit">Guardar</button>
							<a href="{{url('administracion/vuelos')}}" class="btn btn-danger">Cancelar</a>
						</div>
					</div>
				</div>
				<div id="content2" style="display: none;">					
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<div class="form-group">
						<label for="fecha_factura">Fecha</label>
						<input type="date" name="fecha_factura" id="fecha_factura" class="form-control">
					</div>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<div class="form-group">
						<label for="noFactura">Folio</label>
						<input type="text" name="noFactura"	 id="noFactura" class="form-control" placeholder="Expenses with card">
					</div>
				</div>				
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<div class="form-group">
						<label for="TarjetaPago">TARJETA PAGO</label>
						<select name="TarjetaPago" class="form-control" id="TarjetaPago" data-live-search="true">
							<option value="Jo Nagase">Jo Nagase</option>
							<option value="Takehiko Gomi">Takehiko Gomi</option>
							<option value="Kazuk iUehara">Kazuki Uehara</option>
						</select>				
					</div>
				</div>
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
					<div class="form-group">
						<label for="importe">Importe</label>
						<input type="number" name="importe" id="importe" class="form-control" placeholder="Importe" step=".01" >
					</div>
				</div>
				<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12" id ="contentmoneda" >
					<div class="form-group">
						<label>Moneda</label>
						<select name="id_moneda" id= "moneda" class="form-control selectpicker">
							@foreach($monedas as $mon)
								<option value="{{$mon->id_moneda}}">{{$mon->moneda}}</option>
							@endforeach
						</select>
					</div>
				</div>				
				<div class="col-lg-8 col-sm-8 col-md-8 col-xs-12">
		  			<div class="form-group">
		  				<label for="pdf">PDF</label>
		  				<input type="file" name="pdf" id="pdf" class="form-control" accept=".pdf">
		  			</div>
		  		</div>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<div class="form-group">
						<input type="hidden" name="_token" value="{{ csrf_token() }}"></input>
						<button class="btn btn-primary" type="submit">Guardar</button>
						<a href="{{ url('travel/gasto')}}" class="btn btn-danger">Cancelar</a>
					</div>
				</div>
			</div>
				{!!Form::close()!!}
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="table-responsive">
						<table class="table table-striped table-bordered table-condensed table-hover"">
							<thead style="background-color:#A9D0F5">
								<th>Opciones</th>
								<th>Fecha Factura</th>
								<th>Proveedor</th>
								<th>No Factura</th>									
								<th>Importe</th>
								<th>I.V.A.</th>
								<th>Otros Impuestos</th>
								<th width="100px">Subtotal</th>
								<th>XML</th>
								<th>PDF</th>
							</thead>								
							<tbody>							
								@foreach($foliovuelo as $foliovuelos)
									<tr>				
										<td><a href="" data-target='#modal-delete-{{$foliovuelos->id_header_folio}}' data-toggle="modal" class="btn btn-danger fa fa-eraser"></a>
										</td>
										<td>{{$foliovuelos->fecha_compra}}</td>
										<td>{{$foliovuelos->proveedor}}</td>
										<td>{{$foliovuelos->no_factura}}</td>
										<td>{{$foliovuelos->importe}}</td>
										<td>{{$foliovuelos->IVA}}</td>
										<td>{{$foliovuelos->otro_impuesto}}</td>
										<td>{{$foliovuelos->total_vuelo}}</td>
										<td>
											<a target="_blank" href="{{asset('imagenes/folios/'.$folio->id_header_folio.'/'.$foliovuelos->xml)}}" onclick=""><img src="{{asset('imagenes/folios/xml.png')}}" alt="{{$foliovuelos->xml}}" height="30px" width="30px" class="img-thumbnail"></a>													
										</td>											
										<td>
											<a target="_blank" href="{{asset('imagenes/folios/'.$folio->id_header_folio.'/'.$foliovuelos->pdf)}}" onclick=""><img src="{{asset('imagenes/folios/pdf.png')}}" alt="{{$foliovuelos->pdf}}" height="30px" width="30px" class="img-thumbnail"></a>													
										</td>																					
									</tr>
								@endforeach
							</tbody>							
						</table>							
					</div>
				</div>
			</div>
		</div>
		
	</div>
	<div class="col-lg-9 col-md-9 col-sm-9 col-xs-12"></div>
	<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12" id="guardar">
		<div class="form-group">
			<input type="hidden" name="_token" value="{{ csrf_token() }}"></input>
			
			<a href="{{ route('terminarGuardado', ['id' => $folio->id_flight, 'id_folio' => $folio->id_header_folio,'token' => $folio->_token]) }}" class="btn btn-danger">Terminar</a>
		</div>
	</div>
	

@push ('scripts')
<script>
	$(document).ready(function(){

	});

	function showContent() {
		$Tipo = "{{$folio->tipo}}";
		$fecha = "{{$folio->fecha_llegada}}";
	 	element1 = document.getElementById("content1");
        element2 = document.getElementById("content2");
        check = document.getElementById("check");
		
        if (check.checked) {
            element2.style.display='block';

            element1.style.display='none';
		}
        else {
            element2.style.display='none';

            element1.style.display='block';
        }
    }
</script>


@endpush
@endsection