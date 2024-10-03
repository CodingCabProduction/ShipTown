<?php

namespace Tests\Feature\Api\DataCollectorPaymentTypes\DataCollectorPaymentType;

use App\Modules\DataCollectorPayments\src\Models\PaymentType;
use App\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    private string $uri = 'api/data-collector-payment-types/';

    /** @test */
    public function testIfCallReturnsOk(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('admin', 'api'));

        /** @var PaymentType $paymentTypeToDelete */
        $paymentTypeToDelete = PaymentType::factory()->create();

        $response = $this->actingAs($user, 'api')->delete($this->uri.$paymentTypeToDelete->id);

        ray($response->json());

        $response->assertOk();

        $this->assertSoftDeleted('payment_types', ['id' => $paymentTypeToDelete->id]);
    }
}
