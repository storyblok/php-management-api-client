<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Storyblok\ManagementApi\Data\WorkflowData;
use Storyblok\ManagementApi\Endpoints\WorkflowApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\MockHttpClient;

final class WorkflowApiTest extends TestCase
{
    public function testListOfWorkflows(): void
    {
        $responses = [
            $this->mockResponse('list-workflows', 200),
            $this->mockResponse('list-workflows', 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $workflowApi = new WorkflowApi($mapiClient, '222');

        $storyblokResponse = $workflowApi->list('article');
        $url = $storyblokResponse->getLastCalledUrl();
        $this->assertMatchesRegularExpression('/.*content_type=article.*$/', $url);

        $storyblokResponse = $workflowApi->list(['article', 'category']);
        $url = $storyblokResponse->getLastCalledUrl();
        $this->assertMatchesRegularExpression('/.*content_type=article%2Ccategory.*$/', $url);
    }

    public function testOneWorkflow(): void
    {
        $responses = [
            $this->mockResponse('one-workflow', 200),
            $this->mockResponse('list-workflows', 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $workflowApi = new WorkflowApi($mapiClient, '222');

        $storyblokResponse = $workflowApi->get('15268');
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression('/.*workflow.*$/', $url);

        $data = $storyblokResponse->data();
        $this->assertSame('15268', $data->getString('id'));
        $this->assertSame('15268', $data->id());
        $this->assertSame('author workflow', $data->name());
        $this->assertFalse($data->isDefault());
        $this->assertSame(['author'], $data->contentTypes());
    }

    public function testDeletingWorkflow(): void
    {
        $responses = [
            $this->mockResponse('one-workflow', 200),
            $this->mockResponse('list-workflows', 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $workflowApi = new WorkflowApi($mapiClient, '222');

        $storyblokResponse = $workflowApi->delete('15268');
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression('/.*workflow.*$/', $url);

        $data = $storyblokResponse->data();
        $this->assertSame('15268', $data->getString('id'));
        $this->assertSame('15268', $data->id());
        $this->assertSame('author workflow', $data->name());
        $this->assertFalse($data->isDefault());
        $this->assertSame(['author'], $data->contentTypes());
    }

    public function testCreatingWorkflow(): void
    {
        $responses = [
            $this->mockResponse('one-workflow', 200),
            $this->mockResponse('list-workflows', 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $workflowApi = new WorkflowApi($mapiClient, '222');

        $workflowData = new WorkflowData();
        $workflowData->setName('Name');

        $storyblokResponse = $workflowApi->create($workflowData);
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression('/.*workflow.*$/', $url);
        $this->assertTrue($storyblokResponse->isOk());
    }

    public function testUpdatingWorkflow(): void
    {
        $responses = [
            $this->mockResponse('one-workflow', 200),
            $this->mockResponse('one-workflow', 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $workflowApi = new WorkflowApi($mapiClient, '222');

        $storyblokResponse = $workflowApi->get('15268');
        $workflowData = $storyblokResponse->data();
        $workflowData->setName('Name');

        $storyblokResponse = $workflowApi->update('15268', $workflowData);

        $url = $storyblokResponse->getLastCalledUrl();
        $this->assertMatchesRegularExpression('/.*workflow.*$/', $url);

        $data = $storyblokResponse->data();
        $this->assertSame('15268', $data->getString('id'));
        $this->assertSame('15268', $data->id());
        $this->assertSame('author workflow', $data->name());
        $this->assertFalse($data->isDefault());
        $this->assertSame(['author'], $data->contentTypes());
    }
}
