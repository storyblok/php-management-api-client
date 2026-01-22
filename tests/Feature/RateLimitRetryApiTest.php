<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Storyblok\ManagementApi\Endpoints\StoryApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Storyblok\ManagementApi\QueryParameters\Filters\Filter;
use Storyblok\ManagementApi\QueryParameters\Filters\QueryFilters;
use Storyblok\ManagementApi\Response\StoriesResponse;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Symfony\Component\HttpClient\RetryableHttpClient;

final class RateLimitRetryApiTest extends TestCase
{
    public function testRetryMechanismWithListStories(): void
    {
        $responses = [
            $this->mockResponse("list-stories-page-1", 429),
            $this->mockResponse("list-stories-page-1", 429),
            $this->mockResponse("list-stories-page-1", 200, [
                "total" => 6,
                "per-page" => 2,
                "page" => 1,
            ]),
            $this->mockResponse("list-stories-page-2", 429),
            $this->mockResponse("list-stories-page-2", 429),
            $this->mockResponse("list-stories-page-2", 200, [
                "total" => 6,
                "per-page" => 2,
                "page" => 2,
            ]),
            $this->mockResponse("list-stories-page-3", 200, [
                "total" => 6,
                "per-page" => 2,
                "page" => 3,
            ]),
        ];

        $client = new RetryableHttpClient(
            new MockHttpClient($responses),
            new GenericRetryStrategy([429], delayMs: 0),
        );

        $mapiClient = ManagementApiClient::initTest($client);
        $storyApi = new StoryApi($mapiClient, "222");

        $storyResponse = $storyApi->page(
            queryFilters: new QueryFilters()->add(
                new Filter("headline", "like", "Development"),
            ),
        );

        $this->assertSame("my-first-post", $storyResponse->data()->get("0.slug"));

        $storyResponse = $storyApi->page(
            queryFilters: new QueryFilters()->add(
                new Filter("headline", "like", "Development"),
            ),
        );

        $this->assertSame("my-third-post", $storyResponse->data()->get("0.slug"));
    }

    public function testRetryMechanismWithListStories2(): void
    {
        $responses = [
            $this->mockResponse("list-stories-page-1", 429),
            $this->mockResponse("list-stories-page-1", 429),
            $this->mockResponse("list-stories-page-1", 200, [
                "Total" => 6,
                "per-page" => 2,
                "page" => 1,
            ]),
            $this->mockResponse("list-stories-page-2", 429),
            $this->mockResponse("list-stories-page-2", 429),
            $this->mockResponse("list-stories-page-2", 200, [
                "total" => 6,
                "per-page" => 2,
                "page" => 2,
            ]),
            $this->mockResponse("list-stories-page-3", 200, [
                "total" => 6,
                "per-page" => 2,
                "page" => 3,
            ]),
        ];

        $client = new RetryableHttpClient(
            new MockHttpClient($responses),
            new GenericRetryStrategy([429], delayMs: 0),
        );

        $mapiClient = ManagementApiClient::initTest($client);
        $storyApi = new StoryApi($mapiClient, "222");

        $storyResponse = $storyApi->page(
            queryFilters: new QueryFilters()->add(
                new Filter("headline", "like", "Development"),
            ),
        );

        $this->assertSame("my-first-post", $storyResponse->data()->get("0.slug"));
        $this->assertSame(6, $storyResponse->total());
        $this->assertCount(2, $storyResponse->data());

        $storyResponse = $storyApi->page(
            queryFilters: new QueryFilters()->add(
                new Filter("headline", "like", "Development"),
            ),
        );

        $this->assertSame("my-third-post", $storyResponse->data()->get("0.slug"));
    }

    public function testRetryMechanismWith500Exception(): void
    {
        $responses = [
            $this->mockResponse("list-stories-page-1", 429),
            $this->mockResponse("list-stories-page-1", 500),
            $this->mockResponse("list-stories-page-1", 200, [
                "Total" => 6,
                "per-page" => 2,
                "page" => 1,
            ]),
            $this->mockResponse("list-stories-page-1", 500),
            $this->mockResponse("list-stories-page-2", 429),
            $this->mockResponse("list-stories-page-2", 429),
            $this->mockResponse("list-stories-page-2", 200, [
                "total" => 6,
                "per-page" => 2,
                "page" => 2,
            ]),
            $this->mockResponse("list-stories-page-3", 200, [
                "total" => 6,
                "per-page" => 2,
                "page" => 3,
            ]),
        ];

        $client = new RetryableHttpClient(
            new MockHttpClient($responses),
            new GenericRetryStrategy([429], delayMs: 1000),
            maxRetries: 2,
        );

        $mapiClient = ManagementApiClient::initTest($client);
        $storyApi = new StoryApi($mapiClient, "222");

        $this->expectException(ServerException::class);
        $this->expectExceptionMessage("HTTP 500 returned");

        // First call consumes 429 → 500 → retry path setup
        $storyApi->page(
            queryFilters: new QueryFilters()->add(
                new Filter("headline", "like", "Development"),
            ),
        );

        $this->expectException(ServerException::class);
        $this->expectExceptionMessage("HTTP 500 returned");

        $storyApi->page(
            queryFilters: new QueryFilters()->add(
                new Filter("headline", "like", "Development"),
            ),
        );

        // Subsequent successful call
        $storyResponse = $storyApi->page(
            queryFilters: new QueryFilters()->add(
                new Filter("headline", "like", "Development"),
            ),
        );

        $this->assertSame("my-first-post", $storyResponse->data()->get("0.slug"));
        $this->assertSame(6, $storyResponse->total());
        $this->assertSame(2, $storyResponse->perPage());
    }
}
