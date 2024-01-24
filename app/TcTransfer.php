<?php

namespace sisViaticos;

use Illuminate\Database\Eloquent\Model;

class TcTransfer extends Model
{
    //

    protected $table='ssm_viat_tc_cambio';

    protected $primaryKey='id_tc';

    public $timestamps=false;


    protected $fillable=[
        'company',
        'id_header_folio',
        'fecha_transfer',
        'monto',
        'id_moneda',
        'montopesos',

    ];

    protected $guarded = [

    ];
}
