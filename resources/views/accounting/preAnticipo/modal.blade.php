<div class="modal fade modal-slide-in-right" aria-hidden="true"
role="dialog" tabindex="-1" id="modal-delete-{{$fol->folio}}">
	{{Form::Open(array('action'=>array('PreAnticipoController@destroy',$fol->folio),'method'=>'delete'))}}
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" 
				aria-label="Close">
                     <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Autorizacion de anticipo extraordinario Folio No. {{$fol->folio}}</h4>
			</div>
			<div class="modal-body">
				<p>¿Desea confirmar un anticipo extraordinario para este folio?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="submit" class="btn btn-primary">Confirmar</button>
			</div>
		</div>
	</div>
	{{Form::Close()}}

</div>