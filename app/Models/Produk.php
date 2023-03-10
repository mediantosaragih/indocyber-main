<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;
    public function keranjang_detail()
	{
	     return $this->hasMany('App\Models\KeranjangDetail','produk_id', 'id');
	}
}
