@extends('layouts.app')

@section('title', __('Quantity Discounts - Edit'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <quantity-discounts-edit-page initial-discount='{{ json_encode($discount) }}'
                                              initial-products='{{ json_encode($products) }}'></quantity-discounts-edit-page>
            </div>
        </div>
@endsection
