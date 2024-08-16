<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function actingAsAdmin()
    {
        $admin = User::factory()->create([
            'is_admin' => true, // Adjust according to your implementation
        ]);

        return $this->actingAs($admin);
    }
}
