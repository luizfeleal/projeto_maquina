<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    /** @test */
    public function only_loged_in_users_can_see_customers_list()
    {
        $response = $this->get('/dashboard')
            ->assertRedirect('/login');
    }
}
