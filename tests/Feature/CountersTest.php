<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class CountersTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware, WithFaker;
  
    public function testCountersGetAll()
    {
        
        $create = factory(\App\Counters::class, 10)->create()->toArray();

        $response = $this->json('GET', '/api/counters');

        $response->assertStatus(200)
        ->assertJson(['status' => 200, 'payload' => ['count' => 10, 'data' => $create]]);
    }

    public function testCountersStore()
    {
        $data = factory(\App\Counters::class)->make()->toArray();

        $response = $this->json('POST', '/api/counters', $data);

        $response->assertStatus(201)
        ->assertJson(['status' => 201, 'payload' => 'Counter Created']);
    }

    public function testCounterStoreValidation()
    {
        $data = ['name' => 'test'];

        $validateFields = $this->json('POST', '/api/counters', $data);

        $validateFields->assertStatus(422);

        $create = factory(\App\Counters::class)->create();

        $createData = ['counter_name' => $create->counter_name, 'position' => 100];

        $validateUnique = $this->json('POST', '/api/counters', $createData);

        $validateUnique->assertStatus(422);
    }

    public function testCountersUpdate()
    {
        $counter = factory(\App\Counters::class)->create();
        $id = $counter->id;

        $data = factory(\App\Counters::class)->make()->toArray();

        $response = $this->json('PUT', "/api/counters/$id", $data);

        $response->assertStatus(200)
        ->assertJson(['status' => 200, 'payload' => 'Counter Updated']);
    }

    public function testCountersUpdateValidation()
    {
        $id = 1;

        $data = factory(\App\Counters::class)->make()->toArray();

        $validateId = $this->json('PUT', "/api/counters/$id", $data);

        $validateId->assertStatus(404);

        $create = factory(\App\Counters::class,2 )->create()->toArray();
    
        $uniqueData = ['counter_name' => $create[1]['counter_name']];

        $validateUnique = $this->json('PUT', "/api/counters/".$create[0]['id'], $uniqueData);

        $validateUnique->assertStatus(422);

        $validateFields = $this->json('PUT', "/api/counters/".$create[0]['id'], ['name' => 'test']);

        $validateFields->assertStatus(422);
    }
}