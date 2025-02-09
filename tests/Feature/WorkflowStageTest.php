<?php

declare(strict_types=1);

use Storyblok\ManagementApi\Data\WorkflowStageData;
use Storyblok\ManagementApi\Endpoints\WorkflowStageApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\MockHttpClient;

test('Testing list of workflow stages', function (): void {
    $responses = [
        \mockResponse("list-workflow-stages", 200, []),
        \mockResponse("list-workflow-stages", 200, []),
        //\mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $workflowStageApi = $mapiClient->workflowStageApi("222");

    $storyblokResponse = $workflowStageApi->list(
        inWorkflowId: "12345"
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*in_workflow=12345.*$/');

    $storyblokResponse = $workflowStageApi->list(
        byIds: [ "12345", "54321" ]
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*by_ids=12345%2C54321.*$/');

});

test('Testing one workflow stage', function (): void {
    $responses = [
        \mockResponse("one-workflow-stage", 200, []),
        \mockResponse("one-workflow-stage", 200, []),
        //\mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $workflowStageApi = new WorkflowStageApi($mapiClient, "222");

    $storyblokResponse = $workflowStageApi->get(
        "653554"
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*workflow_stages\/653554.*$/');
    $data = $storyblokResponse->data();
    expect($data->id())->toBe("653554");

});

test('Testing delete workflow stage', function (): void {
    $responses = [
        \mockResponse("one-workflow-stage", 200, []),
        \mockResponse("one-workflow-stage", 200, []),
        //\mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $workflowStageApi = new WorkflowStageApi($mapiClient, "222");

    $storyblokResponse = $workflowStageApi->delete(
        "653554"
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*workflow_stages\/653554.*$/');
    $data = $storyblokResponse->data();
    expect($data->id())->toBe("653554");

});

test('Testing update workflow stage', function (): void {
    $responses = [
        \mockResponse("one-workflow-stage", 200, []),
        \mockResponse("one-workflow-stage", 200, []),
        //\mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $workflowStageApi = new WorkflowStageApi($mapiClient, "222");

    $storyblokResponse = $workflowStageApi->get(
        "653554"
    );
    $data = $storyblokResponse->data();
    $data->setName("Test");
    $data->setWorkflowid("12345");

    expect($data->name())->toBe("Test");
    $storyblokResponse = $workflowStageApi->update(
        "653554",
        $data
    );
    $data = $storyblokResponse->data();
    expect($data->id())->toBe("653554");

});

test('Testing create workflow stage', function (): void {
    $responses = [
        \mockResponse("one-workflow-stage", 200, []),
        \mockResponse("one-workflow-stage", 200, []),
        //\mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $workflowStageApi = new WorkflowStageApi($mapiClient, "222");

    $data = WorkflowStageData::make([]);
    $data->setName("Test");
    $data->setWorkflowid("12345");

    expect($data->name())->toBe("Test");
    $storyblokResponse = $workflowStageApi->create(
        $data
    );
    $data = $storyblokResponse->data();
    expect($data->id())->toBe("653554");

});
