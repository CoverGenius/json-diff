<?php

declare(strict_types=1);

namespace Jet\Tests\Feature;

use Jet\JsonDiff\JsonDiff;
use PHPUnit\Framework\TestCase;

class JsonDiffTest extends TestCase
{
    public function testJsonDiff()
    {
        $original = [
            'name' => 'Jet Lim',
            'age' => 23,
            'birth_date' => '16/07/1980',
            'sports' => 'basketball',
        ];

        $new = [
            'name' => 'Ruben Funal',
            'age' => 50,
            'birth_date' => '16/06/1960',
            'hobby' => 'running',
            'sports' => [
                'rugby'
            ]
        ];

        $jsonDiff = new JsonDiff($original, $new);
    }

    public function testRecursiveJsonDiff()
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
            'name' => 'Ruben Funal',
            'age' => 50,
            'birth_date' => '16/06/1960',
            'passport' => [
                'id' => 'A53447892',
                'nationality' => 'Mejico',
                'favourite_food' => 'Doritos'
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
                ]
            ],

        ];

        $jsonDiff = new JsonDiff($original, $new);
    }

    public function testComplexRecursionJsonDiff()
    {
        $original = [
            [
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
            ]
        ];

        $new = [
            [
                'name' => 'Ruben Funal',
                'age' => 50,
                'birth_date' => '16/06/1960',
                'passport' => [
                    'id' => 'A53447892',
                    'nationality' => 'Mejico',
                    'favourite_food' => 'Doritos'
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
                    ]
                ]
            ],
            [
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
            ]
        ];

        $jsonDiff = new JsonDiff($original, $new);

        dd($jsonDiff);
    }

    public function testComplexRecursion2JsonDiff()
    {
        $original = [
            [
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
            ]
        ];

        $new = [
            [
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
                        'name' => 'rugby'
                    ],
                    [
                        'name' => 'soccer'
                    ]
                ],
            ]
        ];

        $jsonDiff = new JsonDiff($original, $new);

        dd($jsonDiff);
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
