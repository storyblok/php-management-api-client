<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\Data\StoryblokData;
use Storyblok\ManagementApi\Data\WorkflowStageData;
use Storyblok\ManagementApi\Data\WorkflowStagesData;
use Storyblok\ManagementApi\QueryParameters\WorkflowStagesParams;
use Storyblok\ManagementApi\Response\StoryblokResponseInterface;

class WorkflowStageChangeApi extends EndpointSpace
{
    /**
     * @param string|string[]|null $byIds
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function list(
        string|int|null $withStory = null,
    ): StoryblokResponseInterface {
        $options = [];
        $params = new WorkflowStageChangesParams(
            $withStory,
        );

        $options = [
            'query' => $params->toArray(),
        ];

        return $this->makeRequest(
            "GET",
            '/v1/spaces/' . $this->spaceId . '/workflow_stage_changes',
            options: $options,
            dataClass: WorkflowStageChangesData::class,
        );
    }



    public function create(
        StoryblokData $storyblokData,
        string|int $release_id = null,
        bool $notify = false,


    ): StoryblokResponseInterface
    {
        return $this->makeRequest(
            "POST",
            "/v1/spaces/" . $this->spaceId . '/workflow_stage_changes',
            [
                "body" => [
                    "workflow_stage_change" => $storyblokData->toArray(),
                ],
            ],
            dataClass: WorkflowStageChangeData::class,
        );
    }


}
