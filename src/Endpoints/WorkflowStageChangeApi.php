<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\Data\StoryblokData;
use Storyblok\ManagementApi\Data\WorkflowStageChange;
use Storyblok\ManagementApi\Data\WorkflowStageChanges;
use Storyblok\ManagementApi\Data\WorkflowStageData;
use Storyblok\ManagementApi\Data\WorkflowStagesData;
use Storyblok\ManagementApi\QueryParameters\WorkflowStageChangesParams;
use Storyblok\ManagementApi\QueryParameters\WorkflowStagesParams;
use Storyblok\ManagementApi\Response\StoryblokResponseInterface;
use Storyblok\ManagementApi\Response\WorkflowStageChangeResponse;
use Storyblok\ManagementApi\Response\WorkflowStageChangesResponse;

class WorkflowStageChangeApi extends EndpointSpace
{
    /**
     * Returns the WorkflowStageChangesResponse object with the list of workflow stage change objects.
     */
    public function page(
        string|int $withStory,
        int $page = 1,
        int $perPage = 5,
    ): WorkflowStageChangesResponse {
        $options = [];
        $params = new WorkflowStageChangesParams($withStory);
        $paramsArray = $params->toArray();
        $paramsArray["page"] = $page;
        $paramsArray["per_page"] = $perPage;
        $options = [
            "query" => $paramsArray,
        ];
        $httpResponse = $this->makeHttpRequest(
            "GET",
            "/v1/spaces/" . $this->spaceId . "/workflow_stage_changes",
            options: $options,
        );

        return new WorkflowStageChangesResponse($httpResponse);
    }

    /**
     *
     * @param int|int[] $assignSpaceRoleIds
     * @param int|int[] $assignUserIds
     */
    public function create(
        WorkflowStageChange $workflowStageChange,
        string|int|null $releaseId = "0",
        ?bool $notify = false,
        ?string $commentMessage = "",
        int|array $assignSpaceRoleIds = [],
        int|array $assignUserIds = [],
    ): WorkflowStageChangeResponse {
        $body = [
            "workflow_stage_change" => $workflowStageChange->toArray(),
        ];
        if (!is_null($releaseId)) {
            $body["release_id"] = $releaseId;
        }

        if (!is_null($notify)) {
            $body["notify"] = $notify;
        }

        if (!is_null($commentMessage)) {
            $body["comment"] = [];
            $body["comment"]["message"] = $commentMessage;
        }

        $body["assign"] = [];
        $body["assign"]["space_role_ids"] = $assignSpaceRoleIds;
        $body["assign"]["user_ids"] = $assignUserIds;

        $httpResponse = $this->makeHttpRequest(
            "POST",
            "/v1/spaces/" . $this->spaceId . "/workflow_stage_changes",
            [
                "body" => $body,
            ],
        );

        return new WorkflowStageChangeResponse($httpResponse);
    }
}
