<?php

declare(strict_types=1);

use Storyblok\ManagementApi\ManagementApiClient;
use Storyblok\ManagementApi\Data\StoryData;
use Storyblok\ManagementApi\QueryParameters\Filters\Filter;
use Storyblok\ManagementApi\QueryParameters\Filters\QueryFilters;
use Storyblok\ManagementApi\QueryParameters\PaginationParams;
use Storyblok\ManagementApi\QueryParameters\StoriesParams;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\MockHttpClient;




test('Testing One Story, StoryData', function (): void {
    $responses = [
        \mockResponse("one-story", 200),
        \mockResponse("empty-story", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $storyApi = $mapiClient->storyApi("222");

    $storyblokResponse = $storyApi->get("111");
    /** @var \Storyblok\ManagementApi\Data\StoryData $storyblokData */
    $storyblokData =  $storyblokResponse->data();
    expect($storyblokData->get("name"))
        ->toBe("My third post")
        ->and($storyblokData->name())->toBe("My third post")
        ->and($storyblokData->createdAt())->toBe("2024-02-08")
        ->and($storyblokResponse->getResponseStatusCode())->toBe(200);

    $storyblokResponse = $storyApi->get("111notexists");
    expect( $storyblokResponse->getResponseStatusCode())->toBe(404) ;
    expect( $storyblokResponse->asJson())->toBe('["This record could not be found"]');
});

test('Create story encodes data correctly as JSON', function (): void {
    $expectedStoryData = [
        'name' => 'Test Story',
        'slug' => 'test-story',
        'content' => [
            'component' => 'blog-post',
            'title' => 'Test Title'
        ]
    ];

    // Create a callback to verify the request
    $callback = function ($method, $url, array $options) use ($expectedStoryData): void {
        expect($method)->toBe('POST');
        expect($url)->toContain('/stories');

        // Decode the request body and verify it matches expected structure
        $requestBody = json_decode((string) $options['body'], true);
        expect($requestBody)->toBeArray();
        expect($requestBody)->toHaveKey('story');
        expect($requestBody['story'])->toEqual($expectedStoryData);
    };

    // Create mock response
    $response = new MockResponse(json_encode([
        'story' => $expectedStoryData
    ]), [
        'http_code' => 201,
        'response_headers' => ['Content-Type: application/json'],
    ]);

    // Create mock client with response and callback
    $client = new MockHttpClient([$response], 'https://api.storyblok.com');
    $mapiClient = ManagementApiClient::initTest($client);
    $storyApi = $mapiClient->storyApi('222');

    // Create story data
    $storyData = StoryData::make($expectedStoryData);

    // Make the request
    $response = $storyApi->create($storyData);

    // Verify response
    expect($response->isOk())->toBeTrue();
    expect($response->getResponseStatusCode())->toBe(201);

    /** @var StoryData $responseData */
    $responseData = $response->data();
    expect($responseData->name())->toBe('Test Story');
    expect($responseData->slug())->toBe('test-story');
});


test('Testing list of stories, Params', function (): void {
    $responses = [
        \mockResponse("list-stories", 200, ["total"=>2, "per-page" => 25 ]),
        \mockResponse("list-stories", 200, ["total"=>2, "per-page" => 25 ]),
        \mockResponse("list-stories", 200, ["total"=>2, "per-page" => 25 ]),
        \mockResponse("list-stories", 200, ["total"=>2, "per-page" => 25 ]),
        \mockResponse("list-stories", 200, ["total"=>2, "per-page" => 25 ]),
        \mockResponse("list-stories", 200, ["total"=>2, "per-page" => 25 ]),
        //\mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $storyApi = $mapiClient->storyApi("222");

    $storyblokResponse = $storyApi->page(params: new StoriesParams(
        favorite: true
    ));
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*favorite=1.*$/');
    expect($string)->toMatch('/.*page=1&per_page=25.*$/');

    $storyblokResponse = $storyApi->page(
        params: new StoriesParams(
            favorite: true
        ),page: new PaginationParams(5, 30)
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*favorite=1.*$/');
    expect($string)->toMatch('/.*page=5&per_page=30.*$/');

    $storyblokResponse = $storyApi->page(
        params: new StoriesParams(
            withTag: "aaa",
            search: "something"
        ),page: new PaginationParams(5, 30)
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*search=something.*$/');
    expect($string)->toMatch('/.*with_tag=aaa.*$/');
    expect($string)->toMatch('/.*page=5&per_page=30.*$/');


    $storyblokResponse = $storyApi->page(
        params: new StoriesParams(
            withTag: "aaa",
            search: "something"
        ),
        queryFilters: (new QueryFilters())->add(
            new Filter(
                "headline",
                "like",
                "something"
            )
        ),

        page: new PaginationParams(5, 30)
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*search=something.*$/');
    expect($string)->toMatch('/.*with_tag=aaa.*$/');
    expect($string)->toMatch('/.*page=5&per_page=30.*$/');
    expect($string)->toMatch('/.*filter_query\[headline\]\[like\]=something.*$/');

    $storyblokResponse = $storyApi->page(
        params: new StoriesParams(
            withTag: "aaa",
            search: "something"
        ),
        queryFilters: (new QueryFilters())->add(
            new Filter(
                "headline",
                "like",
                "something"
            ))->add(
            new Filter(
                "subheadline",
                "like",
                "somethingelse"
            ),

        ),

        page: new PaginationParams(5, 30)
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*search=something.*$/');
    expect($string)->toMatch('/.*with_tag=aaa.*$/');
    expect($string)->toMatch('/.*page=5&per_page=30.*$/');
    expect($string)->toMatch('/.*filter_query\[headline\]\[like\]=something.*$/');
    expect($string)->toMatch('/.*filter_query\[subheadline\]\[like\]=somethingelse.*$/');


});

