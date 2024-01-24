<div class="row">
	<div class="panel panel-primary">
		<div class="panel-body">
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12">
				<div class="form-group">
					<label for="fecha_gasto">Fecha</label>
					<input type="date" name="pfecha_gasto" id="pfecha_gasto" class="form-control">
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label for="gasto_tarjeta">Gastos con tarjeta</label>
					<input type="number" name="pgasto_tarjeta" id="pgasto_tarjeta" class="form-control" placeholder="Expenses with card">
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label for="gasto_efectivo">Gastos en efectivo</label>
					<input type="number" name="pgasto_efectivo" id="pgasto_efectivo" class="form-control" placeholder="Expenses in cash">
				</div>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
				<div class="form-group">
					<label for="viatico">Viatico</label>
					<input type="number" name="pviatico" id="pviatico" class="form-control" placeholder="Food quote">
				</div>
			</div>
			
			<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12">
				<div class="form-group">
					<button type="button" id="bt_add" class="btn btn-primary">Agregar</button>
				</div>
			</div>

			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="table-responsive">
					<table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
						<thead style="background-color:#A9D0F5">
							<th>Opciones</th>
							<th>Fecha</th>
							<th>Gastos con tarjeta</th>
							<th>Gastos en efectivo</th>
							<th>Viatico</th>
							<th>Subtotal</th>
						</thead>
						<tfoot>
							<th>TOTAL</th>			
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th><h4 id="total">S/. 0.00</h4><input type="hidden" name="total_venta" id="total_venta"></th>
						</tfoot>
						<tbody>
							
								@foreach($detalles as $detalle)
									<tr>				
										<td></td>
										<td>{{$detalle->fecha_gasto}}</td>
										<td>{{$detalle->gasto_tarjeta}}</td>
										<td>{{$detalle->gasto_efectivo}}</td>
										<td>{{$detalle->viatico}}</td>
										<td>subtotal</td>
									</tr>
								@endforeach
								
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" id="guardar">
		<div class="form-group">
			<input type="hidden" name="_token" value="{{ csrf_token() }}"></input>
			<button class="btn btn-primary" type="submit">Guardar</button>
			<button class="btn btn-danger" type="reset">Cancelar</button>
		</div>
	</div>
</div>