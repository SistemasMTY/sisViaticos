<?php

namespace sisViaticos;

use Illuminate\Database\Eloquent\Model;

class Folio extends Model
{
    //
    protected $table='ssm_viat_header_folio';

    protected $primaryKey='id_header_folio';

    public $timestamps=false;


    protected $fillable=[
        'company',
    	'fecha',
		'id_solicitante',
		'correo_solicitante',
    	'tipo',
    	'id_status',
    	'destino',
		'proposito',
		'eq_computo',
    	'fecha_salida',
    	'fecha_llegada',
    	'dias',
        'criterio',
    	'id_moneda',
    	'anticipo',
    	'all_subtotal',
    	'all_iva',
    	'all_otros_imp',
		'all_total',
		'correo_auto1',
		'correo_auto2',
		'evidencia_viaje',
		'pdfevidencia',
    	'_token',

    ];

    protected $guarded = [

    ];
}
