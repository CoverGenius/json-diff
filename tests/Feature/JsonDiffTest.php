<?php

declare(strict_types=1);

namespace Jet\Tests\Feature;

use Jet\JsonDiff\JsonDiff;
use Jet\JsonDiff\KeyAdded;
use Jet\JsonDiff\KeyRemoved;
use Jet\JsonDiff\ValueAdded;
use Jet\JsonDiff\ValueChange;
use Jet\JsonDiff\ValueRemoved;
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
            0 => [
                'flight_reference' => 'AP10622',
                'booking_reference' => '345-AST-INS',
                'airline' => 'Alpaca Airline',
                'flight_date' => '12/12/2024',
                'destination' => 'Fiji',
                'flight_time' => '5h30m'
            ],
            2 => [
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
            var_dump($valueRemoved->getValue());
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
    }

    public function testMinimalChangesJsonDiffAddition(): void
    {
        $original = [
            'sports' => [
                [
                    'name' => 'Soccer',
                ],
                [
                    'name' => 'Tennis',
                ],
            ]
        ];

        $new = [
            'sports' => [
                [
                    'name' => 'Swimming',
                ],
                [
                    'name' => 'Soccer',
                    'sub-sports' => [
                        'football'
                    ],
                ],
                [
                    'name' => 'Tennis',
                ],
            ]
        ];

        $jsonDiff = new JsonDiff($original, $new);

        dd($jsonDiff);
    }

    public function testMinimalChangesJsonDiffRemoval(): void
    {
        $original = [
            'sports' => [
                [
                    'name' => 'Soccer',
                ],
                [
                    'name' => 'Tennis',
                ],
            ]
        ];

        $new = [
            'sports' => [
                [
                    'name' => 'Swimming',
                ],
            ]
        ];

        $jsonDiff = new JsonDiff($original, $new);

        dd($jsonDiff);
    }

    public function testComplexMinimalChangesJsonDiff()
    {
        $original = [
            'name' => 'Jet Lim',
            'age' => 23,
            'birth_date' => '16/07/1980',
            'passport' => [
                'id' => 'B12567890',
                'nationality' => 'Malaysian'
            ],
            'sports' => [
                [
                    'name' => 'badminton'
                ],
                [
                    'name' => 'soccer'
                ]
            ],
        ];

        $new = [
            'name' => 'Jet Lim',
            'age' => 23,
            'birth_date' => '16/07/1980',
            'passport' => [
                'id' => 'B12567890',
                'nationality' => 'Malaysian'
            ],
            'sports' => [
                [
                    'name' => 'basketball'
                ],
                [
                    'name' => 'swimming'
                ],
                [
                    'name' => 'cycling'
                ],
                [
                    'name' => 'badminton'
                ],
                [
                    'name' => 'soccer'
                ]
            ],
        ];

        $jsonDiff = new JsonDiff($original, $new);

        dd($jsonDiff);
    }

    public function testMinimalChangesJsonDiffRemovalOfMultipleElements(): void
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

        $jsonDiff = new JsonDiff($original, $new);

        dd($jsonDiff);
    }
}
