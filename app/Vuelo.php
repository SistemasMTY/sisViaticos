<?php

namespace sisViaticos;

use Illuminate\Database\Eloquent\Model;

class Vuelo extends Model
{
    //
    protected $table='ssm_viat_flights';

    protected $primaryKey='id_flight';

    public $timestamps=false;


    protected $fillable=[        
    	'id_header_folio',
    	'fecha_compra',
    	'proveedor',
        'RFC',
    	'no_Factura',
        'importe',
        'IVA',
        'otro_impuesto',
        'total_vuelo',
        'xml',
        'pdf',
        'usuario_subio',
        'motivo_descartar',
        'fecha_insert',
        'branch',
        'UUID',
        'id_gasto',
        'id_cuenta', 
        'tarjetaPago', 
        'id_moneda', 
        'tipo_cambio'
    ];

    protected $guarded = [

    ];

}
