# Hyperf Fakie

This PHP Hyperf library aim to help you generate objects fully populated with fake/random data for testing purposes.

[![Latest Stable Version](http://poser.pugx.org/jainec/hyperf-fakie/v)](https://packagist.org/packages/jainec/hyperf-fakie) 
[![License](http://poser.pugx.org/jainec/hyperf-fakie/license)](https://packagist.org/packages/jainec/hyperf-fakie) 
[![PHP Version Require](http://poser.pugx.org/jainec/hyperf-fakie/require/php)](https://packagist.org/packages/jainec/hyperf-fakie)

## Installation

```shell
composer require jainec/hyperf-fakie
```

## Configuration
Publish the config file so you can define your own rules

```shell
php bin/hyperf.php vendor:publish jainec/hyperf-fakie
```
The config file will show up in the following path:
```shell
config/autoload/fakie.php
```
Here in the file you can define your own creation rules for specific classes and properties
```PHP
<?php

declare(strict_types=1);

return [
    'rules' => [
        'App\Entity\User' => [
            'type' => array_rand(['CONSUMER', 'SELLER']),
        ],
        'App\Entity\Order' => [
            'type' => array_rand(['VIRTUAL', 'PHYSICAL']),
        ],
        'Class' => [
            'property1' => 'rule1',
            'property2' => 'rule2',
        ],
    ],
];

```

## Usage

### Simple usage
Without hyperf-fakie
```php
// Ex.: Generating an OrderHistory object for testing purposes
$order = new Order(
    id: rand(),
    type: array_rand(['VIRTUAL', 'PHYSICAL']),
    amount: rand(),
    value: rand() / 100,
);

$user = new User(
    name: Str::random(),
    telephone: Str::random(),
    city: Str::random(),
);

$oder_history = new OrderHistory(
    id: rand(),
    description: Str::random(),
    order: $order,
    user: $user,
);
```

See how it's simple with hyperf-Fakie
```php
// Ex.: Generating an OrderHistory object for testing purposes with fakie
$order_history = Fakie::object(OrderHistory::class)->create();
```
<hr/>

### Overriding properties values
```php
$user = Fakie::object(OrderHistory::class)->create([
    'description' => 'Specific description for specific test case'
]);
```

<hr/>

### Using a method to build the object other than the __construct()
You can use this feature only if your object can be built using a method that **accepts an array** with the **[properties => values]** as argument 
```php
$user = Fakie::object(User::class)->setBuildMethod('fromArray')->create();
```
<hr />

## Important

- Fakie uses the classes properties types to generate and assign random values to them
- If a property doesn't have a defined type, a default *string* value will be assigned to it
- If a property type is *array* or *array<*Type*>* it's recommended to you define your own rules in the config *fakie.php* file. Because we know with PHP we cannot be sure about what is expected inside an array
- If a property type is abstract/interface, you **need** to define your own rules for these cases specifying a concrete class
- You can also use Fakie objects to specify rules in you config file:
```php
<?php

declare(strict_types=1);

return [
    'rules' => [
        'App\Entity\OrderHistory' => [
            'order' => Fakie::object(Order::class), // Don't call the create() method here
            'user' => Fakie::object(user::class)->setBuildMethod('fromArray'), // Don't call the create() method here
        ],
    ],
];
```
<hr />

Feel free to contribute! Help us improve Fakie! 🎉

MIT © 2022 Jaine Conceição Santos

