<?php

declare(strict_types=1);

namespace Storyblok\Mapi\Endpoints;

use Storyblok\Mapi\Data\SpaceData;
use Storyblok\Mapi\Data\StoriesData;
use Storyblok\Mapi\Data\StoryblokData;
use Storyblok\Mapi\Data\StoryData;
use Storyblok\Mapi\Endpoints\EndpointBase;
use Storyblok\Mapi\StoryblokResponseInterface;

/**
 *
 */
class StoryApi extends EndpointSpace
{
    public function all(): \Generator
    {
        $pageNumber = 1;
        $itemsPerPage = 5;
        $totalPages = null;

        do {

            // Fetch the current page
            $response = $this->page($pageNumber, $itemsPerPage);

            if ($response->isOk()) {
                // Print the total number of tags (only on the first iteration)
                if ($totalPages === null) {
                    //echo "Total Stories: " . $response->total() . PHP_EOL;
                    // Calculate the total number of pages
                    $totalPages = ceil($response->total() / $itemsPerPage);
                }

                /** @var StoriesData $stories */
                $stories = $response->data();
                foreach ($stories as $story) {
                    yield $story;
                }

                // Move to the next page
                ++$pageNumber;
            } elseif ($response->getResponseStatusCode() === 429) {
                echo "Rate limit reached. Retrying in 1 second..." . PHP_EOL;
                sleep(1);
                // Wait for 1 second before retrying
            } else {
                // Handle other exceptions
                echo "An error occurred: " . $response->getErrorMessage() . PHP_EOL;
                break; // Exit the loop on non-recoverable errors
            }

        } while ($pageNumber <= $totalPages);
    }

    public function page(int $page = 1, int $perPage = 25): StoryblokResponseInterface
    {
        $options = [
            'query' => [
                'page' => $page,
                'per_page' => $perPage,
            ],
        ];
        return $this->makeRequest(
            "GET",
            '/v1/spaces/' . $this->spaceId . '/stories',
            options: $options,
            dataClass: StoriesData::class,
        );
    }


    /**
     * @param $storyId
     */
    public function get(string $storyId): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "GET",
            '/v1/spaces/' . $this->spaceId . '/stories/' . $storyId,
            dataClass: StoryData::class,
        );
    }

    public function create(StoryData $storyData): StoryblokResponseInterface
    {
        if (! $storyData->hasKey("content")) {
            $storyData->setContent([
                "component" => $storyData->defaultContentType(),
            ]);
        }

        return $this->makeRequest(
            "POST",
            "/v1/spaces/" . $this->spaceId . "/stories",
            [
                "body" => [
                    "story" => $storyData->toArray(),
                ],
            ],
            dataClass: StoryData::class,
        );
    }



}
