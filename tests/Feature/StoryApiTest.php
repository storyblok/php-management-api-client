<?php

declare(strict_types=1);

namespace Tests\Feature;

use Exception;
use InvalidArgumentException;
use Tests\TestCase;
use Storyblok\ManagementApi\Data\Story;
use Storyblok\ManagementApi\Data\StoryComponent;
use Storyblok\ManagementApi\Endpoints\StoryApi;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;
use Storyblok\ManagementApi\ManagementApiClient;
use Storyblok\ManagementApi\QueryParameters\Filters\Filter;
use Storyblok\ManagementApi\QueryParameters\Filters\QueryFilters;
use Storyblok\ManagementApi\QueryParameters\PaginationParams;
use Storyblok\ManagementApi\QueryParameters\StoriesParams;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class StoryApiTest extends TestCase
{
    public function testOneStoryStoryData(): void
    {
        $responses = [
            $this->mockResponse("one-story", 200),
            $this->mockResponse("empty-story", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $storyApi = new StoryApi($mapiClient, "222");

        $storyblokResponse = $storyApi->get("111");
        /** @var Story $storyblokData */
        $storyblokData = $storyblokResponse->data();

        $this->assertSame("My third post", $storyblokData->get("name"));
        $this->assertSame("My third post", $storyblokData->name());
        $this->assertSame("2024-02-08", $storyblokData->createdAt());
        $this->assertSame(["tag1", "tag2"], $storyblokData->tagListAsArray());
        $this->assertSame("tag1, tag2", $storyblokData->tagListAsString());
        $this->assertSame(200, $storyblokResponse->getResponseStatusCode());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'HTTP 404 returned for "https://example.com/v1/spaces/222/stories/111notexists',
        );

        $storyApi->get("111notexists");
    }

    public function testCreatingStoryWithError(): void
    {
        $responses = [$this->mockResponse("empty-story", 401)];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $storyApi = new StoryApi($mapiClient, "222");

        $storyblokResponse = $storyApi->create(
            new Story("aa", "aa", new StoryComponent("aa")),
        );

        $this->assertSame(401, $storyblokResponse->getResponseStatusCode());

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'HTTP 401 returned for "https://example.com/v1/spaces/222/stories',
        );

        $storyblokResponse->data();
    }

    public function testCreateStoryEncodesDataCorrectlyAsJson(): void
    {
        $expectedStoryData = [
            "name" => "Test Story",
            "slug" => "test-story",
            "content" => [
                "component" => "blog-post",
                "title" => "Test Title",
            ],
        ];

        // Create mock response
        $response = new MockResponse(
            json_encode([
                "story" => $expectedStoryData,
            ], JSON_THROW_ON_ERROR),
            [
                "http_code" => 201,
                "response_headers" => ["Content-Type: application/json"],
            ],
        );

        // Create mock client with response
        $client = new MockHttpClient([$response], "https://api.storyblok.com");
        $mapiClient = ManagementApiClient::initTest($client);
        $storyApi = new StoryApi($mapiClient, "222");

        // Create story data
        $storyData = Story::make($expectedStoryData);

        // Make the request
        $response = $storyApi->create($storyData);

        // Verify response
        $this->assertTrue($response->isOk());
        $this->assertSame(201, $response->getResponseStatusCode());

        /** @var Story $responseData */
        $responseData = $response->data();
        $this->assertSame("Test Story", $responseData->name());
        $this->assertSame("test-story", $responseData->slug());
    }

    public function testListOfStoriesParams(): void
    {
        $responses = [
            $this->mockResponse("list-stories", 200, [
                "total" => 2,
                "per-page" => 25,
            ]),
            $this->mockResponse("list-stories", 200, [
                "total" => 2,
                "per-page" => 25,
            ]),
            $this->mockResponse("list-stories", 200, [
                "total" => 2,
                "per-page" => 25,
            ]),
            $this->mockResponse("list-stories", 200, [
                "total" => 2,
                "per-page" => 25,
            ]),
            $this->mockResponse("list-stories", 200, [
                "total" => 2,
                "per-page" => 25,
            ]),
            $this->mockResponse("list-stories", 200, [
                "total" => 2,
                "per-page" => 25,
            ]),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $storyApi = new StoryApi($mapiClient, "222");

        $storyblokResponse = $storyApi->page(
            params: new StoriesParams(favorite: true),
        );
        $url = $storyblokResponse->getLastCalledUrl();
        $this->assertMatchesRegularExpression('/.*favorite=1.*$/', $url);
        $this->assertMatchesRegularExpression(
            '/.*page=1&per_page=25.*$/',
            $url,
        );

        $storyblokResponse = $storyApi->page(
            params: new StoriesParams(favorite: true),
            page: new PaginationParams(5, 30),
        );
        $url = $storyblokResponse->getLastCalledUrl();
        $this->assertMatchesRegularExpression('/.*favorite=1.*$/', $url);
        $this->assertMatchesRegularExpression(
            '/.*page=5&per_page=30.*$/',
            $url,
        );

        $storyblokResponse = $storyApi->page(
            params: new StoriesParams(withTag: "aaa", search: "something"),
            page: new PaginationParams(5, 30),
        );
        $url = $storyblokResponse->getLastCalledUrl();
        $this->assertMatchesRegularExpression('/.*search=something.*$/', $url);
        $this->assertMatchesRegularExpression('/.*with_tag=aaa.*$/', $url);
        $this->assertMatchesRegularExpression(
            '/.*page=5&per_page=30.*$/',
            $url,
        );

        $storyblokResponse = $storyApi->page(
            params: new StoriesParams(withTag: "aaa", search: "something"),
            queryFilters: new QueryFilters()->add(
                new Filter("headline", "like", "something"),
            ),
            page: new PaginationParams(5, 30),
        );
        $url = $storyblokResponse->getLastCalledUrl();
        $this->assertMatchesRegularExpression('/.*search=something.*$/', $url);
        $this->assertMatchesRegularExpression('/.*with_tag=aaa.*$/', $url);
        $this->assertMatchesRegularExpression(
            '/.*page=5&per_page=30.*$/',
            $url,
        );
        $this->assertMatchesRegularExpression(
            '/.*filter_query\[headline\]\[like\]=something.*$/',
            $url,
        );

        $storyblokResponse = $storyApi->page(
            params: new StoriesParams(withTag: "aaa", search: "something"),
            queryFilters: new QueryFilters()
                ->add(new Filter("headline", "like", "something"))
                ->add(new Filter("subheadline", "like", "somethingelse")),
            page: new PaginationParams(5, 30),
        );
        $url = $storyblokResponse->getLastCalledUrl();
        $this->assertMatchesRegularExpression('/.*search=something.*$/', $url);
        $this->assertMatchesRegularExpression('/.*with_tag=aaa.*$/', $url);
        $this->assertMatchesRegularExpression(
            '/.*page=5&per_page=30.*$/',
            $url,
        );
        $this->assertMatchesRegularExpression(
            '/.*filter_query\[headline\]\[like\]=something.*$/',
            $url,
        );
        $this->assertMatchesRegularExpression(
            '/.*filter_query\[subheadline\]\[like\]=somethingelse.*$/',
            $url,
        );
    }

    public function testSettingStoryMethodsStoryData(): void
    {
        $responses = [
            $this->mockResponse("one-story", 200),
            $this->mockResponse("one-story", 200),
            $this->mockResponse("empty-story", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $storyApi = new StoryApi($mapiClient, "222");

        $storyblokResponse = $storyApi->get("111");

        $storyblokData = $storyblokResponse->data();
        $this->assertSame("My third post", $storyblokData->get("name"));
        $this->assertSame("My third post", $storyblokData->name());
        $this->assertSame("2024-02-08", $storyblokData->createdAt());
        $this->assertSame(200, $storyblokResponse->getResponseStatusCode());

        $storyblokData->setName("AAA");
        $storyblokData->setFolderId("123456");

        $this->assertSame("AAA", $storyblokData->get("name"));
        $this->assertSame("AAA", $storyblokData->name());
        $this->assertSame("2024-02-08", $storyblokData->createdAt());
        $this->assertSame(123456, $storyblokData->getInt("parent_id"));
        $this->assertSame(123456, $storyblokData->folderId());

        $storyblokResponse = $storyApi->update("111", $storyblokData);
        $storyblokData = $storyblokResponse->data();
        $this->assertSame("440448565", $storyblokData->id());
    }

    public function testEditStoryStoryData(): void
    {
        $responses = [
            $this->mockResponse("one-story", 200),
            $this->mockResponse("one-story", 200),
            $this->mockResponse("empty-story", 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $storyApi = new StoryApi($mapiClient, "222");

        $storyblokResponse = $storyApi->get("111");

        $storyblokData = $storyblokResponse->data();
        $this->assertSame("My third post", $storyblokData->get("name"));
        $this->assertSame("My third post", $storyblokData->name());
        $this->assertSame("2024-02-08", $storyblokData->createdAt());
        $this->assertSame(200, $storyblokResponse->getResponseStatusCode());

        $storyblokData->setName("AAA");
        $storyblokResponse = $storyApi->update("111", $storyblokData);
        $storyblokData = $storyblokResponse->data();
        $this->assertSame("440448565", $storyblokData->id());

        // now the third interaction will return an empty payload
        $this->expectException(StoryblokFormatException::class);
        $this->expectExceptionMessage("Expected 'story' in the response.");
        $storyblokResponse = $storyApi->update("111", $storyblokData);
        $storyblokResponse->data();
    }

    public function testPublishingStoryStoryData(): void
    {
        $responses = [
            $this->mockResponse("one-story", 200),
            $this->mockResponse("one-story", 200),
            $this->mockResponse("empty-story", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $storyApi = new StoryApi($mapiClient, "222");

        $storyblokResponse = $storyApi->publish("111", "1112", "en");

        $storyblokData = $storyblokResponse->data();
        $this->assertSame("My third post", $storyblokData->get("name"));
        $this->assertSame("My third post", $storyblokData->name());
        $this->assertSame("2024-02-08", $storyblokData->createdAt());
        $this->assertSame(200, $storyblokResponse->getResponseStatusCode());
    }

    public function testUnpublishingStoryStoryData(): void
    {
        $responses = [
            $this->mockResponse("one-story", 200),
            $this->mockResponse("one-story", 200),
            $this->mockResponse("empty-story", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $storyApi = new StoryApi($mapiClient, "222");

        $storyblokResponse = $storyApi->unpublish("111", "en");

        $storyblokData = $storyblokResponse->data();
        $this->assertSame("My third post", $storyblokData->get("name"));
        $this->assertSame("My third post", $storyblokData->name());
        $this->assertSame("2024-02-08", $storyblokData->createdAt());
        $this->assertSame(200, $storyblokResponse->getResponseStatusCode());
    }

    public function testValidationInputStoryData(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $responses = [
            $this->mockResponse("list-stories", 200),
            $this->mockResponse("list-stories", 200),
            $this->mockResponse("empty-story", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $storyApi = new StoryApi($mapiClient, "222");

        $storyApi->page(page: new PaginationParams(-1, -20));
    }

    public function testValidationInput2StoryData(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $responses = [
            $this->mockResponse("list-stories", 200),
            $this->mockResponse("list-stories", 200),
            $this->mockResponse("empty-story", 404),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $storyApi = new StoryApi($mapiClient, "222");

        $storyApi->page(page: new PaginationParams(1, -20));
    }

    public function testGetUuids(): void
    {
        //$this->expectException(InvalidArgumentException::class);

        $responses = [$this->mockResponse("list-stories", 200)];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $storyApi = new StoryApi($mapiClient, "222");

        $stories = $storyApi->page();
        $array = $stories->data()->getUuids();
        $this->assertCount(2, $array);
        $this->assertSame("e656e146-f4ed-44a2-8017-013e5a9d9395", $array[1]);
        $this->assertSame("e656e146-f4ed-44a2-8017-013e5a9d9396", $array[0]);
    }
}
