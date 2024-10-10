<?php

namespace Tests;

use App\Console\Commands\ClearDatabaseCommand;
use App\User;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Laravel\Dusk\Browser;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;
use Throwable;

abstract class DuskTestCase extends BaseTestCase
{
    private Browser $browser;

    protected int $superShortDelay = 50;

    protected int $shortDelay = 300;

    protected int $longDelay = 0;

    protected function setUp(): void
    {
        parent::setUp();

        ray()->showApp();

        ray()->clearAll();
        ray()->className($this)->blue();

        ClearDatabaseCommand::resetDatabase();

        Artisan::call('app:install');

        User::factory()->create([
            'email' => 'demo-admin@ship.town',
            'password' => bcrypt('secret1144'),
        ]);
    }

    protected function tearDown(): void
    {
        $this->getBrowser()->quit();

        parent::tearDown();
    }

    public function visit(string $uri, User $user = null): Browser
    {
        return $this->getBrowser()
            ->disableFitOnFailure()
            ->loginAs($user ?? User::factory()->create())
            ->visit($uri)
            ->pause($this->shortDelay)
            ->assertSourceMissing('Server Error')
            ->assertSourceMissing('snotify-error');
    }

    public function getBrowser(): Browser
    {
        $this->browser = $this->browser ?? new Browser($this->driver());

        return $this->browser;
    }

    #[BeforeClass]
    public static function prepare(): void
    {
        if (! static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
        }
    }

    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            '--disable-search-engine-choice-screen',
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=300,650',
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
                '--disable-gpu',
                '--headless=new',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }

    /**
     * @throws Throwable
     */
    public function basicUserAccessTest(string $uri, bool $allowed, ?User $user = null): void
    {
        $this->browse(function (Browser $browser) use ($uri, $allowed, $user) {
            /** @var User $visitor */
            $visitor = $user ?? User::factory()->create()->assignRole('user');

            $browser->disableFitOnFailure();

            $browser->loginAs($visitor);
            $browser->visit($uri);
            $browser->pause($this->shortDelay);

            $browser->assertSourceMissing('Server Error');
            $browser->assertSourceMissing('snotify-error');

            if ($allowed) {
                $browser->assertPathIs($uri);
            } else {
                $browser->assertSee('403');
            }
        });
    }

    /**
     * @throws Throwable
     */
    public function basicAdminAccessTest(string $uri, bool $allowed): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->basicUserAccessTest($uri, $allowed, $user);
    }

    protected static function setEnvironmentValue($key, $value): void
    {
        $path = app()->environmentFilePath();

        if (! file_exists($path)) {
            return;
        }

        $str = file_get_contents($path);

        $str .= "\n"; // In case the searched variable is in the last line without \n
        $keyPosition = strpos($str, "{$key}=");
        $endOfLinePosition = strpos($str, "\n", $keyPosition);
        $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

        // If key does not exist, add it
        if (! $keyPosition || ! $endOfLinePosition || ! $oldLine) {
            $str .= "{$key}={$value}\n";
        } else {
            $str = str_replace($oldLine, "{$key}={$value}", $str);
        }

        file_put_contents($path, $str);
    }

    protected function sendKeysTo(Browser $browser, string $keys): void
    {
        $browser->driver->getKeyboard()->sendKeys($keys);
    }
}
