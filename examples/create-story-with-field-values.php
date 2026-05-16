<?php

declare(strict_types=1);

use Storyblok\ManagementApi\Data\Enum\Region;
use Storyblok\ManagementApi\Data\Fields\MultilinkField;
use Storyblok\ManagementApi\Data\Fields\PluginField;
use Storyblok\ManagementApi\Data\Fields\RichtextField;
use Storyblok\ManagementApi\Data\Fields\TableField;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldMultilink;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldPlugin;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldRichtext;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldTable;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldText;
use Storyblok\ManagementApi\Data\Fields\Schema\FieldTextarea;
use Storyblok\ManagementApi\Data\Component;
use Storyblok\ManagementApi\Data\Story;
use Storyblok\ManagementApi\Data\StoryComponent;
use Storyblok\ManagementApi\Endpoints\ComponentApi;
use Storyblok\ManagementApi\Endpoints\StoryApi;
use Storyblok\ManagementApi\ManagementApiClient;
use Symfony\Component\HttpClient\Exception\ClientException;

require dirname(__DIR__) . "/vendor/autoload.php";

function envString(string $key, ?string $default = null): string
{
    $value = getenv($key);
    if ($value === false || $value === "") {
        if ($default !== null) {
            return $default;
        }

        fwrite(
            STDERR,
            "Missing required environment variable: {$key}" . PHP_EOL,
        );
        exit(1);
    }

    return $value;
}

function findComponentByName(
    ComponentApi $componentApi,
    string $componentName,
): ?Component {
    foreach ($componentApi->all()->data() as $component) {
        if (
            $component instanceof Component &&
            $component->name() === $componentName
        ) {
            return $component;
        }
    }

    return null;
}

function createContentType(
    ComponentApi $componentApi,
    string $contentType,
): Component {
    $component = (new Component($contentType))
        ->setDisplayName(ucwords(str_replace(["-", "_"], " ", $contentType)))
        ->setRoot()
        ->setPreviewField("title")
        ->appendFields([
            FieldText::make("title")->setDisplayName("Title")->setRequired(),
            FieldTextarea::make("summary")->setDisplayName("Summary"),
            FieldMultilink::make("cta_link")
                ->setDisplayName("CTA Link")
                ->setLinkTypes(["url", "story"])
                ->setAllowTargetBlank(),
            FieldRichtext::make("body")
                ->setDisplayName("Body")
                ->setToolbar(["bold", "italic", "link"]),
            FieldTable::make("comparison")->setDisplayName("Comparison"),
            FieldPlugin::make("custom_field")
                ->setDisplayName("Custom Field")
                ->setPlugin("example-plugin"),
        ]);

    return $componentApi->create($component)->data();
}

$token = envString("STORYBLOK_MANAGEMENT_TOKEN");
$spaceId = envString("STORYBLOK_SPACE_ID");
$regionCode = strtoupper(envString("STORYBLOK_REGION", "EU"));
$contentType = envString("STORYBLOK_CONTENT_TYPE", "article-page-2");
$storyName = envString("STORYBLOK_STORY_NAME", "Article created from PHP");
$storySlug = envString(
    "STORYBLOK_STORY_SLUG",
    "article-created-from-php-" . date("YmdHis"),
);
$publish = filter_var(
    envString("STORYBLOK_PUBLISH", "false"),
    FILTER_VALIDATE_BOOL,
);

if (!Region::isValid($regionCode)) {
    fwrite(
        STDERR,
        sprintf(
            "Invalid STORYBLOK_REGION value '%s'. Allowed values: %s",
            $regionCode,
            implode(", ", Region::values()),
        ) . PHP_EOL,
    );
    exit(1);
}

$client = new ManagementApiClient(
    personalAccessToken: $token,
    region: Region::from($regionCode),
    shouldRetry: true,
);

$componentApi = new ComponentApi($client, $spaceId);
$storyApi = new StoryApi($client, $spaceId);

try {
    $component = findComponentByName($componentApi, $contentType);

    if ($component instanceof Component) {
        echo "Content type already exists: " .
            $component->name() .
            " (" .
            $component->id() .
            ")" .
            PHP_EOL;
    } else {
        $component = createContentType($componentApi, $contentType);
        echo "Content type created: " .
            $component->name() .
            " (" .
            $component->id() .
            ")" .
            PHP_EOL;
    }
} catch (ClientException $exception) {
    fwrite(
        STDERR,
        "Storyblok API error while creating/checking the component:" . PHP_EOL,
    );
    fwrite(STDERR, $exception->getResponse()->getContent(false) . PHP_EOL);
    exit(1);
}

$content = new StoryComponent($contentType);
$content->set("title", "Article created from PHP");
$content->set(
    "summary",
    "This story uses structured field content value classes.",
);

$content
    ->setMultilink(
        "cta_link",
        MultilinkField::url("https://www.storyblok.com")
            ->openInNewTab(),
    )
    ->setRichtext(
        "body",
        RichtextField::paragraph(
            "This paragraph was created with RichtextField.",
        ),
    )
    ->setTable(
        "comparison",
        TableField::fromRows(
            ["Feature", "Value"],
            [
                ["Multilink", "Typed helper"],
                ["Richtext", "Paragraph helper"],
                ["Table", "Rows helper"],
            ],
        ),
    )
    ->setPlugin(
        "custom_field",
        new PluginField("example-plugin", [
            "value" => "custom plugin payload",
        ]),
    );

$story = new Story(name: $storyName, slug: $storySlug, content: $content);

try {
    $created = $storyApi->create($story, publish: $publish)->data();

    echo "Story created successfully" . PHP_EOL;
    echo "ID: " . $created->id() . PHP_EOL;
    echo "Name: " . $created->name() . PHP_EOL;
    echo "Slug: " . $created->slug() . PHP_EOL;
} catch (ClientException $exception) {
    fwrite(STDERR, "Storyblok API error:" . PHP_EOL);
    fwrite(STDERR, $exception->getResponse()->getContent(false) . PHP_EOL);
    exit(1);
} catch (Throwable $exception) {
    fwrite(STDERR, "Error: " . $exception->getMessage() . PHP_EOL);
    exit(1);
}
