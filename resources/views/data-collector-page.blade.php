@extends('layouts.app')

@section('title', __('Data Collector'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <data-collector-page :data_collection_id="{{ $data_collection_id }}"></data-collector-page>
            </div>
        </div>
    </div>
@endsection
