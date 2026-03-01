<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Storyblok\ManagementApi\Data\Collaborator;
use Storyblok\ManagementApi\Endpoints\CollaboratorApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Storyblok\ManagementApi\QueryParameters\CollaboratorsParams;
use Symfony\Component\HttpClient\MockHttpClient;

final class CollaboratorApiTest extends TestCase
{
    public function testPageOfCollaborators(): void
    {
        $responses = [
            $this->mockResponse('list-collaborators', 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $collaboratorApi = new CollaboratorApi($mapiClient, '300');

        $storyblokResponse = $collaboratorApi->page();
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression('/.*collaborators.*$/', $url);

        $data = $storyblokResponse->data();
        $this->assertSame(2, $data->howManyCollaborators());

        $firstData = $data->toArray()[0];
        $this->assertIsArray($firstData);
        /** @var mixed[] $firstData */
        $first = Collaborator::make($firstData);
        $this->assertSame('10001', $first->id());
        $this->assertSame('editor', $first->role());
        $this->assertSame('300', $first->spaceId());
        $this->assertSame('Alice', $first->firstname());
        $this->assertSame('Smith', $first->lastname());
        $this->assertSame('Alice S.', $first->friendlyName());
        $this->assertSame('alice@example.com', $first->realEmail());
    }

    public function testCollaboratorsIteration(): void
    {
        $responses = [
            $this->mockResponse('list-collaborators', 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $collaboratorApi = new CollaboratorApi($mapiClient, '300');

        $data = $collaboratorApi->page()->data();

        $count = 0;
        foreach ($data as $collaborator) {
            $this->assertInstanceOf(Collaborator::class, $collaborator);
            $this->assertNotEmpty($collaborator->role());
            $this->assertNotEmpty($collaborator->firstname());
            ++$count;
        }

        $this->assertSame(2, $count);
    }

    public function testPageOfCollaboratorsBySpaceIds(): void
    {
        $responses = [
            $this->mockResponse('list-collaborators', 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $collaboratorApi = new CollaboratorApi($mapiClient, '300');

        $params = new CollaboratorsParams(bySpaceIds: ['300', '400']);
        $storyblokResponse = $collaboratorApi->page($params);
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression('/.*collaborators.*$/', $url);
        $this->assertStringContainsString('by_space_ids=300%2C400', $url);

        $data = $storyblokResponse->data();
        $this->assertSame(2, $data->howManyCollaborators());
    }
}
