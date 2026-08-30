<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaybillItem extends Model
{
    //

    protected $fillable= [
            'waybill_id',
            'product_description',
            'batch_no',
            'no_of_cartons',
            'quantity_loaded',



    ];


    public function waybill():BelongsTo{

      return $this->belongsTo(Waybill::class);
    }
}
