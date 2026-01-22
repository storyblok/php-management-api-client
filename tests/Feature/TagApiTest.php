<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Storyblok\ManagementApi\Endpoints\TagApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\MockHttpClient;

final class TagApiTest extends TestCase
{
    public function testListOfTags(): void
    {
        $responses = [
            $this->mockResponse('list-tags', 200),
            $this->mockResponse('list-tags', 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $tagApi = new TagApi($mapiClient, '222');

        $storyblokResponse = $tagApi->page();
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression('/.*tags.*$/', $url);
    }

    public function testOneTag(): void
    {
        $responses = [
            $this->mockResponse('one-tag', 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $tagApi = new TagApi($mapiClient, '222');

        $storyblokResponse = $tagApi->get('56932');
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression('/.*tags.*$/', $url);

        $data = $storyblokResponse->data();
        $this->assertSame('56932', $data->getString('id'));
        $this->assertSame('56932', $data->id());
        $this->assertSame('some', $data->name());
    }

    public function testDeletingTag(): void
    {
        $responses = [
            $this->mockResponse('one-tag', 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $tagApi = new TagApi($mapiClient, '222');

        $storyblokResponse = $tagApi->delete('15268');
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression('/.*tags.*$/', $url);

        $data = $storyblokResponse->data();
        $this->assertSame('56932', $data->getString('id'));
        $this->assertSame('56932', $data->id());
        $this->assertSame('some', $data->name());
    }

    public function testCreatingTag(): void
    {
        $responses = [
            $this->mockResponse('one-tag', 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $tagApi = new TagApi($mapiClient, '222');

        $storyblokResponse = $tagApi->create('name');
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression('/.*tags.*$/', $url);
        $this->assertTrue($storyblokResponse->isOk());
    }

    public function testUpdatingTag(): void
    {
        $responses = [
            $this->mockResponse('one-tag', 200),
            $this->mockResponse('one-tag', 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $tagApi = new TagApi($mapiClient, '222');

        $storyblokResponse = $tagApi->update('56932', 'some');

        $url = $storyblokResponse->getLastCalledUrl();
        $this->assertMatchesRegularExpression('/.*tags.*$/', $url);

        $data = $storyblokResponse->data();
        $this->assertSame('56932', $data->getString('id'));
        $this->assertSame('56932', $data->id());
        $this->assertSame('some', $data->name());
    }
}
