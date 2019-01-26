<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketsTest extends TestCase
{
    use WithFaker, RefreshDatabase;
   
    /**
     * Test the method to issue or get a ticket token
     */
    public function testTicketIssueOrGenerate()
    {
        $user = factory(\App\User::class)->create([
            'role' => 2
        ]);

        $token = auth()->login($user);

        $data = ['priority' => 0];

        $response = $this->json('POST', '/api/tickets/generate', $data);

        $response->assertStatus(201)
        ->assertJsonStructure(['payload']);
    }

    public function testTicketGetNowPending()
    {
        $user = factory(\App\User::class)->create([
            'role' => 2
        ]);

        $token = auth()->login($user);
        
        $create = factory(\App\Tickets::class, 10)->create()->toArray();

        $response = $this->json('GET', '/api/tickets/pending');

        $response->assertStatus(200)
        ->assertJson(['payload' => ['count' => 10, 'data' => $create]]);
    }

    public function testTicketCall()
    {

        $user = factory(\App\User::class)->create([
            'role' => 2
        ]);

        $token = auth()->login($user);

        $ticket = factory(\App\Tickets::class)->create();

        $data = ['ticket_id' => $ticket->id];

        $response = $this->json('POST', '/api/tickets/call', $data);

        $response->assertStatus(200)
        ->assertJson(['payload' => 'You called this ticket']);

    }

    public function testTicketCallValidation()
    {
        $user = factory(\App\User::class)->create([
            'role' => 2
        ]);

        $token = auth()->login($user);

        $ticket = factory(\App\Tickets::class)->create();

        $data = ['ticket_id' => 100];

        $test404 = $this->json('POST', '/api/tickets/call', $data);
        
        $test404->assertStatus(404);
        
    }

    public function testTicketServing()
    {
        $user = factory(\App\User::class)->create([
            'role' => 2
        ]);

        $token = auth()->login($user);

        $ticket = factory(\App\Tickets::class)->create([
            'status' => 1
        ]);

        $ticketUser = factory(\App\TicketsUsers::class)->create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'status' => 1
        ]);

        $data = ['ticket_id' => $ticket->id];

        $response = $this->json('POST', '/api/tickets/serving', $data);

        $response->assertStatus(200)
        ->assertJson(['payload' => 'You currently serving/process this ticket']);
    }

    public function testTicketServingValidation()
    {
        $user = factory(\App\User::class)->create([
            'role' => 2
        ]);

        $token = auth()->login($user);

        $ticket = factory(\App\Tickets::class)->create([
            'status' => 2
        ]);

        $ticketUser = factory(\App\TicketsUsers::class)->create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'status' => 2
        ]);

        $data = ['ticket_id' => $ticket->id];

        $testAlreadtServed403 = $this->json('POST', '/api/tickets/serving', $data);

        $testAlreadtServed403->assertStatus(403)
        ->assertJson(['payload' => 'This ticket already been served/process']);
    }
}