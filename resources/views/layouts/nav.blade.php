@auth
    <nav class="navbar navbar-expand-md navbar-light mb-2 p-0 bg-primary" style="z-index: 1021">
        <div class="container text-white text-nowrap flex-nowrap">
            <div class="d-flex mb-0 navbar-dark w-100 text-nowrap flex-nowrap">
                <!-- Products -->
                <div class="dropdown">
                    <a id="products_link" class="dropdown-toggle btn btn-primary px-2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ __('Products') }}
                    </a>

                    <div class="dropdown-menu dropdown-menu-left w-auto text-left bg-primary " aria-labelledby="navbarDropdown" >
                        <a class="dropdown-item text-white lightHover mt-1" id="inventory_link" href="{{ route('products') }}">{{ __('Inventory') }}</a>
                        <a class="dropdown-item text-white lightHover mt-1" id="transfers_in_link" href="{{ route('data-collector', ['filter[type]' => 'App\\Models\\DataCollectionTransferIn']) }}">{{ __('Transfers In') }}</a>
                        <a class="dropdown-item text-white lightHover mt-1" id="transfers_out_link" href="{{ route('data-collector', ['filter[type]' => 'App\\Models\\DataCollectionTransferOut']) }}">{{ __('Transfers Out') }}</a>
                        <a class="dropdown-item text-white lightHover mt-1" id="stocktaking_link" href="{{ route('stocktaking') }}">{{ __('Stocktaking') }}</a>
                    </div>
                </div>

                <a id="orders_link" class="btn btn-primary px-2" href="{{ route('orders') }}">{{ __('Orders') }}</a>

                @if(Auth::user()->warehouse_id)
                    <!-- Tools -->
                    <div class="dropdown">
                        <a id="tools_link" class="dropdown-toggle btn btn-primary px-2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ __('Tools') }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-left w-auto text-left bg-primary " aria-labelledby="navbarDropdown" >
                            <a class="dropdown-item text-white lightHover mt-1" id="data_collector_link" href="{{ route('data-collector', ['filter[type]' => 'App\\Models\\DataCollectionTransferIn']) }}">{{ __('Data Collector') }}</a>
                            <a class="dropdown-item text-white lightHover mt-1" id="restocking_link" href="{{ route('reports.restocking.index' , ['sort' => '-quantity_required', 'cache_name' => 'restocking_page']) }}">{{ __('Restocking') }}</a>
                            <a class="dropdown-item text-white lightHover mt-1 mb-1" id="packlist" href="/autopilot/packlist?step=select">{{ __('Packlist') }}</a>
                            <a class="dropdown-item text-white lightHover mt-1 mb-1" id="packlist" href="/picklist?step=select">{{ __('Picklist') }}</a>
                            <a class="dropdown-item text-white lightHover mt-1 mb-1" id="shelf_label_printing" href="/tools/printer">{{ __('Shelf Labels') }}</a>
                        </div>
                    </div>
                @endif

                <!-- Middle empty fill -->
                <div class="flex-fill"></div>

                <!-- Reports Dropdown -->
                <div class="dropdown">
                    <a id="navbarDropdown3" class="dropdown-toggle btn btn-primary px-2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Reports
                    </a>

                    <div class="dropdown-menu dropdown-menu-right bg-primary" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item text-white lightHover" href="{{ route('inventory-dashboard') }}">{{ __('Inventory Dashboard') }}</a>
                        <a class="dropdown-item text-white lightHover" href="{{ route('fulfillment-dashboard') }}">{{ __('Fulfillment Dashboard') }}</a>
                        <a class="dropdown-item text-white lightHover" href="{{ route('fulfillment-statistics') .'?between_dates=-7days,now' }}">{{ __('Fulfillment Statistics') }}</a>
                        <a class="dropdown-item text-white lightHover" href="{{ route('reports.picks.index') }}">{{ __('Order Picks') }}</a>
                        <a class="dropdown-item text-white lightHover" href="{{ route('reports.shipments.index') }}">{{ __('Order Shipments') }}</a>
                        <a class="dropdown-item text-white lightHover" href="{{ route('reports.inventory.index', ['filter[warehouse_code]' =>  data_get(Auth::user(), 'warehouse.code'), 'sort' => '-quantity']) }}">{{ __('Inventory') }}</a>
                        <a class="dropdown-item text-white lightHover" href="{{ route('reports.inventory-movements.index', ['filter[warehouse_code]' =>  data_get(Auth::user(), 'warehouse.code'), 'sort' => '-occurred_at,-sequence_number']) }}">{{ __('Inventory Movements') }}</a>
                        <a class="dropdown-item text-white lightHover" href="{{ route('reports.inventory-transfers.index', ['filter[warehouse_code]' =>  data_get(Auth::user(), 'warehouse.code'), 'sort' => '-updated_at']) }}">{{ __('Inventory Transfers') }}</a>
                        <a class="dropdown-item text-white lightHover" href="{{ route('reports.inventory-movements-summary.index', ['per_page' => '200', 'sort' => 'type']) }}">{{ __('Inventory Movements Summary') }}</a>
                        <a class="dropdown-item text-white lightHover" href="{{ route('reports.restocking.index') }}">{{ __('Restocking') }}</a>
                        <a class="dropdown-item text-white lightHover" href="{{ route('reports.stocktake-suggestions.index') }}">{{ __('Stocktake Suggestions') }}</a>
                        <a class="dropdown-item text-white lightHover" href="{{ route('reports.activity-log.index') }}">{{ __('Activity Log') }}</a>

                        @if(count($navigationMenuReports) > 0)
                            <hr v-if='{{ count($navigationMenuReports) > 0 }}' class="mb-1 mt-1">
                            @foreach ($navigationMenuReports as $menu)
                                <a class="dropdown-item text-white lightHover" href="{{ $menu->url }}">
                                    {{ $menu->name }}
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Menu -->
                <div class="dropdown dropdown-menu-right">
                    <a style="height: 37px; width: 40px; position: relative; top: -2px; right: 2px" id="dropdownMenu" class="btn btn-primary px-2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" height="20" width="16" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path fill="#ffffff" d="M0 96C0 78.3 14.3 64 32 64H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z"/></svg>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right w-auto text-left bg-primary" aria-labelledby="navbarDropdown" >
                        {{-- Profile --}}
                        <a class="dropdown-item text-white lightHover" href="{{ route('setting-profile') }}">{{ __('Profile') }}</a>

                        @hasrole('admin')
                        <a class="dropdown-item text-white lightHover" href="{{ route('settings') }}">{{ __('Settings') }}</a>
                        @endhasrole

                        <a class="dropdown-item text-white lightHover" href="https://ship.town/academy" target="_blank">{{ __('Academy') }}</a>
                        <a class="dropdown-item text-white lightHover" href="https://www.youtube.com/channel/UCl04S5dRXop1ZdZsOqY3OnA" target="_blank">{{ __('YouTube') }}</a>

                        <!-- Logout -->
                        <a class="dropdown-item text-white lightHover" href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                               document.getElementById('logout-form').submit();">
                            {{ __('Logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>
@endauth
