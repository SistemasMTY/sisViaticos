<?php

namespace sisViaticos;

use Illuminate\Database\Eloquent\Model;

class FirmaAnticipo extends Model
{
    //
    protected $table='ssm_viat_firma_anticipo';

    protected $primaryKey='id_anticipo';

    public $timestamps=false;


    protected $fillable=[
        'company',
        'id_user',
    	'id_header_folio',
    	'id_autorizador',
    	'status',
    	'anticipo',
    	
    ];

    protected $guarded = [

    ];
}
