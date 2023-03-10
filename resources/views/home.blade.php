@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mb-5">
        <center><h1> Selamat Datang Di Website Marketplace</h1></center>
        </div>

        @foreach($produks as $produk)
        <div class="col-md-4">
            <div class="card">
              <img src="{{ url('uploads') }}/{{ $produk->image }}" class="card-img-top" style="wodth:200px; height:300px" alt="...">
              <div class="card-body">
                <h5 class="card-title">{{ $produk->nama_produk }}</h5>
                <p class="card-text">
                    <strong>Harga :</strong> Rp. {{ number_format($produk->harga)}} <br>
                    <strong>Stok :</strong> {{ $produk->stock }} <br>
                    <hr>
                    <strong>Keterangan :</strong> <br>
                    {{ $produk->keterangan }}
                </p>
                <a href="{{ url('pesan') }}/{{ $produk->id }}" class="btn btn-primary"><i class="fa fa-shopping-cart"></i> Pesan</a>
              </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
