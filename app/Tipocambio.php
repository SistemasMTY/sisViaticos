<?php

namespace sisViaticos;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    //

    protected $table='ssm_viat_tipocambio';

    protected $primaryKey='fecha_cambio';

    public $timestamps=false;


    protected $fillable=[
        'USD',
        'JPN',


    ];

    protected $guarded = [

    ];
}
