@extends ('layout.admin')
@section ('contenido')

	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			<h3>Nueva Solicitud</h3>
			@if (count($errors) > 0)
			<div class="alert alert-danger">
				<ul>
					@foreach($errors->all() as $error)
						<li>{{$error}}</li>
					@endforeach
				</ul>
			</div>
			@endif
		</div>
	</div>

		{!!Form::open(array('url'=>'travel/solicitud','method'=>'POST','autocomplete'=>'off'))!!}
		{{Form::token()}}

	<div class="row">
		<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
	    		<div class="form-group">
	    			<label for="tipo">TIPO</label>
	    			<select class="form-control" name="tipo" required>
	    				<option value="Nacional">Nacional</option>
	    				<option value="Internacional">Internacional</option>
	    			</select>
	    		</div>
	    	</div>	    
	</div>

	<div class="row">
		<input type="hidden" name="id_solicitante" value="{{ Auth::user()->id }}"></input>
		<!--<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
	        <div class="form-group">
	         	<label for="cuenta">CUENTA-CLABE / BANK ACCOUNT</label>
	         	<input type="text" name="cuenta" value ="{{old('cuenta')}}"" class="form-control" placeholder="CUENTA-CLABE / BANK ACCOUNT...">
	        </div>
	    </div>-->
	    
	    	

	    <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
	        <div class="form-group">
	         	<label for="destino">DESTINO / DESTINATION</label>
	         	<input type="text" name="destino" required value ="{{old('destino')}}"" class="form-control" placeholder="DESTINO / DESTINATION...">
	        </div>
	    </div>
	    <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
	        <div class="form-group">
	         	<label for="proposito">PROPOSITO / PURPOSES</label>
	         	<input type="text" name="proposito" required value ="{{old('proposito')}}"" class="form-control" placeholder="PROPOSITO / PURPOSES...">
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
	         	<input type="date" name="fecha_salida" required value ="{{old('fecha_salida')}}"" class="form-control">
	        </div>
	    </div>
	    <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
	        <div class="form-group">
	         	<label for="fecha_llegada">A / TO</label>
	         	<input type="date" name="fecha_llegada"  required value ="{{old('fecha_llegada')}}"" class="form-control">
	        </div>
	    </div>
	    <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
	        <div class="form-group">
	         	<label for="dias">DIAS / DAYS</label>
	         	<input type="number" name="dias" required value ="{{old('dias')}}"" class="form-control">
	        </div>
	    </div>

	    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
	        <div class="form-group">
	         	<label for="criterio">DETALLES DEL VUELO/ FLIGHT DETAILS</label>
	         	<textarea name="criterio" value ="{{old('criterio')}}"" class="form-control" rows="2" maxlength="255"></textarea>
	        </div>
	    </div>

  		<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
        	<div class="form-group">
          		<label >ANTICIPO DE VIAJE / TRIP ADVANCE</label>
          			<select name="id_moneda" class="form-control">
              			@foreach($monedas as $mon)

  						<option value="{{$mon->id_moneda}}">{{$mon->moneda}}</option>

  						@endforeach
          			</select>
        	</div>
      	</div>
	    <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
	        <div class="form-group">
	        	<label for="anticipo">ANTICIPO / ADVANCE PAYMET</label>
	          	<input type="number" name="anticipo" required value ="{{old('anticipo')}}"" class="form-control" placeholder="ANTICIPO / ADVANCE PAYMET...">
	        </div>
	    </div>
	    
	</div>
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" id="guardar">
			<div class="form-group">
				<input type="hidden" name="_token" value="{{ csrf_token() }}"></input>
				<button class="btn btn-primary" type="submit">Guardar</button>
				<button class="btn btn-danger" type="reset">Cancelar</button>
			</div>
		</div>
	</div>

		{!!Form::close()!!}
@endsection