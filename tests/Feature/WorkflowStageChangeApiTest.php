<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Storyblok\ManagementApi\Data\WorkflowStageChange;
use Storyblok\ManagementApi\Endpoints\WorkflowStageChangeApi;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\MockHttpClient;

final class WorkflowStageChangeApiTest extends TestCase
{
    public function testListOfWorkflowStageChanges(): void
    {
        $responses = [
            $this->mockResponse("list-workflow-stage-changes", 200, []),
            $this->mockResponse("list-workflow-stage-changes", 200, []),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $workflowStageChangeApi = new WorkflowStageChangeApi($mapiClient, 123);

        $storyblokResponse = $workflowStageChangeApi->page(withStory: "12345");
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression('/.*with_story=12345.*$/', $url);

        $workflowStageChanges = $storyblokResponse->data();
        $this->assertSame(
            2,
            $workflowStageChanges->howManyWorkflowStageChanges(),
        );
    }

    public function testCreatingWorkflowStageChange(): void
    {
        $responses = [$this->mockResponse("one-workflow-stage-change", 200)];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $changeApi = new WorkflowStageChangeApi($mapiClient, "222");

        $workflowStageChange = new WorkflowStageChange();
        $workflowStageChange->setStoryAndStage(123, 321);

        $storyblokResponse = $changeApi->create($workflowStageChange);
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression(
            '/.*workflow_stage_changes.*$/',
            $url,
        );
        $this->assertTrue($storyblokResponse->isOk());
        $storyblokResponse->data();
    }

    public function testEmptyPayload(): void
    {
        $responses = [$this->mockResponse("empty", 200, [])];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $workflowStageChangeApi = new WorkflowStageChangeApi($mapiClient, 123);

        $this->expectException(StoryblokFormatException::class);
        $this->expectExceptionMessage(
            "Expected 'workflow_stage_changes' in the response.",
        );
        $workflowStageChangeApi
            ->page(withStory: "12345")
            ->data();
    }

    public function testOneEmptyPayload(): void
    {
        $responses = [$this->mockResponse("empty", 200, [])];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $workflowStageChangeApi = new WorkflowStageChangeApi($mapiClient, 123);

        $this->expectException(StoryblokFormatException::class);
        $this->expectExceptionMessage(
            "Expected 'workflow_stage_change' in the response.",
        );

        $workflowStageChange = new WorkflowStageChange();
        $workflowStageChange->setStoryAndStage(123, 321);

        $storyblokResponse = $workflowStageChangeApi->create(
            $workflowStageChange,
        );
        $storyblokResponse->data();
    }

    public function testErrorMessage(): void
    {
        $responses = [$this->mockResponse("error", 200, [])];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $workflowStageChangeApi = new WorkflowStageChangeApi($mapiClient, 123);

        $this->expectException(StoryblokFormatException::class);
        $this->expectExceptionMessage(
            "Expected 'workflow_stage_change' in the response. Error",
        );

        $workflowStageChange = new WorkflowStageChange();
        $workflowStageChange->setStoryAndStage(123, 321);

        $storyblokResponse = $workflowStageChangeApi->create(
            $workflowStageChange,
        );
        $storyblokResponse->data();
    }
}
