{!! Form::open(array('url'=>'accounting/validados','method'=>'GET','autocomplete'=>'off','role'=>'search'))!!}

<div class="row">
	<div class="col-lg-5 col-sm-5 col-md-5 col-xs-12">
		<div class="form-group">
				<label>NOMBRE o No. DE FOLIO</label>
				<input type="text" class="form-control" name="searchText" placeholder="Buscar..." value="{{$searchText}}">
		</div>
	</div>
	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
		<div class="form-group">
			
				<label>FECHA INICIAL</label>
				<input type="date" name="fechaI" class="form-control" value="{{$FechaI}}">
			
		</div>
	</div>
	<div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
		<div class="form-group">
			
				<label>FECHA FINAL</label>
				<input type="date" name="fechaF" class="form-control" value="{{$FechaF}}">
			
		</div>
	</div>

	<div class="col-lg-1 col-sm-1 col-md-1 col-xs-12">
		<div class="form-group">
				<label></label>
				<span>
					<button type="submit" class="btn btn-primary">Buscar</button>
				</span>
		</div>
	</div>

	<div class="col-lg-1 col-sm-1 col-md-1 col-xs-12">
		<div class="form-group">
				<label></label>
				<span>
					<a href="{{ url('accounting/validados')}}" class="btn btn-danger">Reiniciar</a>
				</span>
		</div>
	</div>
</div>
{{Form::close()}}