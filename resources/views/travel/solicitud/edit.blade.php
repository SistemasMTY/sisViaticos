@extends ('layout.admin')
@section ('contenido')
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<h3>Editar Solicitud: {{ $folio->id_header_folio}}</h3>
			@if (count($errors)>0)
			<div class="alert alert-danger">
				<ul>
				@foreach ($errors->all() as $error)
					<li>{{$error}}</li>
				@endforeach
				</ul>
			</div>
			@endif
		</div>
	</div>
	@if(session()->has('msg'))
    	<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
				<div class="alert alert-danger">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true" >&times;</button>
	  					<strong>{{ session()->get('msg') }}</strong>
				</div>
			</div>
		</div>	
	@endif

			{!!Form::model($folio,['method'=>'PATCH','route'=>['solicitud.update',$folio->id_header_folio]])!!}
            {{Form::token()}}
            	<div class="row">
					<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
			    		<div class="form-group">
			    			<label for="tipo">TIPO</label>
			    			<select class="form-control" name="tipo" required>
			    				@if($folio->tipo=="Nacional")
					    				<option value="Nacional" selected>Nacional</option>
					    				<option value="Internacional">Internacional</option>
				    			@else
					    				<option value="Nacional">Nacional</option>
					    				<option value="Internacional" selected>Internacional</option>
				    			@endif
			    			</select>
			    		</div>
			    	</div>	    
				</div>

				<div class="row">
				    <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
				        <div class="form-group">
				         	<label for="destino">DESTINO / DESTINATION</label>
				         	<input type="text" name="destino" required value ="{{$folio->destino}}" class="form-control" placeholder="DESTINO / DESTINATION...">
				        </div>
				    </div>
				    <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
				        <div class="form-group">
				         	<label for="proposito">PROPOSITO / PURPOSES</label>
				         	<input type="text" name="proposito" required value ="{{$folio->proposito}}" class="form-control" placeholder="PROPOSITO / PURPOSES...">
				        </div>
				    </div>
					<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
	    				<div class="form-group">
	    					<label for="eq_computo">EQUIPO DE COMPUTO / COMPUTER EQUIPMENT</label>
	    					<select class="form-control" name="eq_computo" required>
								@if($folio->eq_computo=="Si")
					    				<option value="Si" selected>Si</option>
					    				<option value="No">No</option>
				    			@else
					    				<option value="Si">Si</option>
					    				<option value="No" selected>No</option>
				    			@endif
	    					</select>
	    				</div>
	    			</div>	    
				     <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
				        <div class="form-group">
				         	<label for="periodo">PERIODO / PERIOD</label>
				        </div>
				    </div>
				    <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
				        <div class="form-group">
				         	<label for="fecha_salida">DE / FROM</label>
				         	<input type="date" name="fecha_salida" value="{{date('Y-m-d', strtotime($folio->fecha_salida))}}" class="form-control"  min="{{date('Y-m-d')}}">
				        </div>
				    </div>
				    <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
				        <div class="form-group">
				         	<label for="fecha_llegada">A / TO</label>
				         	<input type="date" name="fecha_llegada"  required value ="{{ date('Y-m-d', strtotime($folio->fecha_llegada)) }}" class="form-control"  min="{{date('Y-m-d')}}">
				        </div>
				    </div>
				    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
				        <div class="form-group">
				         	<label for="criterio">DETALLES DEL VUELO/ FLIGHT DETAILS</label>
				         	<textarea name="criterio" class="form-control" rows="2" maxlength="255">{{$folio->criterio}}</textarea>
				        </div>
				    </div>

			  		<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
			        	<div class="form-group">
			          		<label >ANTICIPO DE VIAJE / TRIP ADVANCE</label>
			          			<select name="id_moneda" class="form-control">
			              			
			              			@foreach($monedas as $mon)
			              				@if($mon->id_moneda==$folio->id_moneda)
			  								<option value="{{$mon->id_moneda}}" selected>{{$mon->moneda}}</option>
			  							@else
			  								<option value="{{$mon->id_moneda}}" >{{$mon->moneda}}</option>
			  							@endif
			  						@endforeach
			          			</select>
			        	</div>
			      	</div>
				    <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
				        <div class="form-group">
				        	<label for="anticipo">ANTICIPO / ADVANCE PAYMENT</label>
				          	<input type="number" name="anticipo" required value="{{$folio->anticipo}}" class="form-control" placeholder="ANTICIPO / ADVANCE PAYMENT...">
				        </div>
				    </div>
				    
				</div>
			  	<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
				  	<div class="form-group">
				  			<button class="btn btn-primary" type="submit">Guardar</button>
				  			<a href="{{ url('travel/solicitud')}}" class="btn btn-danger">Cancelar</a>
				  	</div>
				 </div>

			{!!Form::close()!!}		
            
		
@endsection