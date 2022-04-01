<?php

declare(strict_types=1);

namespace HyperfTest\Unit;

use HyperfTest\Base\Address;
use HyperfTest\TestObjects\Cat;
use HyperfTest\TestObjects\Dog;
use HyperfTest\TestObjects\Game;
use HyperfTest\TestObjects\Job;
use HyperfTest\TestObjects\PetOwner;
use HyperfTest\TestObjects\PlayPiano;
use HyperfTest\TestObjects\PlayVideoGame;
use HyperfTest\TestObjects\User;
use HyperfTest\TestObjects\UserDTO;
use HyperfTest\TestObjects\WoodHouse;
use JaineC\Hyperf\Fakie\Exception\FakieException;
use JaineC\Hyperf\Fakie\Fakie;
use PHPUnit\Framework\TestCase;

class FakieTest extends TestCase
{
    public function testCreateObjectReturnSuccess()
    {
        $user = Fakie::object(User::class)->create();
        $user_array = $user->toArray();

        $this->assertInstanceOf(User::class, $user);

        $this->assertNotNull($user_array['name']);
        $this->assertNotNull($user_array['age']);
        $this->assertNotNull($user_array['height']);
        $this->assertNotNull($user_array['languages']);
        $this->assertNotNull($user_array['active']);
        $this->assertNotNull($user_array['nickname']);

        $this->assertEquals('default', $user_array['hobie']);
        $this->assertEquals('12345678910', $user_array['cpf']);
    }

    public function testCreateObjectOverridingPropertiesReturnSuccess()
    {
        $expected_value = [
            'age' => 23,
            'languages' => ['brazilian-pt', 'english'],
        ];

        $user = Fakie::object(User::class)->create([...$expected_value]);

        $this->assertEquals($expected_value['age'], $user->toArray()['age']);
        $this->assertEquals($expected_value['languages'], $user->toArray()['languages']);
    }

    public function testCreateObjectSettingArrayBuildMethodReturnSuccess()
    {
        $job = Fakie::object(Job::class)->setBuildMethod('fromArray')->create();

        $this->assertInstanceOf(Job::class, $job);

        $this->assertNotNull($job->toArray()['role']);
        $this->assertNotNull($job->toArray()['salary']);
    }

    public function testCreateObjectWithoutAnyPropertyReturnSuccess()
    {
        $this->expectException(FakieException::class);
        $this->expectExceptionMessage('Error trying to fetch class properties. Maybe your class has no constructor/properties/build method: Class "HyperfTest\Base\Address');

        $address = Fakie::object(Address::class)->create();

        $this->assertInstanceOf(Address::class, $address);
    }

    public function testCreateObjectWithFakieRuleInConfigRules()
    {
        $cat = Fakie::object(Cat::class)->create();

        $this->assertInstanceOf(Cat::class, $cat);
        $this->assertNotNull($cat->toArray()['name']);
        $this->assertNotNull($cat->toArray()['house']);
        $this->assertNotNull($cat->toArray()['house']['size']);
        $this->assertNotNull($cat->toArray()['house']['color']);
    }

    public function testCreateObjectWithPropertyClassTypeReturnSuccess()
    {
        $pet_owner = Fakie::object(PetOwner::class)->create();

        $this->assertInstanceOf(PetOwner::class, $pet_owner);
        $this->assertNotNull($pet_owner->toArray()['pet']);
        $this->assertNotNull($pet_owner->toArray()['pet']['name']);
        $this->assertNotNull($pet_owner->toArray()['pet']['age']);
    }

    public function testCreateObjectReturnInvalidMethodNameException()
    {
        $this->expectException(FakieException::class);
        $this->expectExceptionMessage('The object class name is invalid');

        Fakie::object('')->create();
    }

    public function testCreateObjectWithPropertyInterfaceTypeReturnException()
    {
        $this->expectException(FakieException::class);
        $this->expectExceptionMessage('The property house is an interface type');

        Fakie::object(WoodHouse::class)->create();
    }

    public function testCreateObjectWithPropertyAbstractTypeReturnException()
    {
        $this->expectException(FakieException::class);
        $this->expectExceptionMessage("It's not possible to instantiate the abstract property type HyperfTest\\TestObjects\\Hobie");

        Fakie::object(PlayVideoGame::class)->create();
    }

    public function testCreateObjectWitNoConstructorNeitherBuildMethodReturnError()
    {
        $this->expectException(FakieException::class);
        $this->expectExceptionMessage('Error trying to fetch class properties. Maybe your class has no constructor/properties/build method: ');

        Fakie::object(PlayPiano::class)->create();
    }

    public function testCreateObjectWithConstructorWithMoreParametersThanPropertiesReturnSuccess()
    {
        $game = Fakie::object(Game::class)->create();

        $this->assertInstanceOf(Game::class, $game);
    }

    public function testCreateObjectSettingInvalidBuildMethodReturnException()
    {
        $this->expectException(\TypeError::class);

        Fakie::object(Job::class)->setBuildMethod('fromParameters')->create();
    }

    public function testCreateObjectThatExtendingAnAbstractClassUsingBuildMethod()
    {
        $user_dto = Fakie::object(UserDTO::class)->setBuildMethod('fromArray')->create();

        $this->assertInstanceOf(UserDTO::class, $user_dto);
        $this->assertNotNull($user_dto->toArray()['name']);
        $this->assertNotNull($user_dto->toArray()['age']);
        $this->assertNotNull($user_dto->toArray()['height']);
    }

    public function testCreateObjectWithBuildMethodAndRemovingUndesiredPropertiesReturnSuccess()
    {
        $dog = Fakie::object(Dog::class)->setBuildMethod('fromArray', ['owners'])->create();

        $dog_array = $dog->toArray();

        $this->assertInstanceOf(Dog::class, $dog);
        $this->assertNotNull($dog_array['id']);
        $this->assertNotNull($dog_array['height']);
    }
}
