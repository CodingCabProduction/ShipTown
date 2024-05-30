<?php

namespace Database\Seeders;

use App\Jobs\DispatchEveryDayEventJob;
use App\Jobs\DispatchEveryFiveMinutesEventJob;
use App\Jobs\DispatchEveryHourEventJobs;
use App\Jobs\DispatchEveryMinuteEventJob;
use App\Jobs\DispatchEveryTenMinutesEventJob;
use App\Models\AutoStatusPickingConfiguration;
use App\Models\NavigationMenu;
use App\Modules\AutoStatusPicking\src\AutoStatusPickingServiceProvider;
use App\Modules\ScurriAnpost\database\seeders\ScurriAnpostSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('up');

        $this->call([
            Demo\ConfigurationSeeder::class,
            Demo\NavigationSeeder::class,
            Demo\OrderStatusesSeeder::class,
            Demo\UsersSeeder::class,

            Demo\WarehousesSeeder::class,

            Demo\ProductsSeeder::class,
            Demo\ProductsTagsSeeder::class,
            Demo\ProductsPricesSeeder::class,
            Demo\InventorySeeder::class,

            // Orders Seeders
            Demo\TestOrdersSeeder::class,
            Demo\PaidOrdersSeeder::class,
            Demo\PaidPickedOrdersSeeder::class,
            Demo\CollectionOrdersSeeder::class,

            // Data Collector Seeders
            Demo\DataCollections\TransferToCorkBranchSeeder::class,
            Demo\DataCollections\TransfersFromWarehouseSeeder::class,
            Demo\DataCollections\ArchivedTransfersFromWarehouseSeeder::class,

            SalesSeeder::class,
            StocktakeSuggestionsSeeder::class,

            PrintNodeClientSeeder::class,
            DpdIrelandSeeder::class,
            DpdUKSeeder::class,
            ScurriAnpostSeeder::class,
            Modules\Slack\ConfigurationSeeder::class,
            Modules\Magento2MSI\ConnectionSeeder::class,

//            RestockingReportSeeder::class,
//            DataCollectionsSeeder::class,
//            RmsapiConnectionSeeder::class,
//            AutomationsSeeder::class,

//            ProductAliasSeeder::class,
//            ProductTagsSeeder::class,
//            SplitOrdersScenarioSeeder::class,
//            Orders_PackingWebDemoSeeder::class,
//            Orders_StorePickupDemoSeeder::class,
//            UnpaidOrdersSeeder::class,
//            ClosedOrdersSeeder::class,
//            PicksSeeder::class,
//            OrderShipmentsSeeder::class,

        ]);

        AutoStatusPickingConfiguration::query()->updateOrCreate(['max_batch_size' => 5]);
        AutoStatusPickingServiceProvider::enableModule();
        NavigationMenu::query()->create([
            'name' => 'Status: picking',
            'url' => '/picklist?status=picking',
            'group' => 'picklist'
        ]);

        DispatchEveryMinuteEventJob::dispatch();
        DispatchEveryFiveMinutesEventJob::dispatch();
        DispatchEveryTenMinutesEventJob::dispatch();
        DispatchEveryHourEventJobs::dispatch();
        DispatchEveryDayEventJob::dispatch();
    }
}
