<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    use HasFactory;

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

	public function keranjang_detail()
	{
	     return $this->hasMany('App\Models\KeranjangDetail','pesanan_id', 'id');
	}
}
