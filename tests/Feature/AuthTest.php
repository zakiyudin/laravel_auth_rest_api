<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use WithFaker;
    private $myPasswrd = '123456';
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testSignup()
    {
        $name = $this->faker->name;
        $email = $this->faker->email;

        $response = $this->postJson('/api/auth/signup', [
            'name' => $name,
            'email' => $email,
            'password' => $this->myPasswrd,
            'password_confirmation' => $this->myPasswrd,
        ]);

        $response->assertStatus(201)->assertExactJson([
            'message' => 'Successfully created user!'
        ]);
    }

    public function testLogin()
    {
        $name = $this->faker->name;
        $email = $this->faker->email;

        $user = new User([
            'name' => $name,
            'email' => $email,
            'password' => \bcrypt($this->myPasswrd),
        ]);

        $user->save();

        $response = $this->postJson('api/auth/login', [
            'email' => $email,
            'password' => $this->myPasswrd,
        ]);

        $response->assertStatus(200);
        $this->assertAuthenticated();
    }
}
