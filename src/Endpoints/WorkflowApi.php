<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\Data\StoryblokData;
use Storyblok\ManagementApi\Data\WorkflowData;
use Storyblok\ManagementApi\Data\WorkflowsData;
use Storyblok\ManagementApi\Response\StoryblokResponseInterface;
use Storyblok\ManagementApi\Response\WorkflowResponse;

class WorkflowApi extends EndpointSpace
{
    /**
     * @param string|string[]|null $contentType
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function list(
        string|array|null $contentType = null,
    ): StoryblokResponseInterface {
        $options = [];
        if (null !== $contentType) {
            if (is_array($contentType)) {
                $contentType = implode(",", $contentType);
            }

            $options = [
                "query" => [
                    "content_type" => $contentType,
                ],
            ];
        }

        return $this->makeRequest(
            "GET",
            "/v1/spaces/" . $this->spaceId . "/workflows",
            options: $options,
            dataClass: WorkflowsData::class,
        );
    }

    public function get(string|int $workflowId): WorkflowResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "GET",
            "/v1/spaces/" . $this->spaceId . "/workflows/" . $workflowId,
        );
        return new WorkflowResponse($httpResponse);
    }

    /**
     * @param string|int $workflowId the workflow identifier
     */
    public function delete(string|int $workflowId): WorkflowResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "DELETE",
            "/v1/spaces/" . $this->spaceId . "/workflows/" . $workflowId,
        );
        return new WorkflowResponse($httpResponse);
    }

    public function create(StoryblokData $storyblokData): WorkflowResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "POST",
            "/v1/spaces/" . $this->spaceId . "/workflows",
            [
                "body" => [
                    "workflow" => $storyblokData->toArray(),
                ],
            ],
        );
        return new WorkflowResponse($httpResponse);
    }

    public function update(
        string|int $workflowId,
        StoryblokData $storyblokData,
    ): WorkflowResponse {
        $httpResponse = $this->makeHttpRequest(
            "POST",
            "/v1/spaces/" . $this->spaceId . "/workflows/" . $workflowId,
            [
                "body" => $storyblokData->toArray(),
            ],
        );
        return new WorkflowResponse($httpResponse);
    }
}
