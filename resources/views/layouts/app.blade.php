@extends('layouts.body')

@section('app-content')
    @auth
        <heartbeats></heartbeats>
    @endauth

    @if((request()->input('hide_nav_bar', false) === false))
        @include('layouts.nav')
    @endif

    @if (session('alert-info-message'))
        <div class="alert alert-info" role="alert">{{ session('alert-info-message') }}</div>
    @endif

    @if (session('alert-success-message'))
        <div class="alert alert-success" role="alert">{{ session('alert-success-message') }}</div>
    @endif

    @if (session('alert-warning-message'))
        <div class="alert alert-warning" role="alert">{{ session('alert-warning-message') }}</div>
    @endif

    @if (session('alert-danger-message'))
        <div class="alert alert-danger" role="alert">{{ session('alert-danger-message') }}</div>
    @endif

    <main class="py-0 pl-1 pr-2">
        @yield('content')
    </main>

    <vue-snotify></vue-snotify>

    <recent-inventory-movements-modal></recent-inventory-movements-modal>
    <product-details-modal></product-details-modal>
    <new-product-modal></new-product-modal>
    <find-product-modal></find-product-modal>
@endsection
