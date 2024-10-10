<?php

namespace Tests\Browser\Routes\Settings\Modules\QuantityDiscounts;

use App\Modules\DataCollectorQuantityDiscounts\src\Models\QuantityDiscount;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class IdPageTest extends DuskTestCase
{
    private string $uri = '/settings/modules/quantity-discounts/{id}';

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->user->assignRole('admin');

        /** @var QuantityDiscount $discount */
        $discount = QuantityDiscount::factory()->create();

        $this->uri = str_replace('{id}', $discount->id, $this->uri);
    }

    /**
     * @throws Throwable
     */
    public function testPage(): void
    {
        $this->basicAdminAccessTest($this->uri, true);
    }
}
