@auth
    <nav class="navbar navbar-expand-md navbar-light mb-2 p-0 bg-primary" style="z-index: 1021">
        <div class="container text-white text-nowrap flex-nowrap">
            <div class="d-flex mb-0 navbar-dark w-100 text-nowrap flex-nowrap">
                <a id="products_link" class="btn btn-primary" href="{{ route('products') }}">{{ __('Products') }}</a>
                <a id="orders_link" class="btn btn-primary" href="{{ route('orders') }}">{{ __('Orders') }}</a>

                @if(Auth::user()->warehouse_id)
                    <!-- Tools -->
                    <div class="dropdown">
                        <a id="tools_link" class="dropdown-toggle btn btn-primary" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ __('Tools') }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-left w-auto text-left bg-primary " aria-labelledby="navbarDropdown" >
                            <a class="dropdown-item text-white lightHover mt-1" id="data_collector_link" href="{{ route('data-collector') }}">{{ __('Data Collector') }}</a>
                            <a class="dropdown-item text-white lightHover mt-1" id="stocktaking_link" href="{{ route('stocktaking') }}">{{ __('Stocktaking') }}</a>
                            <a class="dropdown-item text-white lightHover mt-1" id="restocking_link" href="{{ route('reports.restocking.index' , ['sort' => '-quantity_required', 'cache_name' => 'restocking_page']) }}">{{ __('Restocking') }}</a>
                            <a class="dropdown-item text-white lightHover mt-1" id="inventory_movements_link" href="{{ route('reports.inventory-movements.index', ['view' => 'reports.inventory-movements']) }}">{{ __('Inventory Movements') }}</a>
                            <a class="dropdown-item text-white lightHover mt-1 mb-1" id="shelf_label_printing" href="/tools/printer">{{ __('Label Printer') }}</a>
                            <a class="dropdown-item text-white lightHover mt-1 mb-1" id="packlist" href="/autopilot/packlist?step=select">{{ __('Packlist') }}</a>
                            <a class="dropdown-item text-white lightHover mt-1 mb-1" id="packlist" href="/picklist?step=select">{{ __('Picklist') }}</a>
                        </div>
                    </div>
                @endif

                <!-- Reports Dropdown -->
                <div class="dropdown">
                    <a id="navbarDropdown3" class="dropdown-toggle btn btn-primary" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Reports
                    </a>

                    <div class="dropdown-menu dropdown-menu-right bg-primary" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item text-white lightHover" href="{{ route('inventory-dashboard') }}">{{ __('Inventory Dashboard') }}</a>
                        <a class="dropdown-item text-white lightHover" href="{{ route('fulfillment-dashboard') }}">{{ __('Fulfillment Dashboard') }}</a>
                        <a class="dropdown-item text-white lightHover" href="{{ route('fulfillment-statistics') .'?between_dates=-7days,now' }}">{{ __('Fulfillment Statistics') }}</a>
                        <a class="dropdown-item text-white lightHover" href="{{ route('reports.picks.index') }}">{{ __('Order Picks') }}</a>
                        <a class="dropdown-item text-white lightHover" href="{{ route('reports.shipments.index') }}">{{ __('Order Shipments') }}</a>
                        <a class="dropdown-item text-white lightHover" href="{{ route('reports.inventory.index', ['sort' => '-quantity']) }}">{{ __('Inventory') }}</a>
                        <a class="dropdown-item text-white lightHover" href="{{ route('reports.inventory-transfers.index', ['filter[warehouse_code]' =>  data_get(Auth::user(), 'warehouse.code'), 'sort' => '-updated_at']) }}">{{ __('Inventory Transfers') }}</a>
                        <a class="dropdown-item text-white lightHover" href="{{ route('reports.inventory-movements.index') }}">{{ __('Inventory Movements') }}</a>
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

                <div class="flex-fill"></div>

                <!-- Menu -->
                <div class="dropdown dropdown-menu-right">
                    <a style="height: 37px" id="dropdownMenu" class="btn btn-primary" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <font-awesome-icon icon="bars" class="fa-lg"></font-awesome-icon>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right w-auto text-left bg-primary" aria-labelledby="navbarDropdown" >
                        {{-- Profile --}}
                        <a class="dropdown-item text-white lightHover" href="{{ route('setting-profile') }}">{{ __('Profile') }}</a>

                        @hasrole('admin')
                        <a class="dropdown-item text-white lightHover" href="{{ route('settings') }}">{{ __('Settings') }}</a>
                        @endhasrole

                        <a class="dropdown-item text-white lightHover" href="https://ship.town/academy" target="_blank">{{ __('Academy') }}</a>
                        <a class="dropdown-item text-white lightHover" href="https://www.youtube.com/channel/UCl04S5dRXop1ZdZsOqY3OnA" target="_blank">{{ __('YouTube') }}</a>
                        <a class="dropdown-item text-white lightHover" href="https://docs.google.com/spreadsheets/d/1IagdPL-ZKOz0-_Rf83ukhDKj02S-DpgLTz7LrvzWWR4/copy" target="_blank">{{ __('Shelf Labels') }}</a>

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
