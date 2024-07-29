<?php

namespace Tests\Browser\Routes\Admin\Settings\Modules\QuantityDiscounts;

use App\Modules\QuantityDiscounts\src\Models\QuantityDiscount;
use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class IdPageTest extends DuskTestCase
{
    private string $uri = '/admin/settings/modules/quantity-discounts/{id}';

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->user->assignRole('admin');

        $discount = QuantityDiscount::factory()->create();

        $this->uri = str_replace('{id}', $discount->id, $this->uri);
    }

    /**
     * @throws Throwable
     */
    public function testBasics(): void
    {
        $this->basicAdminAccessTest($this->uri, true);
        $this->basicUserAccessTest($this->uri, false);
        $this->basicGuestAccessTest($this->uri);
    }

    /**
     * @throws Throwable
     */
    public function testPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->disableFitOnFailure()
                ->loginAs($this->user)
                ->visit($this->uri)
                ->assertPathIs($this->uri)
                ->assertSourceMissing('Server Error');
        });
    }
}
