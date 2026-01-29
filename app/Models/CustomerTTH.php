<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerTTH extends Model
{
    protected $table = 'customertth';
    protected $primaryKey = 'ID'; 
    public $timestamps = false;
    protected $guarded = []; 

    // Relasi ke Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'CustID', 'CustID');
    }

    // Relasi ke Detail
    public function details()
    {
        return $this->hasMany(CustomerTTHDetail::class, 'TTOTTPNo', 'TTOTTPNo');
    }
}