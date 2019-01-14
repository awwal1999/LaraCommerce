<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    protected $fillable = ['quantity', 'buyer_id', 'product_id'];

    public function buyer()
    {
        return $this->belongsTo('App\Buyer');
    }

    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}
