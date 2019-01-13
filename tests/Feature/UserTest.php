<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class UserTest extends TestCase
{
    use WithoutMiddleware, RefreshDatabase;
   

    public function testUserCreate()
    {   
        $this->withoutMiddleware();
        
        $data = ['username' => 'test', 
        'password' => 'test', 
        'role' => 2, 
        'email' => 'test@example.com', 
        'name' => 'Test Name'];
        $response = $this->json('POST', '/api/users', $data);

        $response->assertStatus(201)
        ->assertJson(['payload' => 'User Created']);
    }

    public function testUserCreateRoleRestriction()
    {
        $data = ['username' => 'test', 
        'password' => 'test', 
        'role' => 1, 
        'email' => 'test@example.com', 
        'name' => 'Test Name'];
        $response = $this->json('POST', '/api/users', $data);

        $response->assertStatus(401)
        ->assertJson(['payload' => 'Unauthorized Role Assignment']);

    }


    public function testUserResetPassword()
    {
        $this->withoutMiddleware();

        $user = factory(\App\User::class)->create();

        $data = ['username' => $user->username];
        
        $response = $this->json('POST', '/api/users/reset/password', $data);

        $structure = ['payload' => ['message','newPassword']];

        $response->assertStatus(200)
        ->assertJsonStructure($structure);
    }

}