<?php

namespace sisViaticos;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    //

    protected $table='ssm_viat_status';

    protected $primaryKey='	id_status';

    public $timestamps=false;


    protected $fillable=[
    	'status',
    	
    ];

    protected $guarded = [

    ];
}
