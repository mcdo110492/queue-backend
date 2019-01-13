<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class AuthenticateTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware, WithFaker;


    
    public function testLogin()
    {
        $this->withoutMiddleware();

        $user = factory(\App\User::class)->create();
        
        $credentials = ['username' => $user->username, 'password' => 'secret'];

        $response = $this->json('POST', '/api/auth/login', $credentials);

        $response->assertStatus(200)
        ->assertJsonStructure(['payload' => ['user', 'token']]);

    }

    public function testMe()
    {

        $user = factory(\App\User::class)->create();

        $token = auth()->login($user); 
  
        $response = $this->json('POST', '/api/auth/me');

        $response->assertStatus(200)
        ->assertJsonStructure(['payload']);
        
    }

    public function testRefresh()
    {
        $user = factory(\App\User::class)->create();

        $token = auth()->login($user);
        
        $response = $this->json('POST', '/api/auth/refresh');

        $response->assertStatus(200)
        ->assertJsonStructure(['payload' => ['token']]);
    }

    public function testLogout()
    {
        $user = factory(\App\User::class)->create();

        $token = auth()->login($user);
        
        $response = $this->json('POST', '/api/auth/logout');

        $response->assertStatus(200)
        ->assertJsonStructure(['payload']);
    }
}