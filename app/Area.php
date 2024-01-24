<?php

namespace sisViaticos;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    //

    protected $table='ssm_viat_area';

    protected $primaryKey='id_area';

    public $timestamps=false;


    protected $fillable=[
    	'area',
    	'descripcion',
    	'status',

    ];

    protected $guarded = [

    ];
}
