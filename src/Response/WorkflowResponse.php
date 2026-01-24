<?php

declare(strict_types=1);

namespace Storyblok\ManagementApi\Response;

use Storyblok\ManagementApi\Data\Space;
use Storyblok\ManagementApi\Data\Tag;
use Storyblok\ManagementApi\Data\WorkflowData;
use Storyblok\ManagementApi\Data\WorkflowStageChange;
use Storyblok\ManagementApi\Exceptions\StoryblokFormatException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class WorkflowResponse extends StoryblokResponse implements
    StoryblokResponseInterface
{
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws StoryblokFormatException
     * @throws ClientExceptionInterface
     */
    #[\Override]
    public function data(): WorkflowData
    {
        $key = "workflow";
        $array = $this->toArray();
        if (array_key_exists($key, $array)) {
            return WorkflowData::make($array[$key]);
        }

        $additionalErrorString = "";
        if (array_key_exists("message", $array)) {
            $additionalErrorString = " " . $array["message"];
        }

        throw new StoryblokFormatException(
            sprintf(
                "Expected '%s' in the response.%s",
                $key,
                $additionalErrorString,
            ),
        );
    }
}
