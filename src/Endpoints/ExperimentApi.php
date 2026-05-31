<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\Data\Enum\ExperimentStatus;
use Storyblok\ManagementApi\Data\Experiment;
use Storyblok\ManagementApi\Data\ExperimentResult;
use Storyblok\ManagementApi\Response\ExperimentResponse;
use Storyblok\ManagementApi\Response\ExperimentResultResponse;
use Storyblok\ManagementApi\Response\ExperimentsResponse;

class ExperimentApi extends EndpointSpace
{
    public function page(
        int $page = 1,
        int $perPage = 25,
        ExperimentStatus|null $byStatus = null,
    ): ExperimentsResponse {
        $query = [
            "page" => $page,
            "per_page" => $perPage,
        ];

        if ($byStatus instanceof \Storyblok\ManagementApi\Data\Enum\ExperimentStatus) {
            $query["by_status"] = $byStatus->value;
        }

        $httpResponse = $this->makeHttpRequest(
            "GET",
            "/v1/spaces/" . $this->spaceId . "/experiments",
            [
                "query" => $query,
            ],
        );

        return new ExperimentsResponse($httpResponse);
    }

    public function create(Experiment $experiment): ExperimentResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "POST",
            "/v1/spaces/" . $this->spaceId . "/experiments",
            [
                "body" => json_encode(
                    [
                        "experiment" => $experiment->toArray(),
                    ],
                    JSON_THROW_ON_ERROR,
                ),
            ],
        );

        return new ExperimentResponse($httpResponse);
    }

    public function pushResults(
        string|int $experimentId,
        ExperimentResult $experimentResult,
    ): ExperimentResultResponse {
        $httpResponse = $this->makeHttpRequest(
            "POST",
            "/v1/spaces/" . $this->spaceId . "/experiments/" . $experimentId . "/results",
            [
                "body" => json_encode($experimentResult->toArray(), JSON_THROW_ON_ERROR),
            ],
        );

        return new ExperimentResultResponse($httpResponse);
    }
}
