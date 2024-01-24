<?php

namespace sisViaticos;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    //

    protected $table='ssm_viat_transfer';

    protected $primaryKey='id_transfer';

    public $timestamps=false;


    protected $fillable=[
        'company',
        'id_header_folio',
    	'fecha',

    ];

    protected $guarded = [

    ];
}
