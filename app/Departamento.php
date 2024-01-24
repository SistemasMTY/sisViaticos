<?php

namespace sisViaticos;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    //
    protected $table='ssm_viat_depto';

    protected $primaryKey='id_depto';

    public $timestamps=false;


    protected $fillable=[
    	'departamento',
    	'descripcion',
    	'status',

    ];

    protected $guarded = [

    ];
}
