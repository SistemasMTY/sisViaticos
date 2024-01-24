<?php

namespace sisViaticos;

use Illuminate\Database\Eloquent\Model;

class FirmaGasto extends Model
{
    //
    protected $table='ssm_viat_firma_gasto';

    protected $primaryKey='id_gasto';

    public $timestamps=false;


    protected $fillable=[
        'company',
        'id_user',
    	'id_header_folio',
    	'id_autorizador',
    	'status',
    	'gasto',
    	
    ];

    protected $guarded = [

    ];
}
