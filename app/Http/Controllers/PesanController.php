<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Keranjang;
use App\Models\User;
use App\Models\KeranjangDetail;
use Auth;
use Alert;
use Carbon\Carbon;
class PesanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($id)
    {
    	$produk = Produk::where('id', $id)->first();

    	return view('pesan.index', compact('produk'));
    }

    public function pesan(Request $request, $id)
    {
    	$produk = Produk::where('id', $id)->first();
    	$tanggal = Carbon::now();

    	// validasi apakah melebihi stok
    	if($request->jumlah_pesan > $produk->stock)
    	{
    		return redirect('pesan/'.$id);
    	}

    	//cek validasi
    	$cek_keranjang = Keranjang::where('id_user', Auth::user()->id)->where('status',0)->first();
    	// simpan ke database pesanan
    	if(empty($cek_keranjang))
    	{
    		$keranjang = new Keranjang;
	    	$keranjang->id_user = Auth::user()->id;
	    	$keranjang->tanggal = $tanggal;
	    	$keranjang->status = 0;
	    	$keranjang->qty = 1;
            $keranjang->kode = mt_rand(100, 999);
	    	$keranjang->save();
    	}


    	//simpan ke database pesanan detail
    	$keranjang_baru = Keranjang::where('id_user', Auth::user()->id)->where('status',0)->first();

    	//cek pesanan detail
    	$cek_keranjang_detail = KeranjangDetail::where('produk_id', $produk->id)->where('keranjang_id', $keranjang_baru->id)->first();
    	if(empty($cek_keranjang_detail))
    	{
    		$keranjang_detail = new KeranjangDetail;
	    	$keranjang_detail->produk_id = $produk->id;
	    	$keranjang_detail->keranjang_id = $keranjang_baru->id;
	    	$keranjang_detail->jumlah = $request->jumlah_pesan;
	    	$keranjang_detail->qty = $produk->harga*$request->jumlah_pesan;
	    	$keranjang_detail->save();
    	}else
    	{
    		$keranjang_detail = KeranjangDetail::where('produk_id', $produk->id)->where('keranjang_id', $keranjang_baru->id)->first();

    		$keranjang_detail->jumlah = $keranjang_detail->jumlah+$request->jumlah_pesan;

    		//harga sekarang
    		$harga_keranjang_detail_baru = $produk->harga*$request->jumlah_pesan;
	    	$keranjang_detail->qty = $keranjang_detail->qty+$harga_keranjang_detail_baru;
	    	$keranjang_detail->update();
    	}

    	//jumlah total
    	$keranjang = Keranjang::where('id_user', Auth::user()->id)->where('status',0)->first();
    	$keranjang->qty = $keranjang->qty+$produk->harga*$request->jumlah_pesan;
    	$keranjang->update();

        Alert::success('Succes Message ');
    	return redirect('check-out');

    }

    public function check_out()
    {
        $keranjang = Keranjang::where('id_user', Auth::user()->id)->where('status',0)->first();
 	    $keranjang_details = [];
        if(!empty($keranjang))
        {
            $keranjang_details = KeranjangDetail::where('keranjang_id', $keranjang->id)->get();

        }

        return view('pesan.check_out', compact('keranjang', 'keranjang_details'));
    }

    public function delete($id)
    {
        $keranjang_detail = KeranjangDetail::where('id', $id)->first();

        $keranjang = Keranjang::where('id', $keranjang_detail->keranjang_id)->first();
        $keranjang->qty = $keranjang->qty-$keranjang_detail->qty;
        $keranjang->update();


        $keranjang_detail->delete();

        Alert::error('Keranjang Sukses Dihapus', 'Hapus');
        return redirect('check-out');
    }

    public function konfirmasi()
    {
        $user = User::where('id', Auth::user()->id)->first();

        if(empty($user->alamat))
        {
            Alert::error('Identitasi Harap dilengkapi', 'Error');
            return redirect('profile');
        }

        if(empty($user->nohp))
        {
            Alert::error('Identitasi Harap dilengkapi', 'Error');
            return redirect('profile');
        }

        $keranjang = Keranjang::where('user_id', Auth::user()->id)->where('status',0)->first();
        $keranjang_id = $keranjang->id;
        $keranjang->status = 1;
        $keranjang->update();

        $keranjang_details = KeranjangDetail::where('keranjang_id', $keranjang_id)->get();
        foreach ($keranjang_details as $keranjang_detail) {
            $barang = Barang::where('id', $keranjang_detail->barang_id)->first();
            $barang->stok = $barang->stok-$keranjang_detail->jumlah;
            $barang->update();
        }



        Alert::success('Keranjang Sukses Check Out Silahkan Lanjutkan Proses Pembayaran', 'Success');
        // return redirect('history/'.$keranjang_id);
        return redirect('check-out');

    }
}
