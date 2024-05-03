@extends('layouts.app')

@section('title',__('Inventory Dashboard'))

@section('content')
    <div class="container dashboard-widgets">
    <div class="row">
        <div class="col">
            <div class="widget-tools-container">
                <a class="btn btn-primary btn-sm mt-2 fa-arrow-alt-circle-down"  href="{{ request()->fullUrlWithQuery(['filename' =>  __($report_name).'.csv']) }}">{{ __('Download') }}</a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center">{{ __($meta['report_name']) }}</h4>
                    <table class="table-hover w-100">
                        <thead>
                        <tr>
                            @foreach($fields as $field)
                                <th>{{ __($field) }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $record)
                            <tr class="table-hover">
                                    <td>
{{--                                        <a href="/reports/restocking?sort=quantity_available&filter[warehouse_code]=TRA&filter[quantity_available_between]=-9999,-0.01&filter[inventory_source_warehouse_code]=99&filter[warehouse_quantity_between]=1,99999&select=warehouse_code,product_sku,product_name,quantity_required,quantity_available,quantity_incoming,reorder_point,restock_level,warehouse_quantity&per_page=999">--}}
                                            {{ data_get($record, 'warehouse_code') }}
{{--                                        </a>--}}
                                    </td>
                                    <td>
                                        @if(data_get($record, 'products_on_minus') === 0)
                                            -
                                        @endif

                                        @if(data_get($record, 'products_on_minus') > 0)
                                            <a href='{{ url()->route('reports.restocking', [
                                                'select' => 'warehouse_code,product_sku,product_name,quantity_required,quantity_available,quantity_incoming,reorder_point,restock_level,warehouse_quantity',
                                                'sort' => 'quantity_available',
                                                'per_page' => '999',
                                                'filter[warehouse_code]' => data_get($record, 'warehouse_code'),
                                                'filter[quantity_available_between]' => '-9999,-0.01',
                                                'filter[inventory_source_warehouse_code]' => '99',
                                                'filter[warehouse_quantity_between]' => '1,99999',
                                            ]) }}'>
                                                {{ data_get($record, 'products_on_minus') }}
                                            </a>
                                        @endif
                                    </td>
                                    <td>
{{--                                        <a href="">--}}
                                            {{ data_get($record, 'wh_products_available') }}
{{--                                        </a>--}}
                                    </td>
                                    <td>
{{--                                        <a href="">--}}
                                            {{ data_get($record, 'wh_products_out_of_stock') }}
{{--                                        </a>--}}
                                    </td>
                                    <td>
{{--                                        <a href="">--}}
                                            {{ data_get($record, 'wh_products_required') }}
{{--                                        </a>--}}
                                    </td>
                                    <td>
{{--                                        <a href="">--}}
                                            {{ data_get($record, 'wh_products_stock_level_ok') }}
{{--                                        </a>--}}
                                    </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
