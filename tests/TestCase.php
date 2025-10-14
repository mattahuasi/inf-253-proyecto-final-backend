<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;
use Tests\Traits\AuthenticateUser;
use Tests\Traits\MakeJsonApiRequest;

abstract class TestCase extends BaseTestCase
{
    use MakeJsonApiRequest;
}
