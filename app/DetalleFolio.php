<?php

namespace sisViaticos;

use Illuminate\Database\Eloquent\Model;

class DetalleFolio extends Model
{
    //
    protected $table='ssm_viat_detalle_folio';

    protected $primaryKey='id_detalle_folio';

    public $timestamps=false;


    protected $fillable=[
        'company',
    	'id_header_folio',
    	'fecha_factura',
    	'proveedor',
        'RFC',
    	'noFactura',
    	'id_gasto',
    	'id_cuenta',
        'metodoPago',
        'importe',
        'IVA',
        'otro_impuesto',
        'xml',
        'pdf',
        'comentarios',
        'UUID',
        'importeint',
        'PDFint',
        'IVAint',
    ];

    protected $guarded = [

    ];

}
