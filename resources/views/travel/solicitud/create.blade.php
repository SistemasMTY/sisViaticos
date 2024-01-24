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
		@foreach($autorizadores as $auto)
		<input type="hidden" name="auto_1" value="{{$auto->a1}}"></input>
		<input type="hidden" name="auto_2" value="{{$auto->a2}}"></input>
		@endforeach

	    <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
	        <div class="form-group">
	         	<label for="destinoviaje">DESTINO / DESTINATION</label>
				 <select name="destinoviaje" class="form-control" id="destinoviaje" data-live-search="true" onchange="javascript:showContent()" >
	                <option value="SSMQUERETARO">SSM QUERETARO</option>
					<option value="SSMMONTERREY">SSM MONTERREY</option>
					<option value="SSMSALAMANCA">SSM SALAMANCA</option>
					<option value="OTRO">Otro Destino</option>
	            </select>				
	        </div>
	    </div>
		<div id="otrodestino" class="col-lg-6 col-sm-6 col-md-6 col-xs-12" style="display: none;">
	        <div class="form-group">
				<label for="destino">OTRO DESTINO / OTHER DESTINATION</label>				
				<input type="text" name="destino" id="destino"value = "SSM QUERETARO" required class="form-control" placeholder="DESTINO / DESTINATION..." >
	        </div>
	    </div>
	    <div class="col-lg-8 col-sm-8 col-md-8 col-xs-12">
	        <div class="form-group">
	         	<label for="proposito">PROPOSITO / PURPOSES</label>
	         	<input type="text" name="proposito" required value ="{{old('proposito')}}" class="form-control" placeholder="PROPOSITO / PURPOSES...">
	        </div>
	    </div>
		<div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
			<div class="form-group">
    			<label for="eq_computo">EQUIPO DE COMPUTO / COMPUTER EQUIPMENT</label>
	    		<select class="form-control" name="eq_computo" required>
	    			<option value="Si">Si</option>
	    			<option value="No">No</option>
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
	         	<input type="date" name="fecha_salida" required value ="{{old('fecha_salida')}}"" class="form-control" min="{{date('Y-m-d')}}">
	        </div>
	    </div>
	    <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
	        <div class="form-group">
	         	<label for="fecha_llegada">A / TO</label>
	         	<input type="date" name="fecha_llegada"  required value ="{{old('fecha_llegada')}}"" class="form-control" min="{{date('Y-m-d')}}">
	        </div>
	    </div>

	    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
	        <div class="form-group">
	         	<label for="criterio">DETALLES DEL VUELO/ FLIGHT DETAILS</label>
	         	<textarea name="criterio" value ="{{old('criterio')}}" class="form-control" rows="2" maxlength="255" placeholder="EN CASO DE REQUERIR VUELO, ANOTAR LOS DETALLES, DE LO CONTRARIO ESCRIBIR NA"></textarea>
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
	        	<label for="anticipo">ANTICIPO / ADVANCE PAYMENT</label>
				<input type="number" type="number" step="0.01" name="anticipo" required value ="{{old('anticipo')}}"" class="form-control" placeholder="ANTICIPO / ADVANCE PAYMENT...">
	        </div>
	    </div>
	    
	</div>
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" id="guardar">
			<div class="form-group">
				<input type="hidden" name="_token" value="{{ csrf_token() }}"></input>
				<button class="btn btn-primary" type="submit">Guardar</button>
				<a href="{{ url('travel/solicitud')}}" class="btn btn-danger">Atras</a>
			</div>
		</div>
	</div>

		{!!Form::close()!!}

@endsection
<script>
	function showContent() {
		destinoviaje = $('#destinoviaje').val();
		otrodestino = document.getElementById("otrodestino");
		
		if(destinoviaje === "OTRO"){
			otrodestino.style.display='block';
			document.getElementById("destino").required = true;
			document.getElementById("destino").value = "{{old('destino')}}";		
		}
		else{
			otrodestino.style.display='none';
			document.getElementById("destino").required = false;
			if(destinoviaje === "SSMQUERETARO"){
				document.getElementById("destino").value = 'SSM QUERETARO';
			}
			else if(destinoviaje === "SSMMONTERREY"){
				document.getElementById("destino").value = 'SSM MONTERREY';
			}
			else{
				document.getElementById("destino").value = 'SSM SALAMANCA';
			}
		}
    }
</script>