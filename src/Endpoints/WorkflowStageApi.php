<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\Data\StoryblokData;
use Storyblok\ManagementApi\Data\WorkflowStageData;
use Storyblok\ManagementApi\Data\WorkflowStagesData;
use Storyblok\ManagementApi\QueryParameters\WorkflowStagesParams;
use Storyblok\ManagementApi\Response\StoryblokResponseInterface;

class WorkflowStageApi extends EndpointSpace
{
    /**
     * @param string|string[]|null $byIds
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function list(
        string|int|null $inWorkflowId = null,
        string|null $search = null,
        string|array|null $byIds = null,
        string|null $excludeId = null,
    ): StoryblokResponseInterface {
        $options = [];
        $params = new WorkflowStagesParams(
            $inWorkflowId,
            $search,
            $byIds,
            $excludeId,
        );

        $options = [
            'query' => $params->toArray(),
        ];

        return $this->makeRequest(
            "GET",
            '/v1/spaces/' . $this->spaceId . '/workflow_stages',
            options: $options,
            dataClass: WorkflowStagesData::class,
        );
    }

    public function get(string|int $workflowStageId): StoryblokResponseInterface
    {

        return $this->makeRequest(
            "GET",
            '/v1/spaces/' . $this->spaceId . '/workflow_stages/' . $workflowStageId,
            dataClass: WorkflowStageData::class,
        );
    }

    /**
     * @param string|int $workflowStageId the workflow stage identifier
     */
    public function delete(string|int $workflowStageId): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "DELETE",
            '/v1/spaces/' . $this->spaceId . '/workflow_stages/' . $workflowStageId,
            dataClass: WorkflowStageData::class,
        );
    }

    public function create(StoryblokData $storyblokData): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "POST",
            "/v1/spaces/" . $this->spaceId . '/workflow_stages',
            [
                "body" => [
                    "workflow_stage" => $storyblokData->toArray(),
                ],
            ],
            dataClass: WorkflowStageData::class,
        );
    }

    public function update(string|int $workflowStageId, StoryblokData $storyblokData): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "POST",
            "/v1/spaces/" . $this->spaceId . '/workflow_stages/' . $workflowStageId,
            [
                "body" => $storyblokData->toArray(),
            ],
            dataClass: WorkflowStageData::class,
        );
    }
}
