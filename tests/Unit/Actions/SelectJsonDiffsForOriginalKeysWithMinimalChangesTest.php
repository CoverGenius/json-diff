<?php

declare(strict_types=1);

namespace Jet\Tests\Unit\Actions;

use Carbon\Carbon;
use Illuminate\Container\Container;
use Jet\JsonDiff\Actions\CalculateAllDifferencesRespectiveToOriginalAction;
use Jet\JsonDiff\Actions\SelectJsonDiffsForOriginalKeysWithMinimalChangesAction;
use Jet\JsonDiff\Actions\SortDifferencesByNumberOfChangesAction;
use Jet\Tests\TestCase;

class SelectJsonDiffsForOriginalKeysWithMinimalChangesTest extends TestCase
{
    /**
     * Test the action will create a mapping for each original array element to an element in the new array if the
     * new array has greater or equal number of array elements to the original array.
     *
     * Note:
     * The new array element mapped to the original array element will always have the least amount of changes in
     * respect to the original element.
     */
    public function test_each_original_keys_have_a_mapping_to_a_new_key_if_new_array_has_greater_or_equal_elements_to_the_original_array(): void
    {
        $originalArray = [
            [
                'label' => 'post1',
                'description' => null,
                'meta' => [
                    'created_at' => Carbon::now(),
                ],
            ],
            [
                'label' => 'post2',
                'description' => 'description for test post 2',
                'meta' => null,
            ],
        ];

        $newArray = [
            [
                'label' => 'post1',
                'description' => 'description for test post 1',
                'meta' => [
                    'created_at' => Carbon::now(),
                    'comment_count' => 25,
                ],
            ],
            [
                'label' => 'post2',
                'description' => 'description for test post 2',
                'meta' => null,
            ],
            [
                'label' => 'post3',
                'description' => 'description for test post 3',
                'meta' => null,
            ],
        ];

        $diffMappings = $this->container(CalculateAllDifferencesRespectiveToOriginalAction::class)->execute($originalArray, $newArray, '');
        $diffMappings = $this->container(SortDifferencesByNumberOfChangesAction::class)->execute($diffMappings);
        $diffMappings = $this->container(SelectJsonDiffsForOriginalKeysWithMinimalChangesAction::class)->execute($diffMappings);

        // Assert that both original array elements have a mapping to the new array elements
        $this->assertSame(2, $diffMappings->count());
    }

    private function container($abstract = null, array $parameters = [])
    {
        if (null === $abstract) {
            return Container::getInstance();
        }

        return Container::getInstance()->make($abstract, $parameters);
    }
}
