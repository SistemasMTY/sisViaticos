<?php

namespace sisViaticos;

use Illuminate\Database\Eloquent\Model;

class Moneda extends Model
{
    //

    protected $table='ssm_viat_moned';

    protected $primaryKey='id_moneda';

    public $timestamps=false;


    protected $fillable=[
    	'moneda',
    	'simbolo',
    	
    ];

    protected $guarded = [

    ];
}
