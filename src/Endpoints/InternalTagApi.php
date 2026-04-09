<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Endpoints;

use Storyblok\ManagementApi\Data\InternalTag;
use Storyblok\ManagementApi\QueryParameters\InternalTagsParams;
use Storyblok\ManagementApi\Response\InternalTagResponse;
use Storyblok\ManagementApi\Response\InternalTagsResponse;

class InternalTagApi extends EndpointSpace
{
    /**
     * Retrieve internal tags.
     * @link https://www.storyblok.com/docs/api/management/core-resources/internal-tags/retrieve-multiple-internal-tags
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function page(?InternalTagsParams $params = null): InternalTagsResponse
    {
        $options = [];
        if ($params instanceof InternalTagsParams) {
            $options["query"] = $params->toArray();
        }

        $httpResponse = $this->makeHttpRequest(
            "GET",
            "/v1/spaces/" . $this->spaceId . "/internal_tags",
            options: $options,
        );
        return new InternalTagsResponse($httpResponse);
    }

    /**
     * Retrieve a single internal tag.
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function get(string|int $internalTagId): InternalTagResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "GET",
            "/v1/spaces/" . $this->spaceId . "/internal_tags/" . $internalTagId,
        );
        return new InternalTagResponse($httpResponse);
    }

    /**
     * Create an internal tag.
     * @link https://www.storyblok.com/docs/api/management/core-resources/internal-tags/create-an-internal-tag
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function create(InternalTag $internalTagData): InternalTagResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "POST",
            "/v1/spaces/" . $this->spaceId . "/internal_tags",
            [
                "body" => json_encode(["internal_tag" => $internalTagData->toArray()]),
            ],
        );
        return new InternalTagResponse($httpResponse);
    }

    /**
     * Update an internal tag.
     * @link https://www.storyblok.com/docs/api/management/core-resources/internal-tags/update-an-internal-tag
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function update(string|int $internalTagId, InternalTag $internalTagData): InternalTagResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "PUT",
            "/v1/spaces/" . $this->spaceId . "/internal_tags/" . $internalTagId,
            [
                "body" => json_encode(["internal_tag" => $internalTagData->toArray()]),
            ],
        );
        return new InternalTagResponse($httpResponse);
    }

    /**
     * Delete an internal tag.
     * @link https://www.storyblok.com/docs/api/management/core-resources/internal-tags/delete-an-internal-tag
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function delete(string|int $internalTagId): InternalTagResponse
    {
        $httpResponse = $this->makeHttpRequest(
            "DELETE",
            "/v1/spaces/" . $this->spaceId . "/internal_tags/" . $internalTagId,
        );
        return new InternalTagResponse($httpResponse);
    }
}
