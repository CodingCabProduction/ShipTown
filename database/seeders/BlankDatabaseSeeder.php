<?php

namespace Database\Seeders;

use App\Jobs\DispatchEveryHourEventJobs;
use App\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class BlankDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            Demo\ConfigurationSeeder::class,

//            WebhooksTestSeeder::class,
//            RmsapiConnectionSeeder::class,


//            AutomationsSeeder::class,
//            PrintNodeClientSeeder::class,
//            DpdIrelandSeeder::class,
//            ProductAliasSeeder::class,
//            ProductTagsSeeder::class,
//            ProductPriceSeeder::class,
//            SplitOrdersScenarioSeeder::class,
//            Orders_PackingWebDemoSeeder::class,
//            Orders_StorePickupDemoSeeder::class,
//            UnpaidOrdersSeeder::class,
//            ClosedOrdersSeeder::class,
//            PicksSeeder::class,
//            OrderShipmentsSeeder::class,
        ]);

        DispatchEveryHourEventJobs::dispatchSync();
    }
}
