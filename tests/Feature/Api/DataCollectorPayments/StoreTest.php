<?php

namespace Tests\Feature\Api\DataCollectorPayments;

use App\Models\DataCollection;
use App\Models\DataCollectionTransaction;
use App\Modules\DataCollectorPayments\src\Models\PaymentType;
use App\User;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private string $uri = 'api/data-collector-payments/';

    /** @test */
    public function testIfCallReturnsOk()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('admin', 'api'));

        /** @var PaymentType $paymentType */
        $paymentType = PaymentType::factory()->create();

        /** @var DataCollection $dataCollection */
        $dataCollection = DataCollection::factory()->create([
            'type' => DataCollectionTransaction::class,
        ]);

        $response = $this->actingAs($user, 'api')->postJson($this->uri, [
            'transaction_id' => $dataCollection->id,
            'payment_type_id' => $paymentType->id,
            'amount' => 1,
        ]);

        ray($response->json());

        $response->assertSuccessful();

        $this->assertDatabaseHas('data_collection_payments', ['id' => $response->json('data.id')]);
    }
}
