<?php

namespace Tests\Feature;

use App\Models\DebitCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DebitCardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Passport::actingAs($this->user);
    }

    public function testCustomerCanSeeAListOfDebitCards()
    {
        // get /debit-cards
        DebitCard::factory(3)->create([
            'user_id' => $this->user->id,
            'disabled_at' => null
        ]);

        $res = $this->getJson('api/debit-cards');
        $res->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => ['id', 'number', 'type', 'expiration_date', 'is_active']
            ]);
    }

    public function testCustomerCannotSeeAListOfDebitCardsOfOtherCustomers()
    {
        // get /debit-cards
        $otherUser = User::factory()->create();
        $otherUserDebitCard = DebitCard::factory(2)->create([
            'user_id' => $otherUser->id, 
            'disabled_at' => null
        ]);
        DebitCard::factory()->create([
            'user_id' => $this->user->id, 
            'disabled_at' => null
        ]);

        $res = $this->getJson('api/debit-cards');
        $res->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonStructure([
                '*' => ['id', 'number', 'type', 'expiration_date', 'is_active']
            ]);

        $res->assertJsonMissing(['id' => $otherUserDebitCard[0]->id])
            ->assertJsonMissing(['id' => $otherUserDebitCard[1]->id]);
    }

    public function testCustomerCanCreateADebitCard()
    {
        // post /debit-cards
        $data = [
            "type" => "GPN"
        ];

        $res = $this->postJson('api/debit-cards', $data);
        $res->assertStatus(201)
            ->assertJsonFragment($data)
            ->assertJsonStructure(['id', 'number', 'type', 'expiration_date', 'is_active']);

        $this->assertDatabaseHas('debit_cards', array_merge($data, ['user_id' => $this->user->id]));
    }

    public function testCustomerCanSeeASingleDebitCardDetails()
    {
        // get api/debit-cards/{debitCard}
        $debitCard = DebitCard::factory()->create([
            'user_id' => $this->user->id,
            'disabled_at' => null
        ]);

        $res = $this->getJson('/api/debit-cards/'.$debitCard->id);
        $res->assertStatus(200)
            ->assertJsonFragment(['id' => $debitCard->id])
            ->assertJsonStructure(['id', 'number', 'type', 'expiration_date', 'is_active']);
    }

    public function testCustomerCannotSeeASingleDebitCardDetails()
    {
        // get api/debit-cards/{debitCard}
    }

    public function testCustomerCanActivateADebitCard()
    {
        // put api/debit-cards/{debitCard}
    }

    public function testCustomerCanDeactivateADebitCard()
    {
        // put api/debit-cards/{debitCard}
    }

    public function testCustomerCannotUpdateADebitCardWithWrongValidation()
    {
        // put api/debit-cards/{debitCard}
    }

    public function testCustomerCanDeleteADebitCard()
    {
        // delete api/debit-cards/{debitCard}
    }

    public function testCustomerCannotDeleteADebitCardWithTransaction()
    {
        // delete api/debit-cards/{debitCard}
    }

    // Extra bonus for extra tests :)
}
