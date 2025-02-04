<?php

declare(strict_types=1);


use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\MockHttpClient;

test('Testing list of workflow stages', function (): void {
    $responses = [
        \mockResponse("list-workflow-stages", 200, []),
        \mockResponse("list-workflow-stages", 200,[]),
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
