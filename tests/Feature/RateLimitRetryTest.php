<?php

use Storyblok\ManagementApi\Endpoints\StoryApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Storyblok\ManagementApi\QueryParameters\Filters\Filter;
use Storyblok\ManagementApi\QueryParameters\Filters\QueryFilters;
use Storyblok\ManagementApi\RateLimitRetryService;
use Storyblok\ManagementApi\Response\StoriesResponse;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Symfony\Component\HttpClient\RetryableHttpClient;

test("Testing retry mechanism with list stories", function (): void {
    $responses = [
        \mockResponse("list-stories-page-1", 429),
        \mockResponse("list-stories-page-1", 429),
        \mockResponse("list-stories-page-1", 200, [
            "total" => 6,
            "per-page" => 2,
            "page" => 1,
        ]),
        \mockResponse("list-stories-page-2", 429),
        \mockResponse("list-stories-page-2", 429),
        \mockResponse("list-stories-page-2", 200, [
            "total" => 6,
            "per-page" => 2,
            "page" => 2,
        ]),
        \mockResponse("list-stories-page-3", 200, [
            "total" => 6,
            "per-page" => 2,
            "page" => 3,
        ]),

        //\mockResponse("empty-asset", 404),
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
    expect($storyResponse->data()->get("0.slug"))->toBe("my-first-post");
    $storyResponse = $storyApi->page(
        queryFilters: new QueryFilters()->add(
            new Filter("headline", "like", "Development"),
        ),
    );
    expect($storyResponse->data()->get("0.slug"))->toBe("my-third-post");
});

test("Testing retry mechanism with list stories 2", function (): void {
    $responses = [
        \mockResponse("list-stories-page-1", 429),
        \mockResponse("list-stories-page-1", 429),
        \mockResponse("list-stories-page-1", 200, [
            "Total" => 6,
            "per-page" => 2,
            "page" => 1,
        ]),
        \mockResponse("list-stories-page-2", 429),
        \mockResponse("list-stories-page-2", 429),
        \mockResponse("list-stories-page-2", 200, [
            "total" => 6,
            "per-page" => 2,
            "page" => 2,
        ]),
        \mockResponse("list-stories-page-3", 200, [
            "total" => 6,
            "per-page" => 2,
            "page" => 3,
        ]),

        //\mockResponse("empty-asset", 404),
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
    expect($storyResponse->data()->get("0.slug"))->toBe("my-first-post");
    expect($storyResponse->total())->toBe(6);
    expect($storyResponse->data()->count())->toBe(2);

    $storyResponse = $storyApi->page(
        queryFilters: new QueryFilters()->add(
            new Filter("headline", "like", "Development"),
        ),
    );
    expect($storyResponse->data()->get("0.slug"))->toBe("my-third-post");
});

test(
    "Testing retry mechanism with list stories with 500 exception",
    function (): void {
        $responses = [
            \mockResponse("list-stories-page-1", 429),
            \mockResponse("list-stories-page-1", 500),
            \mockResponse("list-stories-page-1", 200, [
                "Total" => 6,
                "per-page" => 2,
                "page" => 1,
            ]),
            \mockResponse("list-stories-page-2", 429),
            \mockResponse("list-stories-page-2", 429),
            \mockResponse("list-stories-page-2", 200, [
                "total" => 6,
                "per-page" => 2,
                "page" => 2,
            ]),
            \mockResponse("list-stories-page-3", 200, [
                "total" => 6,
                "per-page" => 2,
                "page" => 3,
            ]),

            //\mockResponse("empty-asset", 404),
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

        expect(
            fn(): StoriesResponse => $storyApi->page(
                queryFilters: new QueryFilters()->add(
                    new Filter("headline", "like", "Development"),
                ),
            ),
        )->toThrow(
            \Symfony\Component\HttpClient\Exception\ServerException::class,
            "HTTP 500 returned",
        );

        $storyResponse = $storyApi->page(
            queryFilters: new QueryFilters()->add(
                new Filter("headline", "like", "Development"),
            ),
        );
        expect($storyResponse->data()->get("0.slug"))->toBe("my-first-post");
        expect($storyResponse->total())->toBe(6);
        expect($storyResponse->perPage())->toBe(2);
    },
);
