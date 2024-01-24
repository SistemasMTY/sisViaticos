<?php

namespace sisViaticos;

use Illuminate\Database\Eloquent\Model;

class Banco extends Model
{
    //
    protected $table='ssm_viat_banco';

    protected $primaryKey='id_banco';

    public $timestamps=false;


    protected $fillable=[
    	'banco',
    	'descripcion',
    	'status',

    ];

    protected $guarded = [

    ];
}
