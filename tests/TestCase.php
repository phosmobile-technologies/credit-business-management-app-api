<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Tests\GraphQL\Helpers\Traits\InteractsWithGraphQLRequests;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, MakesGraphQLRequests, InteractsWithGraphQLRequests;
}
