@extends ('layout.admin')
@section ('contenido')
	<div class="row">
		<div class="col-lg-6 col-sm-6 col-md-6 col-xs-6">
  			<div class="form-group">
  				<label for="fecha">Fecha:</label>
  				<p>{{ date('d-m-Y', strtotime($folio->fecha)) }}</p>
  			</div>
  		</div>
  		<div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
        	<div class="form-group">
          		<label for="tipo">TIPO / TYPE </label>
          			<p>{{$folio->tipo}}</p>
        	</div>
      	</div>
  		<div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
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
	         	<label for="fecha_salida">A / TO</label>
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
	    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" >
	    	<div class="form-group">
            	<a href="{{ route('reviewfolio', ['id' => $folio->id_header_folio]) }}"><button class="btn btn-primary" type="submit">Enviar</button></a>
            	<a href="{{url('travel/solicitud')}}"><button class="btn btn-danger">Cancelar</button></a>
        	</div>
        	
        </div>
	</div>
@endsection