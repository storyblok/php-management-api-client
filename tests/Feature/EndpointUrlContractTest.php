<?php

declare(strict_types=1);

namespace Tests\Feature;

use Storyblok\ManagementApi\Data\ExperimentResult;
use Storyblok\ManagementApi\Endpoints\AssetApi;
use Storyblok\ManagementApi\Endpoints\AssetFolderApi;
use Storyblok\ManagementApi\Endpoints\ExperimentApi;
use Storyblok\ManagementApi\Endpoints\InternalTagApi;
use Storyblok\ManagementApi\Endpoints\SpaceApi;
use Storyblok\ManagementApi\Endpoints\StoryApi;
use Storyblok\ManagementApi\Endpoints\TagApi;
use Storyblok\ManagementApi\Endpoints\WorkflowApi;
use Storyblok\ManagementApi\Endpoints\WorkflowStageApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Tests\TestCase;

/**
 * Pins the exact request URL each endpoint builds from caller-supplied
 * identifiers and names.
 *
 * Two groups:
 *  - "plain input" tests are a regression net: the URLs they assert must NOT
 *    change when path segments are later URL-encoded (rawurlencode of a plain
 *    token is a no-op).
 *  - "hostile input" tests prove free-text path values remain one segment even
 *    when they contain URL-significant characters such as slash or query marker.
 *
 * Under ManagementApiClient::initTest() there is no base_uri, so MockHttpClient
 * resolves relative paths against its default host, https://example.com.
 */
final class EndpointUrlContractTest extends TestCase
{
    private const string BASE = "https://example.com";

    /**
     * A client whose HTTP layer records nothing but a 200 — we only assert on
     * the resolved request URL, never on the response body.
     */
    private function client(): ManagementApiClient
    {
        $mock = new MockHttpClient(
            static fn($method, $url, $options): MockResponse => new MockResponse(
                '{"id":1,"name":"x"}',
                ["http_code" => 200],
            ),
        );

        return ManagementApiClient::initTest($mock);
    }

    // ---------------------------------------------------------------------
    // Regression net: exact URLs for well-formed identifiers / names.
    // These MUST remain green before and after the URL-encoding change.
    // ---------------------------------------------------------------------

    public function testSpaceGetUrl(): void
    {
        $url = (new SpaceApi($this->client()))->get("12345")->getLastCalledUrl();
        $this->assertSame(self::BASE . "/v1/spaces/12345", $url);
    }

    public function testSpaceDeleteUrl(): void
    {
        $url = (new SpaceApi($this->client()))->delete("12345")->getLastCalledUrl();
        $this->assertSame(self::BASE . "/v1/spaces/12345", $url);
    }

    public function testTagGetUrl(): void
    {
        $url = (new TagApi($this->client(), "222"))->get("release-notes")->getLastCalledUrl();
        $this->assertSame(self::BASE . "/v1/spaces/222/tags/release-notes", $url);
    }

    public function testTagDeleteUrl(): void
    {
        $url = (new TagApi($this->client(), "222"))->delete("release-notes")->getLastCalledUrl();
        $this->assertSame(self::BASE . "/v1/spaces/222/tags/release-notes", $url);
    }

    public function testTagUpdateUrlUsesCurrentName(): void
    {
        $url = (new TagApi($this->client(), "222"))->update("old-name", "new-name")->getLastCalledUrl();
        $this->assertSame(self::BASE . "/v1/spaces/222/tags/old-name", $url);
    }

    public function testWorkflowGetUrl(): void
    {
        $url = (new WorkflowApi($this->client(), "222"))->get("55")->getLastCalledUrl();
        $this->assertSame(self::BASE . "/v1/spaces/222/workflows/55", $url);
    }

    public function testWorkflowStageGetUrl(): void
    {
        $url = (new WorkflowStageApi($this->client(), "222"))->get("77")->getLastCalledUrl();
        $this->assertSame(self::BASE . "/v1/spaces/222/workflow_stages/77", $url);
    }

    public function testAssetGetUrl(): void
    {
        $url = (new AssetApi($this->client(), "222"))->get("42")->getLastCalledUrl();
        $this->assertSame(self::BASE . "/v1/spaces/222/assets/42", $url);
    }

    public function testAssetConvertUrl(): void
    {
        $url = (new AssetApi($this->client(), "222"))->convert("42", "8")->getLastCalledUrl();
        $this->assertSame(self::BASE . "/v1/spaces/222/assets/42/convert?target_asset_folder_id=8", $url);
    }

    public function testAssetFolderGetUrl(): void
    {
        $url = (new AssetFolderApi($this->client(), "222"))->get("3")->getLastCalledUrl();
        $this->assertSame(self::BASE . "/v1/spaces/222/asset_folders/3", $url);
    }

    public function testInternalTagGetUrl(): void
    {
        $url = (new InternalTagApi($this->client(), "222"))->get("4")->getLastCalledUrl();
        $this->assertSame(self::BASE . "/v1/spaces/222/internal_tags/4", $url);
    }

    public function testExperimentPushResultsUrl(): void
    {
        $url = (new ExperimentApi($this->client(), "222"))
            ->pushResults("6", ExperimentResult::forCharts([]))
            ->getLastCalledUrl();
        $this->assertSame(self::BASE . "/v1/spaces/222/experiments/6/results", $url);
    }

    public function testStoryGetUrl(): void
    {
        $url = (new StoryApi($this->client(), "222"))->get("8")->getLastCalledUrl();
        $this->assertSame(self::BASE . "/v1/spaces/222/stories/8", $url);
    }

    // ---------------------------------------------------------------------
    // Hostile input: tag names are single path segments, even when they include
    // URL-significant characters.
    // ---------------------------------------------------------------------

    public function testTagNameWithQueryStringIsEncoded(): void
    {
        $url = (new TagApi($this->client(), "222"))->get("a?x=1")->getLastCalledUrl();
        $this->assertSame(self::BASE . "/v1/spaces/222/tags/a%3Fx%3D1", $url);
    }

    public function testTagNameWithSlashIsEncoded(): void
    {
        $url = (new TagApi($this->client(), "222"))->get("a/b")->getLastCalledUrl();
        $this->assertSame(self::BASE . "/v1/spaces/222/tags/a%2Fb", $url);
    }

    public function testTagNameWithTraversalStaysInsideTagsPath(): void
    {
        $url = (new TagApi($this->client(), "222"))->get("../../spaces/999/tags/x")->getLastCalledUrl();
        $this->assertSame(self::BASE . "/v1/spaces/222/tags/..%2F..%2Fspaces%2F999%2Ftags%2Fx", $url);
    }
}
