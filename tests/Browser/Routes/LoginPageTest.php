<?php

namespace Tests\Browser\Routes;

use App\User;
use Tests\DuskTestCase;
use Throwable;

class LoginPageTest extends DuskTestCase
{
    private string $uri = '/login';

    /**
     * @throws Throwable
     */
    public function testBasics(): void
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}
