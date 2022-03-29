<?php

namespace HyperfTest\Unit;

use Fakie\Fakie;
use HyperfTest\User;
use PHPUnit\Framework\TestCase;

class FakieTest extends TestCase
{
    public function testObjectMethodReturnFakieInstance()
    {
        $user = Fakie::object(User::class);

        $this->assertInstanceOf(User::class, $user);
    }
}