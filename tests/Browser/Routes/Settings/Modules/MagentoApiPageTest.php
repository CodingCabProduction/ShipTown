<?php

namespace Tests\Browser\Routes\Settings\Modules;

use App\Modules\MagentoApi\src\EventServiceProviderBase;
use App\User;
use Tests\DuskTestCase;
use Throwable;

class MagentoApiPageTest extends DuskTestCase
{
    private string $uri = '/settings/modules/magento-api';

    /**
     * @throws Throwable
     */
    public function testBasics(): void
    {
        EventServiceProviderBase::enableModule();

        $admin = User::factory()->create()->assignRole('admin');

        $this->visit($this->uri, $admin)
            ->waitForText('Magento Api Configurations');
    }
}
