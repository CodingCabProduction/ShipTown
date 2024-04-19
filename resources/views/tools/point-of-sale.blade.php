@extends('layouts.app')

@section('title', __('ePOS'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <point-of-sale-page></point-of-sale-page>
            </div>
        </div>
    </div>
@endsection
