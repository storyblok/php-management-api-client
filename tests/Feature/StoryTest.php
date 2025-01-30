<?php

declare(strict_types=1);

use Storyblok\ManagementApi\ManagementApiClient;
use Storyblok\ManagementApi\Data\StoryData;
use Storyblok\ManagementApi\QueryParameters\PaginationParams;
use Storyblok\ManagementApi\QueryParameters\StoriesParams;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\MockHttpClient;
use Psr\Log\NullLogger;

test('Testing One Story, StoryData', function (): void {
    $responses = [
        \mockResponse("one-story", 200),
        \mockResponse("empty-story", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $storyApi = $mapiClient->storyApi("222");

    $storyblokResponse = $storyApi->get("111");
    /** @var \Storyblok\Mapi\Data\StoryData $storyblokData */
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

test('StoryApi works with custom logger', function (): void {
    // Create a mock logger that tracks log calls
    $mockLogger = new class extends NullLogger {
        public array $logs = [];

        public function log($level, string|\Stringable $message, array $context = []): void
        {
            $this->logs[] = [
                'level' => $level,
                'message' => $message,
                'context' => $context
            ];
        }

        public function error(string|\Stringable $message, array $context = []): void
        {
            $this->log('error', $message, $context);
        }
    };

    // Create a response that will trigger error logging
    $responses = [
        new MockResponse([], [
            'http_code' => 500,
            'response_headers' => ['Content-Type: application/json'],
        ])
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $storyApi = $mapiClient->storyApi('222', $mockLogger);

    // Use the all() method which we know triggers logging
    try {
        iterator_to_array($storyApi->all());
    } catch (\Exception) {
        // Expected exception
    }

    // Verify that logs were recorded
    expect($mockLogger->logs)->not->toBeEmpty()
        ->and($mockLogger->logs[0]['level'])->toBe('error')
        ->and($mockLogger->logs[0]['message'])->toBe('Error fetching stories');
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


    $storyblokResponse = $storyApi->all(
        params: new StoriesParams(
            withTag: "aaa",
            search: "something"
        )
    );
    expect($storyblokResponse)->toBeInstanceOf(Generator::class);
    foreach ($storyblokResponse as $story) {
        expect($story->name())->toBe("My third post");
    }


});
