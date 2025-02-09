<?php

declare(strict_types=1);

use Storyblok\ManagementApi\Data\WorkflowData;
use Storyblok\ManagementApi\Endpoints\TagApi;
use Storyblok\ManagementApi\Endpoints\WorkflowApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\MockHttpClient;

test('Testing list of tags', function (): void {
    $responses = [
        \mockResponse("list-tags", 200),
        \mockResponse("list-tags", 200),
        //\mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $tagApi = new TagApi($mapiClient, "222");

    $storyblokResponse = $tagApi->page();
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*tags.*$/');

});

test('Testing one tag', function (): void {
    $responses = [
        \mockResponse("one-tag", 200),
        //\mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $tagApi = new TagApi($mapiClient, "222");

    $storyblokResponse = $tagApi->get(
        "56932"
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*tags.*$/');
    $data = $storyblokResponse->data();
    expect($data->getString("id"))->toBe("56932");
    expect($data->id())->toBe("56932");
    expect($data->name())->toBe("some");

});

test('Testing deleting tag', function (): void {
    $responses = [
        \mockResponse("one-tag", 200),
        //\mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $tagApi = new TagApi($mapiClient, "222");

    $storyblokResponse = $tagApi->delete(
        "15268"
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*tags.*$/');
    $data = $storyblokResponse->data();
    expect($data->getString("id"))->toBe("56932");
    expect($data->id())->toBe("56932");
    expect($data->name())->toBe("some");

});

test('Testing creating tag', function (): void {
    $responses = [
        \mockResponse("one-tag", 200),
        //\mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $tagApi = new TagApi($mapiClient, "222");

    $storyblokResponse = $tagApi->create("name");
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*tags.*$/');
    expect($storyblokResponse->isOk())->toBeTrue();

});

test('Testing updating tag', function (): void {
    $responses = [
        \mockResponse("one-tag", 200),
        \mockResponse("one-tag", 200),
        //\mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $tagApi = new TagApi($mapiClient, "222");

    $storyblokResponse = $tagApi->update("56932", "some");

    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*tags.*$/');
    $data = $storyblokResponse->data();
    expect($data->getString("id"))->toBe("56932");
    expect($data->id())->toBe("56932");
    expect($data->name())->toBe("some");

});
