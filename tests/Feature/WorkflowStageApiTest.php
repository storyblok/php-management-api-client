<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Storyblok\ManagementApi\Data\WorkflowStageData;
use Storyblok\ManagementApi\Endpoints\WorkflowStageApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\MockHttpClient;

final class WorkflowStageApiTest extends TestCase
{
    public function testListOfWorkflowStages(): void
    {
        $responses = [
            $this->mockResponse('list-workflow-stages', 200, []),
            $this->mockResponse('list-workflow-stages', 200, []),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $workflowStageApi = $mapiClient->workflowStageApi('222');

        $storyblokResponse = $workflowStageApi->list(inWorkflowId: '12345');
        $url = $storyblokResponse->getLastCalledUrl();
        $this->assertMatchesRegularExpression('/.*in_workflow=12345.*$/', $url);

        $storyblokResponse = $workflowStageApi->list(byIds: ['12345', '54321']);
        $url = $storyblokResponse->getLastCalledUrl();
        $this->assertMatchesRegularExpression('/.*by_ids=12345%2C54321.*$/', $url);
    }

    public function testOneWorkflowStage(): void
    {
        $responses = [
            $this->mockResponse('one-workflow-stage', 200, []),
            $this->mockResponse('one-workflow-stage', 200, []),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $workflowStageApi = new WorkflowStageApi($mapiClient, '222');

        $storyblokResponse = $workflowStageApi->get('653554');
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression('/.*workflow_stages\/653554.*$/', $url);

        $data = $storyblokResponse->data();
        $this->assertSame('653554', $data->id());
    }

    public function testDeleteWorkflowStage(): void
    {
        $responses = [
            $this->mockResponse('one-workflow-stage', 200, []),
            $this->mockResponse('one-workflow-stage', 200, []),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $workflowStageApi = new WorkflowStageApi($mapiClient, '222');

        $storyblokResponse = $workflowStageApi->delete('653554');
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression('/.*workflow_stages\/653554.*$/', $url);

        $data = $storyblokResponse->data();
        $this->assertSame('653554', $data->id());
    }

    public function testUpdateWorkflowStage(): void
    {
        $responses = [
            $this->mockResponse('one-workflow-stage', 200, []),
            $this->mockResponse('one-workflow-stage', 200, []),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $workflowStageApi = new WorkflowStageApi($mapiClient, '222');

        $storyblokResponse = $workflowStageApi->get('653554');
        $data = $storyblokResponse->data();
        $data->setName('Test');
        $data->setWorkflowid('12345');

        $this->assertSame('Test', $data->name());

        $storyblokResponse = $workflowStageApi->update('653554', $data);
        $data = $storyblokResponse->data();

        $this->assertSame('653554', $data->id());
    }

    public function testCreateWorkflowStage(): void
    {
        $responses = [
            $this->mockResponse('one-workflow-stage', 200, []),
            $this->mockResponse('one-workflow-stage', 200, []),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $workflowStageApi = new WorkflowStageApi($mapiClient, '222');

        $data = WorkflowStageData::make([]);
        $data->setName('Test');
        $data->setWorkflowid('12345');

        $this->assertSame('Test', $data->name());

        $storyblokResponse = $workflowStageApi->create($data);
        $data = $storyblokResponse->data();

        $this->assertSame('653554', $data->id());
    }
}
