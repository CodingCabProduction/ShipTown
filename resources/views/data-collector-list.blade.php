@extends('layouts.app')

@section('title', __('Data Collector List'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <data-collector-list-page></data-collector-list-page>
            </div>
        </div>
    </div>
@endsection
