<?php

namespace Tests\Browser\Routes\Admin\Settings\Modules;

use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Throwable;

class QuantityDiscountsPageTest extends DuskTestCase
{
    private string $uri = '/admin/settings/modules/quantity-discounts';

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
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->disableFitOnFailure()
                ->loginAs($user)
                ->visit($this->uri)
                ->assertPathIs($this->uri)
                ->assertSourceMissing('Server Error');
        });
    }
}
