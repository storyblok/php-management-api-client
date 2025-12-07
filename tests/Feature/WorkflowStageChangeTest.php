<?php

declare(strict_types=1);

use Storyblok\ManagementApi\Data\WorkflowStageChange;
use Storyblok\ManagementApi\Data\WorkflowStageData;
use Storyblok\ManagementApi\Endpoints\WorkflowStageApi;
use Storyblok\ManagementApi\Endpoints\WorkflowStageChangeApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\MockHttpClient;

test("Testing list of workflow stage changes", function (): void {
    $responses = [
        \mockResponse("list-workflow-stage-changes", 200, []),
        \mockResponse("list-workflow-stage-changes", 200, []),
        //\mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $workflowStageChangeApi = new WorkflowStageChangeApi($mapiClient, 123);

    $storyblokResponse = $workflowStageChangeApi->page(withStory: "12345");
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*with_story=12345.*$/');
    $workflowStageChanges = $storyblokResponse->data();

    expect($workflowStageChanges->howManyWorkflowStageChanges())->toBe(2);
});

test("Testing creating Workflow Stage Change", function (): void {
    $responses = [
        \mockResponse("one-workflow-stage-change", 200),
        //\mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $changeApi = new WorkflowStageChangeApi($mapiClient, "222");

    $workflowStageChange = new WorkflowStageChange();
    $workflowStageChange->setStoryAndStage(123, 321);

    $storyblokResponse = $changeApi->create($workflowStageChange);
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*workflow_stage_changes.*$/');
    expect($storyblokResponse->isOk())->toBeTrue();
});
