@extends('layouts.body')

@section('title', __('Quick Connect'))

@section('app-content')
    <div class="m-5">
        <a href="quick-connect/magento" class="btn btn-primary btn-block">Magento</a>
        <a href="quick-connect/shopify" class="btn btn-primary btn-block">Shopify</a>
        <br><br>
        <a href="/products" class="btn btn-primary btn-block">Skip</a>
    </div>
@endsection
