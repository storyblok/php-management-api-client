<?php

declare(strict_types=1);

use Storyblok\ManagementApi\Data\WorkflowData;
use Storyblok\ManagementApi\Endpoints\WorkflowApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\MockHttpClient;

test('Testing list of workflows', function (): void {
    $responses = [
        \mockResponse("list-workflows", 200),
        \mockResponse("list-workflows", 200),
        //\mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $workflowApi = new WorkflowApi($mapiClient, "222");

    $storyblokResponse = $workflowApi->list(
        $contentType = "article"
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*content_type=article.*$/');

    $storyblokResponse = $workflowApi->list(
        $contentType = [ "article", "category" ]
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*content_type=article%2Ccategory.*$/');

});

test('Testing one workflow', function (): void {
    $responses = [
        \mockResponse("one-workflow", 200),
        \mockResponse("list-workflows", 200),
        //\mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $workflowApi = new WorkflowApi($mapiClient, "222");

    $storyblokResponse = $workflowApi->get(
        "15268"
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*workflow.*$/');
    $data = $storyblokResponse->data();
    expect($data->getString("id"))->toBe("15268");
    expect($data->id())->toBe("15268");
    expect($data->name())->toBe("author workflow");
    expect($data->isDefault())->toBeFalse();
    expect($data->contentTypes())->toBe(["author"]);

});

test('Testing deleting workflow', function (): void {
    $responses = [
        \mockResponse("one-workflow", 200),
        \mockResponse("list-workflows", 200),
        //\mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $workflowApi = new WorkflowApi($mapiClient, "222");

    $storyblokResponse = $workflowApi->delete(
        "15268"
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*workflow.*$/');
    $data = $storyblokResponse->data();
    expect($data->getString("id"))->toBe("15268");
    expect($data->id())->toBe("15268");
    expect($data->name())->toBe("author workflow");
    expect($data->isDefault())->toBeFalse();
    expect($data->contentTypes())->toBe(["author"]);

});

test('Testing creating workflow', function (): void {
    $responses = [
        \mockResponse("one-workflow", 200),
        \mockResponse("list-workflows", 200),
        //\mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $workflowApi = new WorkflowApi($mapiClient, "222");

    $workflowData = new WorkflowData();
    $workflowData->setName("Name");

    $storyblokResponse = $workflowApi->create($workflowData);
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*workflow.*$/');
    expect($storyblokResponse->isOk())->toBeTrue();

});

test('Testing updating workflow', function (): void {
    $responses = [
        \mockResponse("one-workflow", 200),
        \mockResponse("one-workflow", 200),
        //\mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $workflowApi = new WorkflowApi($mapiClient, "222");

    $storyblokResponse = $workflowApi->get("15268");
    $workflowData = $storyblokResponse->data();
    $workflowData->setName("Name");

    $storyblokResponse = $workflowApi->update("15268", $workflowData);

    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*workflow.*$/');
    $data = $storyblokResponse->data();
    expect($data->getString("id"))->toBe("15268");
    expect($data->id())->toBe("15268");
    expect($data->name())->toBe("author workflow");
    expect($data->isDefault())->toBeFalse();
    expect($data->contentTypes())->toBe(["author"]);

});
