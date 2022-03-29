# Fakie

A PHP Hyperf library to help you generate objects fully populated with fake/random data for testing purposes.

## Problem
Generate fully populated tests objects with fake/random data is repetitive and boring

```php
// Ex.: Generating a TransactionAggregate object for testing purpose
$address = new UserAddress(
    id: $this->uuid->uuid4(),
    street: Str::random(),
    number: rand(1, 100),
    city: Str::random(),
    country: Str::random(),
);

$contact = new UserContact(
    email = Str::random(),
    telephone = Str::random(),
    twitter = Str::random(),
);

$user = new User(
    cpf: Str::random(),
    age: rand(1, 100),
    address: $address,
    contact: $contact,
    active: true,
);
```

<hr/>

## Solution
Use *Fakie*

- It creates fully populated test objects for you
- It uses the attributes types to generate and assign random values to them
- If an attribute doesnt have a defined type, a random *integer* value will be assigned to it
- If an attribute type is abstract/interface, Fakie will find and create the first concrete found implementation and assign to it
- You can override attributes with desired values
- You can set specific rules for your objects creation **(We strongly recommend doing this for non-typed attributes and also abstract/interface type attributes)**


### Simple Usage
```php
// Ex.: Generating a TransactionAggregate object for testing purpose
$user = Fakie::object(User::class)->create();

// Return
User(
    '12345678910',
    23,
    Address(
        '123e4567-e89b-12d3-a456-426655440000',
        'randomstring1',
        100,
        'randomstring1',
        'randomstring1',
    ),
    Contact(
        'randomstring1',
        'randomstring2',
        'randomstring3',
    ),
    true,
)
```

### Overriding attributes values
```php
$user = Fakie::object(User::class)->create([
    'age' => 30
]);
```

### Setting desired rules
Use the fakie.php config file
```php
// fakie.php

return [
    'rules' => [
        'App\Domain\Entity\User' => [
            'cpf' => $faker->cpf,
        ],
    ],
];
```

### Using a method to build the object other than the __construct()
```php
$user = Fakie::object(UserDTO::class)
    ->setBuildMethod('fromArray')
    ->create();
```