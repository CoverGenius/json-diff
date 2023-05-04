<?php

declare(strict_types=1);

namespace Jet\Tests\Feature;

use Illuminate\Support\Arr;
use Illuminate\Support\Js;
use Jet\JsonDiff\JsonDiff;
use Jet\JsonDiff\KeyAdded;
use Jet\JsonDiff\KeyRemoved;
use Jet\JsonDiff\ValueAdded;
use Jet\JsonDiff\ValueChange;
use Jet\JsonDiff\ValueRemoved;
use Jet\Tests\Factories\ListArrayFactory;
use PHPUnit\Framework\TestCase;

class JsonDiffTest extends TestCase
{
    public function test_basic_value_change_between_jsons()
    {
        $original = [
            'name' => 'Charles',
            'age' => 23,
            'birth_date' => '16/07/1980',
            'sports' => 'basketball',
        ];

        $new = [
            'name' => 'James',
            'age' => 50,
            'birth_date' => '16/06/1960',
            'sports' => 'soccer'
        ];

        $jsonDiff = new JsonDiff($original, $new);

        $jsonDiff->getValuesChanged()->each(function (ValueChange $valueChange) use ($new) {
            $this->assertEquals($new[$valueChange->getPath()], $valueChange->getNewValue());
        });

        $this->assertEquals(4, $jsonDiff->getValuesChanged()->count());

        $this->assertEmpty($jsonDiff->getKeysAdded());
        $this->assertEmpty($jsonDiff->getKeysRemoved());
        $this->assertEmpty($jsonDiff->getValuesAdded());
        $this->assertEmpty($jsonDiff->getValuesRemoved());
    }

    public function test_new_elements_added_between_jsons(){
        $original = [
            'name' => 'Charles',
            'age' => 23,
            'birth_date' => '16/07/1980',
            'sports' => 'basketball',
        ];

        $new = [
            'name' => 'Charles',
            'age' => 23,
            'birth_date' => '16/07/1980',
            'sports' => 'basketball',
            'gender' => 'male',
            'nationality' => 'Singapore'
        ];

        $addedItems = [
            'gender' => 'male',
            'nationality' => 'Singapore'
        ];

        $jsonDiff = new JsonDiff($original, $new);

        $this->assertEquals(2, $jsonDiff->getValuesAdded()->count());
        $this->assertEquals(2, $jsonDiff->getKeysAdded()->count());

        $keysAdded = $jsonDiff->getKeysAdded();
        $valuesAdded = $jsonDiff->getValuesAdded();

        // Check that the new keys are added
        $keysAdded->each(function (KeyAdded $keyAdded) use ($addedItems) {
            $this->assertArrayHasKey($keyAdded->getKey(), $addedItems);
        });

        // Check that new values are added
        $valuesAdded->each(function (ValueAdded $valueAdded) use ($addedItems) {
            $this->assertEquals($addedItems[$valueAdded->getPath()], $valueAdded->getValue());
        });

        $this->assertEmpty($jsonDiff->getKeysRemoved());
        $this->assertEmpty($jsonDiff->getValuesRemoved());
        $this->assertEmpty($jsonDiff->getValuesChanged());
    }

    public function test_new_array_elements_added_between_jsons()
    {
        $original = [
            [
                'name' => 'Charles',
                'age' => 23,
                'birth_date' => '16/07/1985',
                'nationality' => 'Singapore',
            ]
        ];

        $new = [
            [
                'name' => 'James',
                'age' => 31,
                'birth_date' => '16/06/1972',
                'nationality' => 'Korea',
            ],
            [
                'name' => 'Charles',
                'age' => 23,
                'birth_date' => '16/07/1985',
                'nationality' => 'Singapore',
            ],
            [
                'name' => 'Joanne',
                'age' => 25,
                'birth_date' => '16/06/1982',
                'nationality' => 'Sweden',
            ],
        ];

        $addedItems = [
            0 => [
                'name' => 'James',
                'age' => 31,
                'birth_date' => '16/06/1972',
                'nationality' => 'Korea',
            ],
            2 => [
                'name' => 'Joanne',
                'age' => 25,
                'birth_date' => '16/06/1982',
                'nationality' => 'Sweden',
            ]
        ];

        $jsonDiff = new JsonDiff($original, $new);

        $this->assertEquals(2, $jsonDiff->getValuesAdded()->count());
        $this->assertEquals(2, $jsonDiff->getKeysAdded()->count());

        $keysAdded = $jsonDiff->getKeysAdded();
        dd($keysAdded, $jsonDiff);
        $valuesAdded = $jsonDiff->getValuesAdded();

        // Check that the new keys are added
        $keysAdded->each(function (KeyAdded $keyAdded) use ($addedItems) {
            $this->assertArrayHasKey($keyAdded->getKey(), $addedItems);
        });

        // Check that new values are added
        $valuesAdded->each(function (ValueAdded $valueAdded) use ($addedItems) {
            $this->assertEquals($addedItems[$valueAdded->getPath()], $valueAdded->getValue());
        });

        $this->assertEmpty($jsonDiff->getKeysRemoved());
        $this->assertEmpty($jsonDiff->getValuesChanged());
        $this->assertEmpty($jsonDiff->getValuesRemoved());
    }

    public function test_elements_removed_between_jsons()
    {
        $original = [
            'flight_reference' => 'MH10783',
            'booking_reference' => '123-MHS-INS',
            'airline' => 'Test Airline',
            'flight_date' => '12/12/2024',
            'destination' => 'Japan',
            'flight_time' => '7h30m'
        ];

        $new = [
            'flight_reference' => 'MH10783',
            'booking_reference' => '123-MHS-INS',
            'airline' => 'Test Airline',
            'destination' => 'Japan',
        ];

        $removedItems = [
            'flight_date' => '12/12/2024',
            'flight_time' => '7h30m'
        ];

        $jsonDiff = new JsonDiff($original, $new);

        $keysRemoved = $jsonDiff->getKeysRemoved();
        $valuesRemoved = $jsonDiff->getValuesRemoved();

        // Check that the correct keys are removed
        $keysRemoved->each(function (KeyRemoved $keyRemoved) use ($removedItems) {
            $this->assertArrayHasKey($keyRemoved->getKey(), $removedItems);
        });

        // Check that the correct values are removed
        $valuesRemoved->each(function (ValueRemoved $valueRemoved) use ($removedItems) {
            $this->assertEquals($removedItems[$valueRemoved->getPath()], $valueRemoved->getValue());
        });

        $this->assertEmpty($jsonDiff->getKeysAdded());
        $this->assertEmpty($jsonDiff->getValuesAdded());
        $this->assertEmpty($jsonDiff->getValuesChanged());
    }

    public function test_array_elements_removed_between_jsons()
    {
        $original = [
            [
                'flight_reference' => 'AP10622',
                'booking_reference' => '345-AST-INS',
                'airline' => 'Alpaca Airline',
                'flight_date' => '12/12/2024',
                'destination' => 'Fiji',
                'flight_time' => '5h30m'
            ],
            [
                'flight_reference' => 'MH10783',
                'booking_reference' => '123-MHS-INS',
                'airline' => 'Koala Airline',
                'flight_date' => '12/12/2024',
                'destination' => 'Japan',
                'flight_time' => '7h30m'
            ],
            [
                'flight_reference' => 'JT12222',
                'booking_reference' => '222-JTS-INS',
                'airline' => 'Jet Airline',
                'flight_date' => '12/12/2024',
                'destination' => 'France',
                'flight_time' => '10h45m'
            ]
        ];

        $new = [
            [
                'flight_reference' => 'MH10783',
                'booking_reference' => '123-MHS-INS',
                'airline' => 'Koala Airline',
                'flight_date' => '12/12/2024',
                'destination' => 'Japan',
                'flight_time' => '7h30m'
            ],
        ];

        $removedItems = [
            [
                'flight_reference' => 'AP10622',
                'booking_reference' => '345-AST-INS',
                'airline' => 'Alpaca Airline',
                'flight_date' => '12/12/2024',
                'destination' => 'Fiji',
                'flight_time' => '5h30m'
            ],
            [
                'flight_reference' => 'JT12222',
                'booking_reference' => '222-JTS-INS',
                'airline' => 'Jet Airline',
                'flight_date' => '12/12/2024',
                'destination' => 'France',
                'flight_time' => '10h45m'
            ]
        ];

        $jsonDiff = new JsonDiff($original, $new);

        $valuesRemoved = $jsonDiff->getValuesRemoved();

        // Check that the correct number keys are removed
        $this->assertEquals(2, $jsonDiff->getKeysRemoved()->count());

        // Check that the correct values are removed
        $valuesRemoved->each(function (ValueRemoved $valueRemoved) use ($removedItems) {
            $this->assertContains($valueRemoved->getValue(), $removedItems);
        });

        $this->assertEmpty($jsonDiff->getKeysAdded());
        $this->assertEmpty($jsonDiff->getValuesAdded());
        $this->assertEmpty($jsonDiff->getValuesChanged());
    }

    public function test_modify_nested_element_between_jsons()
    {
        $original = [
            'id' => '3fe21e46fd78',
            'company' => 'Alpha Airline',
            'points' => 20000,
            'duration' => 862,
            'segment' => [
                0 => [
                    'duration' => 635,
                    'departureTime' => '2023-05-04 00:53:35',
                    'arrivalTime' => '2023-05-04 11:28:53',
                    'origin' => 'Sydney',
                    'destination' => 'Taiwan',
                    'connectionDuration' => 125,
                ],
                1 => [
                    'duration' => 180,
                    'departureTime' => '2023-05-04 13:33:53',
                    'arrivalTime' => '2023-05-04 16:33:53',
                    'origin' => 'Taiwan',
                    'destination' => 'Korea',
                ],
            ],
        ];

        $new = [
            'id' => '3fe21e46fd78',
            'company' => 'Beta Airline',
            'points' => 50000,
            'duration' => 862,
            'segment' => [
                0 => [
                    'duration' => 635,
                    'departureTime' => '2023-05-04 00:53:35',
                    'arrivalTime' => '2023-05-04 11:28:53',
                    'origin' => 'Sydney',
                    'destination' => 'Japan',
                    'connectionDuration' => 125,
                ],
                1 => [
                    'duration' => 180,
                    'departureTime' => '2023-05-04 13:33:53',
                    'arrivalTime' => '2023-05-04 16:33:53',
                    'origin' => 'Japan',
                    'destination' => 'Korea',
                ],
            ],
        ];

        $modifiedItemsKey = [
            'company',
            'points',
            'segment.0.destination',
            'segment.1.origin'
        ];

        $jsonDiff = new JsonDiff($original, $new);

        $valuesChanged = $jsonDiff->getValuesChanged();

        $valuesChanged->each(function (ValueChange $valueChange) use ($new, $modifiedItemsKey) {
            $this->assertContains($valueChange->getPath(), $modifiedItemsKey);
            $this->assertEquals(Arr::get($new, $valueChange->getPath()), $valueChange->getNewValue());
        });

        $this->assertEmpty($jsonDiff->getKeysAdded());
        $this->assertEmpty($jsonDiff->getKeysRemoved());
        $this->assertEmpty($jsonDiff->getValuesAdded());
        $this->assertEmpty($jsonDiff->getValuesRemoved());
    }

    public function test_add_new_nested_element_between_jsons()
    {
        $original = [
            'id' => '3fe21e46fd78',
            'company' => 'Alpha Airline',
            'points' => 20000,
            'duration' => 862,
            'segment' => [
                0 => [
                    'duration' => 635,
                    'departureTime' => '2023-05-04 00:53:35',
                    'arrivalTime' => '2023-05-04 11:28:53',
                    'origin' => 'Sydney',
                    'destination' => 'Taiwan',
                    'connectionDuration' => 125,
                ],
                1 => [
                    'duration' => 180,
                    'departureTime' => '2023-05-04 13:33:53',
                    'arrivalTime' => '2023-05-04 16:33:53',
                    'origin' => 'Taiwan',
                    'destination' => 'Korea',
                ],
            ],
        ];

        $new = [
            'id' => '3fe21e46fd78',
            'company' => 'Alpha Airline',
            'points' => 20000,
            'duration' => 862,
            'segment' => [
                0 => [
                    'booking_reference' => 'AH15243',
                    'boarding_information' => [
                        'terminal' => 2,
                        'gate' => '15'
                    ],
                    'duration' => 635,
                    'departureTime' => '2023-05-04 00:53:35',
                    'arrivalTime' => '2023-05-04 11:28:53',
                    'origin' => 'Sydney',
                    'destination' => 'Taiwan',
                    'connectionDuration' => 125,
                ],
                1 => [
                    'booking_reference' => 'AH35728',
                    'boarding_information' => [
                        'terminal' => 1,
                        'gate' => '1'
                    ],
                    'duration' => 180,
                    'departureTime' => '2023-05-04 13:33:53',
                    'arrivalTime' => '2023-05-04 16:33:53',
                    'origin' => 'Taiwan',
                    'destination' => 'Korea',
                ],
            ],
        ];

        $addedKeys = [
            'segment.0.booking_reference',
            'segment.0.boarding_information',
            'segment.1.booking_reference',
            'segment.1.boarding_information',
        ];

        $jsonDiff = new JsonDiff($original, $new);

        $valuesAdded = $jsonDiff->getValuesAdded();
        $keysAdded = $jsonDiff->getKeysAdded();

        $keysAdded->each(function (KeyAdded $keyAdded) use ($addedKeys) {
            $this->assertContains($keyAdded->getPath(), $addedKeys);
        });

        $valuesAdded->each(function (ValueAdded $valueAdded) use ($new) {
            $this->assertEquals(Arr::get($new, $valueAdded->getPath()), $valueAdded->getValue());
        });

        $this->assertEmpty($jsonDiff->getKeysRemoved());
        $this->assertEmpty($jsonDiff->getValuesChanged());
        $this->assertEmpty($jsonDiff->getValuesRemoved());
    }

    public function test_remove_nested_element_between_jsons()
    {
        $original = [
            [
                'id' => '3fe21e46fd78',
                'company' => 'Alpha Airline',
                'points' => 20000,
                'duration' => 862,
                'segment' => [
                    0 => [
                        'booking_reference' => 'AH15243',
                        'boarding_information' => [
                            'terminal' => 2,
                            'gate' => '15'
                        ],
                        'duration' => 635,
                        'departureTime' => '2023-05-04 00:53:35',
                        'arrivalTime' => '2023-05-04 11:28:53',
                        'origin' => 'Sydney',
                        'destination' => 'Taiwan',
                        'connectionDuration' => 125,
                    ],
                    1 => [
                        'booking_reference' => 'AH35728',
                        'boarding_information' => [
                            'terminal' => 1,
                            'gate' => '1'
                        ],
                        'duration' => 180,
                        'departureTime' => '2023-05-04 13:33:53',
                        'arrivalTime' => '2023-05-04 16:33:53',
                        'origin' => 'Taiwan',
                        'destination' => 'Korea',
                    ],
                ],
            ],
            [
                'id' => '4fe21e477f78',
                'company' => 'Beta Airline',
                'points' => 10000,
                'duration' => 300,
                'segment' => [
                    0 => [
                        'booking_reference' => 'BH61121',
                        'boarding_information' => [
                            'terminal' => 3,
                            'gate' => '7'
                        ],
                        'duration' => 300,
                        'departureTime' => '2023-05-04 00:53:35',
                        'arrivalTime' => '2023-05-04 05:53:35',
                        'origin' => 'Singapore',
                        'destination' => 'Thailand',
                    ]
                ],
            ]
        ];

        $new = [
            [
                'id' => '4fe21e477f78',
                'company' => 'Beta Airline',
                'points' => 10000,
                'duration' => 300,
                'segment' => [
                    0 => [
                        'booking_reference' => 'BH61121',
                        'duration' => 300,
                        'departureTime' => '2023-05-04 00:53:35',
                        'arrivalTime' => '2023-05-04 05:53:35',
                        'origin' => 'Singapore',
                        'destination' => 'Thailand',
                    ]
                ],
            ]
        ];

        $removedItemsKey = [
            0,
            ''
        ];

        $jsonDiff = new JsonDiff($original, $new);
//        dd($jsonDiff->getKeysRemoved());
    }
}
