<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQL\Helpers\Traits\InteractsWithTestUsers;
use Tests\TestCase;

class ClientQueriesTest extends TestCase
{
    use InteractsWithTestUsers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('DatabaseSeeder');
    }
}
