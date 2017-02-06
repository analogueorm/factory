<?php

use Stubs\User;

class FactoryTest extends FactoryTestCase
{

    /** @test */
    public function it_generates_entities()
    {
        $user = analogue_factory(User::class)->make();
        $this->assertInstanceOf(User::class, $user);
    }

    /** @test */
    public function it_creates_entities()
    {
        $user = analogue_factory(User::class)->create();
        $this->seeInDatabase('users', [
            'id' => $user->id,
        ]);
    }

    /** @test */
    public function it_generate_entities_with_states()
    {
        $user = analogue_factory(User::class)->states('custom')->make();
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('custom', $user->custom);
        $this->assertNotNull($user->email);
    }

}