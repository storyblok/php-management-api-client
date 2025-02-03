<?php

declare(strict_types=1);


use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\MockHttpClient;

test('Testing list of workflows', function (): void {
    $responses = [
        \mockResponse("list-workflows", 200),
        \mockResponse("list-workflows", 200),
        //\mockResponse("empty-asset", 404),
    ];

    $client = new MockHttpClient($responses);
    $mapiClient = ManagementApiClient::initTest($client);
    $workflowApi = $mapiClient->workflowApi("222");

    $storyblokResponse = $workflowApi->list(
        $contentType = "article"
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*content_type=article.*$/');

    $storyblokResponse = $workflowApi->list(
        $contentType = [ "article", "category" ]
    );
    $string = $storyblokResponse->getLastCalledUrl();
    expect($string)->toMatch('/.*content_type=article%2Ccategory.*$/');



});
