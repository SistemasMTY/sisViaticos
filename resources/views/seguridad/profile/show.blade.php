@extends ('layout.admin')
@section ('contenido')
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
			
		<h3>Datos: {{ Auth::user()->name }}</h3>
						
			
			
		@foreach($profiles as $profile)
            <div class="form-group row">
	          <label for="NumNomina" class="col-md-4 col-form-label text-md-right">NUMERO NOMINA</label>

	          <div class="col-md-6">
	              <input type="text" class="form-control" name="NumNomina" value="{{ $profile->TrabajadorID }}"  readonly>
	          </div>
	      	</div>
	      	
	      	<div class="form-group row">
	          <label for="rfc" class="col-md-4 col-form-label text-md-right">R.F.C.</label>

	          <div class="col-md-6">
	              <input type="text" class="form-control" name="rfc" value="{{ $profile->RFC }}" readonly>
	          </div>
	      	</div>

	      	<div class="form-group row">
	          <label for="curp" class="col-md-4 col-form-label text-md-right">C.U.R.P.</label>

	          <div class="col-md-6">
	              <input type="text" class="form-control" name="curp" value="{{ $profile->CURP }}" readonly>
	          </div>
	      	</div>

	      	<div class="form-group row">
	          <label for="Ingreso" class="col-md-4 col-form-label text-md-right">FECHA DE INGRESO</label>

	          <div class="col-md-6">
	              <input type="date" class="form-control" name="Ingreso" value="{{ $profile->fechaingreso }}" readonly>
	          </div>
	      	</div>

	      	<div class="form-group row">
	          <label for="puesto" class="col-md-4 col-form-label text-md-right">PUESTO</label>

	          <div class="col-md-6">
	              <input type="text" class="form-control" name="puesto" value="{{ $profile->PuestoDescripcion }}" readonly>
	          </div>
	      	</div>

	      	<div class="form-group row">
	          <label for="banco" class="col-md-4 col-form-label text-md-right">BANCO</label>

	          <div class="col-md-6">
	              <input type="text" class="form-control" name="banco" value="{{ $profile->Banco }}" readonly>
	          </div>
	      	</div>

	      	<div class="form-group row">
	          <label for="cuenta" class="col-md-4 col-form-label text-md-right">CUENTA</label>

	          <div class="col-md-6">
	              <input type="text" class="form-control" name="cuenta" value="{{ $profile->BancoCuenta }}" readonly>
	          </div>
	      	</div>

	      	<div class="form-group row">
	          <label for="clabe" class="col-md-4 col-form-label text-md-right">CLABE</label>

	          <div class="col-md-6">
	              <input type="text" class="form-control" name="clabe" value="{{ $profile->CLABE }}" readonly>
	          </div>
	      	</div>

	      	<div class="form-group row">
	          <label for="departamento" class="col-md-4 col-form-label text-md-right">DEPARTAMENTO</label>

	          <div class="col-md-6">
	              <input type="text" class="form-control" name="departamento" value="{{ $profile->depto }}" readonly>
	          </div>
	      	</div>
			  @endforeach
			@foreach($autorizadores as $auto)
			<div class="form-group row">
	          	<label for="auto1" class="col-md-4 col-form-label text-md-right">REVISA</label>
	    	    <div class="col-md-6">
						<input type="text" class="form-control" name="auto1" value="{{ $auto->a1 }}" readonly>
	      		</div>
	      	</div>
			<div class="form-group row">
	         	<label for="auto2" class="col-md-4 col-form-label text-md-right">AUTORIZA</label>
	    	    <div class="col-md-6">
					<input type="text" class="form-control" name="auto2" value="{{ $auto->a2 }}" readonly>
	      		</div>
	      	</div>
			  
			@endforeach



	      	<div class="form-group row">
	          <label for="BRH" class="col-md-4 col-form-label text-md-right">BRH</label>

	          <div class="col-md-6">
	              <input type="text" class="form-control" name="BRH" value="{{ $profile->compania }}" readonly>
	          </div>
	      	</div>

            <div class="form-group">
            	<a href="{{url('travel/solicitud')}}"><button class="btn btn-danger">Atras</button></a>
            </div>

            
		</div>
	</div>
@endsection