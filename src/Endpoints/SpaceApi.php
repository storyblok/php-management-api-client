<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\Data\Space;
use Storyblok\ManagementApi\Data\Spaces;
use Storyblok\ManagementApi\Data\StoryblokData;
use Storyblok\ManagementApi\Response\SpaceResponse;
use Storyblok\ManagementApi\Response\SpacesResponse;
use Storyblok\ManagementApi\Response\StoryblokResponse;
use Storyblok\ManagementApi\Response\StoryblokResponseInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class SpaceApi extends EndpointBase
{
    protected const string API_PATH_SPACE_PREFIX_V1 = "/v1/spaces";

    public function all(): SpacesResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "GET",
            self::API_PATH_SPACE_PREFIX_V1,
        );
        return new SpacesResponse($httpResponse, Spaces::class);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function get(string $spaceId): SpaceResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "GET",
            self::buildSpacesEndpoint($spaceId),
        );

        return new SpaceResponse($httpResponse);
    }

    public function create(Space $spaceData): SpaceResponse
    {

        $httpResponse = $this->makeHttpRequest(
            "POST",
            self::API_PATH_SPACE_PREFIX_V1,
            [
                "body" => [
                    "space" => $spaceData->toArray(),
                ],
            ],
        );
        return new SpaceResponse($httpResponse);
    }

    public function update(string $spaceId, Space $spaceData): SpaceResponse
    {
        $this->validateSpaceId($spaceId);
        //$this->validateSpaceData($spaceData);
        echo $spaceData->toJson();
        $httpResponse = $this->makeHttpRequest(
            "PUT",
            $this->buildSpacesEndpoint($spaceId),
            [
                "body" => json_encode(["space" => $spaceData->toArray()]),
            ]
        );
        return new SpaceResponse($httpResponse);
    }

    public function duplicate(string|int $duplicateId, string $name, bool $inOrg = false): SpaceResponse
    {
        $body = [
            "dup_id" => $duplicateId,
            "space" => [
                "name" => $name,
            ],
        ];
        if ($inOrg) {
            $body["in_org"] = true;
        }

        $httpResponse = $this->makeHttpRequest(
            "POST",
            self::API_PATH_SPACE_PREFIX_V1,
            [
                "body" => $body,
            ],
        );
        return new SpaceResponse($httpResponse);
    }

    /**
     * @param $spaceId
     */
    public function delete(string $spaceId): SpaceResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "DELETE",
            self::API_PATH_SPACE_PREFIX_V1 . '/' . $spaceId,
        );
        return new SpaceResponse($httpResponse);
    }

    /**
     * @param $spaceId
     */
    public function backup(string $spaceId): SpaceResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "POST",
            sprintf('%s/%s/backups', self::API_PATH_SPACE_PREFIX_V1, $spaceId),
            [
                "body" => [
                ],
            ],
        );
        return new SpaceResponse($httpResponse);
    }

    private function buildSpacesEndpoint(string $spaceId): string
    {
        return sprintf('%s/%s', self::API_PATH_SPACE_PREFIX_V1, $spaceId);
    }

    /**
     * Validates space ID
     *
     * @throws \InvalidArgumentException
     */
    private function validateSpaceId(string $spaceId): void
    {
        if ($spaceId === '' || $spaceId === '0') {
            throw new \InvalidArgumentException('Space ID cannot be empty');
        }
    }
}
