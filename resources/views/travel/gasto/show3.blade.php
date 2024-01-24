@extends ('layout.admin')
@section ('contenido')
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
		<h3>Solicitud</h3>
		
	</div>
</div>

{!!Form::open(array('url'=>'travel/gasto','method'=>'POST','autocomplete'=>'off','files'=>'true'))!!}
{{Form::token()}}
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
			<div class="form-check" style="margin-left: 30px">
			    <input class="form-check-input" type="checkbox" name="checkev" id="checkev" value="1" onchange="javascript:showContentev()" />
			    <label class="form-check-label" for="exampleCheck3" id="exampleCheck3">Agregar/Modificar evidencia de viaje</label>
			 </div>	
			 <div class="panel-body">
				<div id="contentevidencia"style="display: none;">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<h4>Evidencia de viaje</h4>
					</div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="form-group">
							<label for="comentarioevidencia">Comentarios de la evidencia</label>
							<input type="text" name="comentarioevidencia" id="comentarioevidencia" class="form-control" placeholder="Comentarios del viaje">
						</div>
					</div>
					<div class="col-lg-12 col-sm-6 col-md-12 col-xs-12">
						<div class="form-group">
							<label for="pdfevidencia">Evidencia del viaje</label>
							<input type="file" name="pdfevidencia" id="pdfevidencia" class="form-control">
						</div>
					</div>
					<div class="form-check col-lg-6 col-sm-6 col-md-6 col-xs-12" style="margin-left: 30px;">
						<label></label>
			    		<input class="form-check-input" type="checkbox" name="checksolo" id="checksolo" value="1" onchange="javascript:showContentsolo()" />
			    		<label class="form-check-label" for="examplechecksolo">Guardar solo la evidencia de viaje</label>
			 		</div>	
				</div>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<h4>Gastos del viaje</h4>
			</div>
			<div class="form-check" style="margin-left: 30px">
			    <input class="form-check-input" type="checkbox" name="check" id="check" value="1" onchange="javascript:showContent()" />
			    <label class="form-check-label" for="exampleCheck1">Captura sin XML</label>
			 </div>		
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				@if(session()->has('msg'))
    		
					<div class=" col-lg-6 col-md-6 col-sm-6 col-xs-12 alert alert-danger" role="alert" >
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true" >&times;</button>
	  					<strong>{{ session()->get('msg') }}</strong>
					</div>
				@endif
			</div>
			
		<div class="panel-body">
			<div id="content1">	
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
					<div class="form-group">
						<label>Tipo Gasto</label>
	                    <select name="xidgasto" class="form-control selectpicker" id="xidgasto" data-live-search="true">
	                        @foreach($gastos as $gasto)
	                        	<option value="{{$gasto->id_gasto}}">{{$gasto->nomGasto}}</option>
	                        @endforeach                          
	                    </select>
					</div>
				</div>
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
					<div class="form-group">
						<label>Metodo Pago</label>
	                    <select name="xmetodo" class="form-control selectpicker" id="metodo" data-live-search="true">
	                        	<option value="Efectivo">Efectivo</option>
	                        	<option value="AMEX">AMEX</option>
	                    </select>
					</div>
				</div>
				<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
		  			<div class="form-group">
		  				<label for="xml">XML</label>
		  				<input type="file" name="xml" id="xml" class="form-control" accept=".xml" required>
		  			</div>
		  		</div>
		  		<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
		  			<div class="form-group">
		  				<label for="xpdf">PDF</label>
		  				<input type="file" name="xpdf" id="xpdf" class="form-control" accept=".pdf" required>
		  			</div>
		  		</div>
			  	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="form-group">
						<label for="xcomentarios">Comentarios</label>
						<input type="text" name="xcomentarios" id="xcomentarios" class="form-control" placeholder="Comentarios">
					</div>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<div class="form-group">
						<input type="hidden" name="_token" value="{{ csrf_token() }}"></input>
						<button class="btn btn-primary" type="submit">Guardar</button>
						<a href="{{url('travel/gasto')}}" class="btn btn-danger">Cancelar</a>
					</div>
				</div>
			</div>

			<div id="content2" style="display: none;">					
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<div class="form-group">
						<label for="fecha_factura">Fecha</label>
						<input type="date" name="fecha_factura" id="fecha_factura" class="form-control" min="{{date('Y-m-d', strtotime($folio->fecha_salida))}}" max="{{date('Y-m-d', strtotime($folio->fecha_llegada))}}" required>
					</div>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<div class="form-group">
						<label for="noFactura">Folio</label>
						<input type="text" name="noFactura"	 id="noFactura" class="form-control" placeholder="Expenses with card" required>
					</div>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<div class="form-group">
						<label>Tipo Gasto</label>
	                    <select name="gasto" class="form-control selectpicker" id="gasto" data-live-search="true">
	                        @foreach($gastos as $gasto)
	                        	<option value="{{$gasto->id_gasto}}">{{$gasto->nomGasto}}</option>
	                        @endforeach                          
	                    </select>
					</div>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
					<div class="form-group">
						<label>Metodo Pago</label>
	                    <select name="metodo" class="form-control selectpicker" id="metodo" data-live-search="true">
	                        	<option value="Efectivo">Efectivo</option>
	                        	<option value="AMEX">AMEX</option>
	                    </select>
					</div>
				</div>
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
					<div class="form-group">
						<label for="importe">Importe</label>
						<input type="number" name="importe" id="importe" class="form-control" placeholder="Importe" step=".01" required>
					</div>
				</div>
				<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12" id ="contentmoneda" >
					<div class="form-group">
						<label>Moneda</label>
						<select name="id_moneda" id= "moneda" class="form-control selectpicker"onchange="javascript:showContent2()">
							@foreach($monedas as $mon)
								<option value="{{$mon->id_moneda}}">{{$mon->moneda}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div id="contentchecbox" class="form-check col-lg-2 col-sm-2 col-md-2 col-xs-12" style="margin-left: 30px; display: none">
					<label></label>
			    	<input class="form-check-input" type="checkbox" name="check2" id="check2" value="1" onchange="javascript:showContent3()" />
			    	<label class="form-check-label" for="exampleCheck2">Capturar Importe en pesos</label>
			 	</div>	
				 <div  id="contentpdf" class="col-lg-3 col-sm-3 col-md-3 col-xs-12" style="display: none;">
		  			<div class="form-group">
		  				<label for="pdfimporte">PDF del Tipo de Cambio </label>
		  				<input type="file" name="pdfimporte" id="pdfimporte" class="form-control" accept=".pdf">
		  			</div>
		  		</div>
				<div id="contentimporte" class="col-lg-2 col-md-2 col-sm-2 col-xs-12"style="display: none;" >
					<div class="form-group">
						<label for="importemxn">Importe en pesos</label>
						<input type="number" name="importemxn" id="importemxn" class="form-control" placeholder="Importe en pesos" step=".01" required>
					</div>
				</div>	
				<div class="col-lg-8 col-sm-8 col-md-8 col-xs-12">
		  			<div class="form-group">
		  				<label for="pdf">PDF</label>
		  				<input type="file" name="pdf" id="pdf" class="form-control" accept=".pdf">
		  			</div>
		  		</div>
				
		  		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="form-group">
						<label for="comentarios">Comentarios</label>
						<input type="text" name="comentarios" id="comentarios" class="form-control" placeholder="Comentarios" >
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
					<table id="nacional" class="table table-striped table-bordered table-condensed table-hover" style="display: none;">
						<thead style="background-color:#A9D0F5">
							<th>Opciones</th>
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
							<th>DIFERENCIA</th>			
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th><h4>$ {{number_format(abs($folio->anticipo - $folio->all_total),2)}}</h4></th>
							<th></th>
							<th></th>
							<th></th>
						</tfoot>
						<tbody>							
								@foreach($detalles as $detalle)
									<tr>				
										<td><a href="" data-target='#modal-delete-{{$detalle->id_detalle_folio}}' data-toggle="modal" class="btn btn-danger fa fa-eraser"></a>
										</td>
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
									@include('travel.gasto.modal')
								@endforeach
						</tbody>
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
							<th><h4><strong>$ {{number_format($folio->all_total,2)}}</strong></h4></th>
							<th></th>
							<th></th>
							<th></th>
						</tr>
					</table>
					<table id="internacional" class="table table-striped table-bordered table-condensed table-hover"style="display: none;">
						<thead style="background-color:#A9D0F5">
							<th>Opciones</th>
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
						<tfoot>
							<th>DIFERENCIA</th>			
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							@if($folio->tipo =="Nacional" ||$folio->fecha_llegada <="2019-10-03 00:00:00" || $folio->moneda == "Pesos"|| $folio->anticipo == "0.00")
							<th><h4>$ {{number_format(abs($folio->anticipo - $folio->all_total),2)}}</h4></th>
							@else
							<th><h4>$ {{number_format(abs($folioi->montopesos - $folioi->all_total),2)}}</h4></th>
							@endif
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</tfoot>
						<tbody>				
								@foreach($detallesint as $detalle)
									<tr>				
										<td><a href="" data-target='#modal-delete-{{$detalle->id_detalle_folio}}' data-toggle="modal" class="btn btn-danger fa fa-eraser"></a>
										</td>
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
									@include('travel.gasto.modal')
								@endforeach
						</tbody>
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
							<th></th>
							<th><h4><strong>$ {{number_format($folio->all_total,2)}}</strong></h4></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

@push ('scripts')


<script>


	$(document).ready(function(){
		$USD = '';
		$JPN = '';
		$USD2 = '';
		$JPN2 = '';
		$('html,body').animate({
        scrollTop: $("#folioDetalle").offset().top},
        'slow');
        NotRequiredContent2();
		NotRequiredMoneda();
		NotRequiredContentevidencia();
		$comentarioevidencia = "{{$folio->evidencia_viaje}}";
		labelevidencia = document.getElementById("exampleCheck3");
		labelevidencia2 = document.getElementById("exampleCheck4");
		$Tipo = "{{$folio->tipo}}";
		$fechall = "{{$folio->fecha_llegada}}";
		tablan = document.getElementById("nacional");
		tablai = document.getElementById("internacional");
		if ($Tipo == "Internacional" && $fechall >= '2019-10-05 00:00:00'){
			tablai.style.display='block';

			tablan.style.display='none';
		}
		else{
			tablai.style.display='none';

			tablan.style.display='block';
		}
	});
	
	
	//INICIO DE FUNCIONES DE REQUERIMIENTOS FORMULARIOS//
	function NotRequiredContent2(){
		
		document.getElementById("fecha_factura").required = false;
		document.getElementById("noFactura").required = false;
		document.getElementById("importe").required = false;
	}

	function RequiredContent2(){
		
		document.getElementById("fecha_factura").required = true;
		document.getElementById("noFactura").required = true;
		document.getElementById("importe").required = true;
	}

	function NotRequiredContent1(){
		document.getElementById("xml").required = false;
		document.getElementById("xpdf").required = false;
	}

	function RequiredContent1(){
		document.getElementById("xml").required = true;
		document.getElementById("xpdf").required = true;
	}

	function NotRequiredContentevidencia(){
		document.getElementById("comentarioevidencia").required = false;
		// document.getElementById("pdfevidencia").required = false;
	}

	function RequiredContentevidencia(){
		document.getElementById("comentarioevidencia").required = true;
		// document.getElementById("pdfevidencia").required = true;
	}
	
	function NotRequiredMoneda(){
		document.getElementById("importemxn").required = false;
		document.getElementById("pdfimporte").required = false;
	}

	function NotRequiredImportemxn(){
		document.getElementById("importemxn").required = false;
		document.getElementById("pdfimporte").required = false;
	}
	
	function RequiredImportemxn(){
		document.getElementById("importemxn").required = true;
		document.getElementById("pdfimporte").required = false;
	}

	function NotRequiredPdf(){
		document.getElementById("pdfimporte").required = false;
	}

	function RequiredPdf(){
		document.getElementById("importemxn").required = true;
		document.getElementById("pdfimporte").required = true;
	}
	//FIN DE FUNCIONES DE REQUERIMIENTOS FORMULARIOS//
	function showContentev() {
	
		checkev = document.getElementById("checkev");
		elementev = document.getElementById("contentevidencia");
		checksolo = document.getElementById("checksolo");

		if(checkev.checked){
			elementev.style.display='block';
			RequiredContentevidencia();
		}
		else{
			elementev.style.display='none';
			NotRequiredContentevidencia();
		}
		
	}
	function showContentsolo() {
		checkev = document.getElementById("checkev");
		elementev = document.getElementById("contentevidencia");
		checksolo = document.getElementById("checksolo");

		if(checksolo.checked){
			NotRequiredContent1();
			NotRequiredContent2();
		}
		else{
			RequiredContent1();
			RequiredContent2();
		}
	}
	function showContent() {
		$Tipo = "{{$folio->tipo}}";
		$fecha = "{{$folio->fecha_llegada}}";
	 	element1 = document.getElementById("content1");
        element2 = document.getElementById("content2");
        check = document.getElementById("check");
		elementmoneda = document.getElementById("contentmoneda");
		checkev = document.getElementById("checkev");
		elementev = document.getElementById("contentevidencia");
		checksolo = document.getElementById("checksolo");

		if(checkev.checked){
			elementev.style.display='block';
			RequiredContentevidencia();
		}
		else{
			elementev.style.display='none';
			NotRequiredContentevidencia();
		}
        if (check.checked) {
            element2.style.display='block';
            RequiredContent2();
            element1.style.display='none';
            NotRequiredContent1();
			if ($Tipo == "Internacional" && $fecha >= '2019-10-05 00:00:00'){
				elementmoneda.style.display='block';
			}
			else{
				elementmoneda.style.display='none';
				NotRequiredMoneda();
			}        }
        else {
            element2.style.display='none';
            NotRequiredContent2();
            element1.style.display='block';
            RequiredContent1();
			NotRequiredMoneda();
        }
    }

	//FUNCION EN EL CUAL AL AGREGAR LA FECHA, TOMA EL DOLLAR/JEN PARA MULTIPLICARLO CON EL DINERO//
	$('#fecha_factura').change(function(){
		function getData(){
			return $.ajax({
				async: false,
				type: "POST",
				url: "/tipocambio",
				data: {
					'fechacambio' : $('input[name=fecha_factura]').val(),
					'_token': $('input[name=_token]').val()
				},
				dataType: "json"
			});        
    	}
		getData().done(function(result) {
		$USD  = result.Cambio.USD;
        $JPN = result.Cambio.JPN;
   		}).fail(function() {
    	});
		$importe = $('#importe').val();

		$USD2 = $USD * $importe;
		$JPN2 = $JPN * $importe;
		if(moneda == 2){
			$('#importemxn').attr('value', $USD2);
		}
		if(moneda == 3){
			$('#importemxn').attr('value', $JPN2);
		}
	});

	//FUNCION EN EL CUAL AL AGREGAR EL IMPORTE, MULTIPLICA EL DOLLAR/JEN POR PESOS//
	$('#importe').change(function(){
		function getData(){
			return $.ajax({
				async: false,
				type: "POST",
				url: "/tipocambio",
				data: {
					'fechacambio' : $('input[name=fecha_factura]').val(),
					'_token': $('input[name=_token]').val()
				},
				dataType: "json"
			});
				
		}
		getData().done(function(result) {
		$USD  = result.Cambio.USD;
        $JPN = result.Cambio.JPN;

    	}).fail(function() {
    	});
		$importe = $('#importe').val();

		$USD2 = $USD * $importe;
	
		
		$JPN2 = $JPN * $importe;

		if(moneda == 2){
			$('#importemxn').attr('value', $USD2);
		}
		if(moneda == 3){
			$('#importemxn').attr('value', $JPN2);
		}
		
	
	});
	
	function showContent2(){
		moneda = $('#moneda').val();
		contentcheck2 = document.getElementById("contentchecbox");
		elementimporte = document.getElementById("contentimporte");
		
		if(moneda == 2){
			contentcheck2.style.display='block';
			elementimporte.style.display = 'block';
			$('#importemxn').attr('readonly', true);
			$('#importemxn').attr('value', $USD2);
			RequiredImportemxn();
		}
		if(moneda == 3){
			contentcheck2.style.display='block';
			elementimporte.style.display = 'block';
			$('#importemxn').attr('readonly', true);
			$('#importemxn').attr('value', $JPN2);
			RequiredImportemxn();
		}
		if(moneda == 1){
			contentcheck2.style.display='none';
			elementimporte.style.display = 'none';
			$('#importemxn').attr('readonly', true);
			$('#importemxn').attr('value', $USD2);
			NotRequiredImportemxn();
		}
	}

	function showContent3() {
		elementimporte = document.getElementById("contentimporte");
		elementpdf = document.getElementById("contentpdf");
		if(check2.checked){
			elementpdf.style.display = 'block';
			elementimporte.style.display = 'block';
			$('#importemxn').attr('readonly', false);
			$USD2 = 0;
			$JPN2 = 0;
			$USD2 = $USD * $importe;
			$JPN2 = $JPN * $importe;

			if(moneda == 2){
				$('#importemxn').attr('value', $USD2);
			}
			if(moneda == 3){
				$('#importemxn').attr('value', $JPN2);
			}
			RequiredPdf();
		}
		else{
			elementpdf.style.display = "none";
			elementimporte.style.display = 'block';
			$('#importemxn').attr('readonly', true);
				$USD2 = 0;
				$JPN2 = 0;
				$USD2 = $USD * $importe;
				$JPN2 = $JPN * $importe;

			if(moneda == 2){
				$('#importemxn').val($USD2);
			}
			if(moneda == 3){
				$('#importemxn').val($JPN2);
			}
			NotRequiredPdf();
		}
	}
</script>
@endpush
@endsection