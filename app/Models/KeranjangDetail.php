<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeranjangDetail extends Model
{
    use HasFactory;
    public function produk()
	{
	      return $this->belongsTo('App\Models\Produk','produk_id', 'id');
	}

	public function keranjang()
	{
	      return $this->belongsTo('App\Keranjang','keranjang_id', 'id');
	}
}
