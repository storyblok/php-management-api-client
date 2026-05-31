<?php

declare(strict_types=1);

namespace Tests\Feature;

use Storyblok\ManagementApi\Data\ExperimentResult;
use Storyblok\ManagementApi\Data\Experiment;
use Storyblok\ManagementApi\Data\Enum\ExperimentStatus;
use Storyblok\ManagementApi\Data\ExperimentVariant;
use Storyblok\ManagementApi\Endpoints\ExperimentApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Tests\TestCase;

final class ExperimentApiTest extends TestCase
{
    public function testCreateExperiment(): void
    {
        $lastRequest = [];
        $responses = [
            function (string $method, string $url, array $options) use (
                &$lastRequest,
            ): MockResponse {
                $lastRequest = [
                    "method" => $method,
                    "url" => $url,
                    "body" => $options["body"] ?? "",
                ];

                return $this->mockResponse('one-experiment', 200);
            },
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $experimentApi = new ExperimentApi($mapiClient, '222');

        $experiment = Experiment::make()
            ->setName('a_simple_test')
            ->setDisplayName('A simple test')
            ->setDescription('A short description for a simple test')
            ->setStoryIds([176024833123843])
            ->addExperimentVariant(
                ExperimentVariant::make()
                    ->setDisplayName("Control")
                    ->setControl(true)
                    ->setName("control")
                    ->setWeight(60),
            )
            ->addExperimentVariant(
                ExperimentVariant::make()
                    ->setDisplayName("Test")
                    ->setControl(false)
                    ->setName("test")
                    ->setWeight(40),
            );

        $storyblokResponse = $experimentApi->create($experiment);
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertSame("POST", $lastRequest["method"]);
        $this->assertMatchesRegularExpression('/.*spaces\/222\/experiments.*$/', $url);

        $payload = json_decode($lastRequest["body"], true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($payload);
        $this->assertArrayHasKey("experiment", $payload);
        $this->assertIsArray($payload["experiment"]);
        $this->assertSame("a_simple_test", $payload["experiment"]["name"]);
        $this->assertSame("A simple test", $payload["experiment"]["display_name"]);
        $this->assertSame([176024833123843], $payload["experiment"]["story_ids"]);
        $this->assertIsArray($payload["experiment"]["experiment_variants_attributes"]);
        $firstVariant = $payload["experiment"]["experiment_variants_attributes"][0];
        $this->assertIsArray($firstVariant);
        $this->assertSame(
            "control",
            $firstVariant["name"],
        );

        $data = $storyblokResponse->data();
        $this->assertSame('123', $data->id());
        $this->assertSame('homepage_hero_test', $data->name());
    }

    public function testListExperiments(): void
    {
        $responses = [
            $this->mockResponse('list-experiments', 200, [
                "total" => 2,
                "per-page" => 25,
            ]),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $experimentApi = new ExperimentApi($mapiClient, '222');

        $storyblokResponse = $experimentApi->page();
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression('/.*spaces\/222\/experiments.*$/', $url);
        $this->assertMatchesRegularExpression('/.*page=1&per_page=25.*$/', $url);
        $this->assertSame(2, $storyblokResponse->total());
        $this->assertSame(25, $storyblokResponse->perPage());

        $data = $storyblokResponse->data();
        $this->assertSame(2, $data->howManyExperiments());
        $firstExperiment = $data[0];
        $secondExperiment = $data[1];
        $this->assertInstanceOf(Experiment::class, $firstExperiment);
        $this->assertInstanceOf(Experiment::class, $secondExperiment);
        $this->assertSame('homepage_hero_test', $firstExperiment->name());
        $this->assertSame('pricing_cta_test', $secondExperiment->name());
    }

    public function testListExperimentsWithPaginationAndStatusFilter(): void
    {
        $responses = [
            $this->mockResponse('list-experiments', 200),
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $experimentApi = new ExperimentApi($mapiClient, '222');

        $storyblokResponse = $experimentApi->page(2, 10, ExperimentStatus::Running);
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression('/.*spaces\/222\/experiments.*$/', $url);
        $this->assertMatchesRegularExpression('/.*page=2.*$/', $url);
        $this->assertMatchesRegularExpression('/.*per_page=10.*$/', $url);
        $this->assertMatchesRegularExpression('/.*by_status=running.*$/', $url);
    }

    public function testPushExperimentResults(): void
    {
        $lastRequest = [];
        $responses = [
            function (string $method, string $url, array $options) use (
                &$lastRequest,
            ): MockResponse {
                $lastRequest = [
                    "method" => $method,
                    "url" => $url,
                    "body" => $options["body"] ?? "",
                ];

                return $this->mockResponse('one-experiment-result', 200);
            },
        ];

        $client = new MockHttpClient($responses);
        $mapiClient = ManagementApiClient::initTest($client);
        $experimentApi = new ExperimentApi($mapiClient, '222');

        $experimentResult = ExperimentResult::forCharts([
            [
                "kind" => "bar",
                "title" => "Conversion Rate",
                "xLabel" => "Variant",
                "yLabel" => "Rate",
                "labels" => ["Control", "Variant A"],
                "series" => [
                    [
                        "label" => "Conversion rate",
                        "data" => [0.12, 0.15],
                    ],
                ],
            ],
        ]);

        $storyblokResponse = $experimentApi->pushResults('987654321', $experimentResult);
        $url = $storyblokResponse->getLastCalledUrl();

        $this->assertMatchesRegularExpression(
            '/.*spaces\/222\/experiments\/987654321\/results.*$/',
            $url,
        );
        $this->assertTrue($storyblokResponse->isOk());

        $this->assertSame("POST", $lastRequest["method"]);
        $payload = json_decode($lastRequest["body"], true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($payload);
        $this->assertArrayHasKey("charts", $payload);
        $this->assertIsArray($payload["charts"]);
        $chart = $payload["charts"][0];
        $this->assertIsArray($chart);
        $this->assertSame("Conversion Rate", $chart["title"]);
        $this->assertArrayHasKey("series", $chart);
        $this->assertIsArray($chart["series"]);
        $series = $chart["series"][0];
        $this->assertIsArray($series);
        $this->assertSame([0.12, 0.15], $series["data"]);

        $data = $storyblokResponse->data();
        $this->assertSame('123456789', $data->id());
        $this->assertSame('987654321', $data->experimentId());
        $this->assertSame('Conversion Rate by Variant', $data->charts()[0]['title']);
        $this->assertSame('2026-03-15T10:30:00.000Z', $data->pushedAt());
    }
}
