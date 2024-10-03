@extends('layouts.app')

@section('title', __('Payments - Settings'))

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <data-collector-payments-configuration-page></data-collector-payments-configuration-page>
            </div>
        </div>
@endsection
