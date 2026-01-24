<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\Data\StoryblokData;
use Storyblok\ManagementApi\Data\WorkflowStageData;
use Storyblok\ManagementApi\Data\WorkflowStagesData;
use Storyblok\ManagementApi\QueryParameters\WorkflowStagesParams;
use Storyblok\ManagementApi\Response\StoryblokResponseInterface;
use Storyblok\ManagementApi\Response\WorkflowStageResponse;

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
            "query" => $params->toArray(),
        ];

        return $this->makeRequest(
            "GET",
            "/v1/spaces/" . $this->spaceId . "/workflow_stages",
            options: $options,
            dataClass: WorkflowStagesData::class,
        );
    }

    public function get(string|int $workflowStageId): WorkflowStageResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "GET",
            "/v1/spaces/" .
                $this->spaceId .
                "/workflow_stages/" .
                $workflowStageId,
        );
        return new WorkflowStageResponse($httpResponse);
    }

    /**
     * @param string|int $workflowStageId the workflow stage identifier
     */
    public function delete(string|int $workflowStageId): WorkflowStageResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "DELETE",
            "/v1/spaces/" .
                $this->spaceId .
                "/workflow_stages/" .
                $workflowStageId,
        );
        return new WorkflowStageResponse($httpResponse);
    }

    public function create(StoryblokData $storyblokData): WorkflowStageResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "POST",
            "/v1/spaces/" . $this->spaceId . "/workflow_stages",
            [
                "body" => [
                    "workflow_stage" => $storyblokData->toArray(),
                ],
            ],
        );
        return new WorkflowStageResponse($httpResponse);
    }

    public function update(
        string|int $workflowStageId,
        StoryblokData $storyblokData,
    ): WorkflowStageResponse {
        $httpResponse = $this->makeHttpRequest(
            "POST",
            "/v1/spaces/" .
                $this->spaceId .
                "/workflow_stages/" .
                $workflowStageId,
            [
                "body" => $storyblokData->toArray(),
            ],
        );
        return new WorkflowStageResponse($httpResponse);
    }
}
