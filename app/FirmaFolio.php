<?php

namespace sisViaticos;

use Illuminate\Database\Eloquent\Model;

class FirmaFolio extends Model
{
    //

    protected $table='ssm_viat_firma_folio';

    protected $primaryKey='id_firma_folio';

    public $timestamps=false;


    protected $fillable=[
        'company',
        'id_user',
    	'id_header_folio',
    	'id_autorizador',
    	'anticipo',
    	'gasto',
    	
    ];

    protected $guarded = [

    ];

}
