# Proof of Concept: Storyblok Management API PHP Client

The *Storyblok Management API PHP Client* library simplifies the integration with Storyblok's Management API in PHP applications. With easy-to-use methods, you can interact with your Storyblok space effectively.

> ⚠️ This is just a Proof of Concept, so it is a Work In Progress. We are adding more endpoint coverage, specific Response Data, etc.

## Installation

Install the package via Composer:

```shell
composer require storyblok/php-management-api-client:dev-main
```

> Since we are in the PoC phase, you must install the package via Composer using the `:dev-main` suffix within the package name.


Below is an example showcasing how to use the library to interact with the Management API.

## Initializing the `MapiClient`

Initialize the `MapiClient` with your personal access token to interact with the API:

```php
<?php
require 'vendor/autoload.php';

use Storyblok\Mapi\MapiClient;

/** @var MapiClient $client */
$client = new MapiClient($storyblokPersonalAccessToken);
```
The second optional parameter is for setting the region.
We provide an Enum class to set the region. In this case, you can use the `Region` enum: `Region::US` or `Region::AP` or `Region::CA` or `Region::CN`.

For example, for using the US region, you can use:
```php

use \Storyblok\Mapi\Data\Enum\Region;

$client = new MapiClient($storyblokPersonalAccessToken, Region::US);
```

## Handling the Personal Access Token
To access the Management API and interact with its endpoints, you need to follow two steps:

- Retrieve a Personal Access Token (or an OAuth token).
- Store the token securely and make it available to your application (e.g., in an environment variable or another secure location).

> The token for accessing the Management API differs from the Access Token used for the Content Delivery API.

To obtain a proper token for accessing the Management API you can choose:

- **Personal Access Token**: Navigate to [your Storyblok account settings](https://app.storyblok.com/#/me/account?tab=token) and click on "Generate new token."
- **OAuth Token**: Follow the steps outlined in [this guide on authentication apps](https://www.storyblok.com/docs/plugins/authentication-apps).

Once you have your Token, instead of storing the access token directly in the source code, you should consider handling it via environment variables.
For example, you can create the `.env` file (if it does not already exist) and set a parameter for storing the Personal Access Token.

Then, for loading the environment variable, you can use the PHP "dotenv" package:

```php
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$storyblokPersonalAccessToken = $_ENV['SECRET_KEY'];
$client = new MapiClient($storyblokPersonalAccessToken);
```

> The PHP dotenv package is here: <https://github.com/vlucas/phpdotenv>

## Using the ManagementApi class

The Storyblok Management API Client provides two main approaches for interacting with the API:

- Using the `ManagementApi` class
- Using specific API classes (like `StoryApi` or `SpaceApi`)

The `ManagementApi` class offers a flexible, generic interface for managing content. It includes methods to get, create, update, and delete content. With this approach, you can define the endpoint path and pass query string parameters as a generic array. The response is returned as a `StoryblokData` object, allowing you to access the JSON payload, status codes, and other details directly.

Alternatively, you can leverage dedicated classes like `SpaceApi`, which are tailored to specific resources. For instance, the `SpaceApi` class provides methods for managing spaces and returns specialized data objects, such as `SpaceData` (for a single space) or `SpacesData` (for a collection of spaces). These classes simplify interactions with specific endpoints by offering resource-specific methods.

If a dedicated API class like `SpaceApi` or `StoryApi` does not exist for your desired endpoint, you can always fall back to the more versatile `ManagementApi` class.

To illustrate how to use the `ManagementApi` class, we will demonstrate its usage with the Internal Tags endpoint.

> **Reference**: [Management API documentation for the Internal Tags endpoint](https://www.storyblok.com/docs/api/management/core-resources/internal-tags/)

This approach can be adapted to create other resources by modifying the endpoint and payload, for example, for handling data sources, components, etc. To learn more about the endpoints, the parameters, and the structure of the response payload, you can use the [Storyblok Management API reference](https://www.storyblok.com/docs/api/management/getting-started).

### Retrieving content with ManagementApi class

To retrieve content using the `ManagementApi` class:

1. Initialize the client.
2. Obtain an instance of the `ManagementApi` class.
3. Call the `get` method of the `ManagementApi` class using the appropriate parameters.

For example, to retrieve multiple internal tags, use the Internal Tags endpoint with the GET HTTP method:
[Retrieve Multiple Internal Tags](https://www.storyblok.com/docs/api/management/core-resources/internal-tags/retrieve-multiple-internal-tags).

Below is an example of initializing the client for the EU region (default) using a Personal Access Token:

```php
$client = new MapiClient($storyblokPersonalAccessToken);
```

Getting the ManagementApi instance:

```php
$managementApi = $client->managementApi()
```

Calling GET HTTP method with `spaces/:spaceid/internal_tags`:

```php
$spaceId = "12345";
$response = $clientEU->managementApi()->get(
    "spaces/{$spaceId}/internal_tags",
    [
        "by_object_type" => "asset",
        //"search" => "some"
    ]
);
```

#### Using Query Parameters and Accessing Data

You can pass query string parameters as an array in the second parameter of the `get()` method.

To retrieve internal tags, specify the object type, such as `asset` or `component`. In the example below, the `by_object_type` parameter is set to `asset` to fetch tags for assets. You can also filter asset tags by name using the `search` query parameter.

The `get()` method returns a `StoryblokResponse` instance, which provides useful information such as the last called URL, the total number of items (helpful for paginated responses), and the data itself:

```php
echo $response->getLastCalledUrl() . PHP_EOL;
// https://mapi.storyblok.com/v1/spaces/321388/internal_tags?by_object_type=asset

echo $response->asJson();
// The returned JSON {"internal_tags":[ ... ]}

echo "Total Tags: " . $response->total() . PHP_EOL;
// Total Tags: 8
```

You can access the `internal_tags` data in the returned JSON like this:

```php
$tags = $response->data()->get("internal_tags");
```

The `$response->data()` method retrieves an instance of the `StoryblokData` class, which is responsible for storing the JSON response data in memory. It also provides convenient methods to access JSON values, including nested data.

For example, the `get()` method, provided by the `StoryblokData` class, allows access to specific data:

```php
$tagName = $response->data()->get('internal_tags.0.name');
```

In this case, you're retrieving the name of the first tag in the `internal_tags` array.



#### Looping through the items

Thanks to the `StoryblokData` object returned by the `$response->data()`, you can loop through the items and get values.

To loop through `internal_tags` from the `StoryblokData` object and access individual values.

To retrieve the items, you use the `get()` method to access the `internal_tags` array.

 ```php
 $tags = $response->data()->get("internal_tags");
 ```

To iterate through the items, you can use `foreach` because StoryblokData is iterable, and you can access specific properties of each tag using the `get()` method:

 ```php
 foreach ($tags as $tag) {
     $name = $tag->get("name");
     $id = $tag->get("id");
     $objectType = $tag->get("object_type");
 }
 ```



### Creating a new resource with the Storyblok Management API

This example demonstrates creating a new internal tag using the Storyblok Management API.

First, define the tag details in an array, including attributes like `name` and `object_type`. Then, use the `post` method of the `ManagementApi` class to send a POST request to the `internal_tags` endpoint for the specified space.

The response will indicate whether the operation was successful. If it succeeds, you can retrieve the created tag’s data using the `data()->get("internal_tag")` method. If the operation fails, the error message can be retrieved using the `getErrorMessage()` method.

Here is the complete example:

```php
// Define the tag details
$tag = [
    "name" => "new tag",
    "object_type" => "asset"
];

// Send the POST request to create the tag
$response = $managementApi()->post(
    "spaces/{$spaceId}/internal_tags",
    ["internal_tag" => $tag]
);

// Show the URL of the response
echo $response->getLastCalledUrl() . PHP_EOL;

if ($response->isOk()) {
    // Parse the created tag data
    $createdTag = $response->data()->get("internal_tag");
    echo "Tag created with id: " . $createdTag->get("id") . PHP_EOL;
    echo $createdTag->toJson();
} else {
    // Handle errors
    echo $response->getErrorMessage();
}

```

### Editing a resource with the Storyblok Management API

This example demonstrates how to update an existing resource, such as an internal tag, using the Storyblok Management API.

To edit a resource, first retrieve or define the resource data you want to update. Modify the desired fields in the resource array, then use the `put` method of the `ManagementApi` class to send an update request to the appropriate endpoint, including the resource's ID.

After sending the request, check if the operation was successful. If successful, you can log the updated response or any relevant details. If it fails, retrieve the error message for debugging.

Here is the complete example:

```php
$tag["name"] = $tag["name"] . "-UPDATED";
$response = $managementApi()->put(
    "spaces/{$spaceId}/internal_tags/{$id}",
    ["internal_tag" => $tag]
);
if ($response->isOk()) {
    echo "Updated Response : <" . $response->getResponseBody() . ">" . PHP_EOL;
    echo "Tag updated via id: " . $id . PHP_EOL;
} else {

    echo $response->getErrorMessage() . PHP_EOL;
}
```

### Deleting a resource with the Storyblok Management API

This example explains how to delete a resource, such as an internal tag, using the Storyblok Management API.

To delete a resource, use the `delete` method of the `ManagementApi` class and specify the appropriate endpoint along with the resource's ID. The ID uniquely identifies the resource you want to remove.

After sending the delete request, check the response to confirm whether the operation was successful. For a successful delete, the response body is typically empty. If the operation fails, retrieve and log the error message for further investigation.

Here is the complete example:

```php
$response = $managementApi()->delete(
    "spaces/{$spaceId}/internal_tags/{$id}"
);
if ($response->isOk()) {
    echo "Response from a delete is empty: <" . $response->getResponseBody() . ">" . PHP_EOL;
    echo "Tag deleted via id: " . $id . PHP_EOL;
} else {
    echo $response->getErrorMessage() . PHP_EOL;
}

```

### Quick recap: using the `ManagementApi` Class

The `ManagementApi` class is used for performing generic administrative tasks in Storyblok, including creating, updating, retrieving, and deleting resources. 

### Note: `ManagementApi` vs Specialized Api Classes

In addition to the general-purpose `ManagementApi` class, the Storyblok Management PHP client also provides specific classes such as `SpaceApi`, `StoryApi`, `TagApi` and `AssetApi`. These classes function similarly to the `ManagementApi` but are tailored for specific scenarios, offering additional methods or data types to work with particular resources.

- **`SpaceApi`** focuses on managing space-level operations, such as retrieving space information, performing backup etc.
- **`StoryApi`** specializes in handling stories and their content, including creating, updating, retrieving, and deleting stories. This class also provides methods that deal with the structure and fields specific to stories.
- **`AssetApi`** designed to manage assets like images, files, and other media. It provides methods to upload, retrieve, and manage assets, offering features specific to media management.
- **`TagApi`** designed to manage tags.

These specialized classes extend the functionality of the `ManagementApi` class, offering more precise control and optimized methods for interacting with specific resource types in your Storyblok space.



## Handling Spaces

### Retrieve all the spaces

Fetch a list of all spaces associated with your account in the current region (the region is initialized in the `MapiClient`):

```php
// Retrieve all spaces
$response = $spaceApi->all();
echo "STATUS CODE : " . $response->getResponseStatusCode() . PHP_EOL;
echo "LAST URL    : " . $response->getLastCalledUrl() . PHP_EOL;
$data = $response->data();
```

### Loop through the spaces

Iterate through the list of spaces to access their details:

```php
foreach ($data as $key => $space) {
    echo $space->get("region") . " " . $space->get("id") . " " . $space->get("name") . PHP_EOL;
}
echo "SPACE NAME  : " . $data->get("0.name") . PHP_EOL;
echo "SPACES      : " . $data->count() . PHP_EOL;
```

### Get one specific Space

Retrieve detailed information about a specific space using its ID:

```php
// Get details of a specific space
$response = $spaceApi->get($spaceID);
$space = $response->data();
echo $space->get("name") . PHP_EOL;
echo $space->get("plan") . " " . $space->get("plan_level") . PHP_EOL;
```

### Triggering the backup

Create a backup of a specific space by triggering the backup API:

```php
// Create a backup for a space
try {
    $response = $spaceApi->backup($spaceID);
    if ($response->isOk()) {
        echo "BACKUP DONE!";
    } else {
        echo $response->getErrorMessage() . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error, " . $e;
}
```

## Handling Stories

### Getting the StoryApi instance

To handle Stories, get stories, get a single story, create a story, update a story, or delete a story, you can start getting the instance of StoryApi that allows you to access the methods for handling stories.

```php
use Storyblok\Mapi\MapiClient;
$spaceId= "1234";
$client = new MapiClient($storyblokPersonalAccessToken);
$storyApi = $client->storyApi($spaceId);
```



### Getting a page of stories


```php
$response = $storyApi->page();

echo "STATUS CODE : " . $response->getResponseStatusCode() . PHP_EOL;
echo "LAST URL    : " . $response->getLastCalledUrl() . PHP_EOL;
echo "TOTAL       : " . $response->total() . PHP_EOL;
echo "PAR PAGE    : " . $response->perPage() . PHP_EOL;

$data = $response->data();
echo "Stories found with the page: " . $data->howManyStories() . PHP_EOL;
foreach ($data as $key => $story) {
    echo $story->get("id") . "  " .
    $story->getName() . PHP_EOL;
}
```

### Getting a Story by ID

```php
$storyId= "1234";
$response = $storyApi->get($storyId);

echo "STATUS CODE : " . $response->getResponseStatusCode() . PHP_EOL;
echo "LAST URL    : " . $response->getLastCalledUrl() . PHP_EOL;

$story = $response->data();
echo $story->getName() . PHP_EOL;
```

### Creating a Story

To create a story, you can call the `create()` method provided by `StoryApi` and use the `StoryData` class. The `StoryData` class is specific for storing and handling story information. It also provides some nice methods for accessing some relevant Story fields.

```php
$story = new StoryData();
$story->setName("A Story");
$story->setSlug("a-story");
$story->setContentType("page");
$response = $storyApi->create($story);

echo $response->getLastCalledUrl() . PHP_EOL;
echo $response->asJson() . PHP_EOL;
echo $response->getResponseStatusCode() . PHP_EOL;
if ($response->isOk()) {
    $storyCreated = $response->data();
    echo "Story created, ID: " . $storyCreated->id() . PHP_EOL;
    echo "             UUID: " . $storyCreated->uuid() . PHP_EOL;
    echo "             SLUG: " . $storyCreated->slug() . PHP_EOL;
} else {
    echo $response->getErrorMessage();
}
```



## Handling users

### Getting the current user

To get the current user, owner of the Personal access token used you can use the userApi and the UserData.

```php

$response = $c->userApi()->me();
 /** @var UserData $currentUser */
$currentUser = $response->data();
// "User ID"
echo $currentUser->id();
// "User identifier"
echo $currentUser->userid();
// "User email"
echo $currentUser->email());
// "User has Organization"
echo $currentUser->hasOrganization() ? " HAS ORG" : "NO ORG";
// "User Organization"
echo $currentUser->orgName();
// "User has Partner"
echo $currentUser->hasPartner() ? " HAS PARTNER" : "NO PARTNER";
```

## Handling assets

### Getting the AssetApi instance

To handle assets, get assets, get a single asset, upload an asset, update an asset, or delete an asset, you can start getting the instance of AssetApi that allows you to access the methods for handling assets.

```php
use Storyblok\Mapi\MapiClient;
$client = new MapiClient($storyblokPersonalAccessToken);

$spaceId = "spaceid";
$assetApi = $client->assetApi($spaceId);
```

### Getting the assets list

To get the assets list you can use the `assetApi` and the `AssetsData`.

```php
$assetApi = $client->assetApi($spaceId);
$response = $assetApi->page();
/** @var AssetsData $assets */
$assets = $response->data();

foreach ($assets as $key => $asset) {
    echo $asset->id() . PHP_EOL;
    echo $asset->contentType() . PHP_EOL;
    echo $asset->contentLength() . PHP_EOL;
    echo $asset->filenameCDN() . PHP_EOL;
    echo "---" . PHP_EOL;
}
```

### Getting one asset

To get a specific asset, you can use the `AssetApi` and the `AssetData` classes.

```php
$response = $assetApi->get($assetId);
/** @var AssetData $assets */
$asset = $response->data();
echo $asset->id() . PHP_EOL;
echo $asset->contentType() . PHP_EOL;
echo $asset->contentLength() . PHP_EOL;
echo $asset->filenameCDN() . PHP_EOL;
echo $asset->filename() . PHP_EOL;
echo "---" . PHP_EOL;
```

### Uploading an Asset

To upload an asset, you can use the `upload()` method:

```php
$response = $assetApi->upload("image.png");

echo $response->getLastCalledUrl() . PHP_EOL;
echo $response->asJson() . PHP_EOL;
echo $response->getResponseStatusCode() . PHP_EOL;
if ($response->isOk()) {
    $assetCreated = $response->data();
    echo "Asset created, ID: " . $assetCreated->id() . PHP_EOL;
    echo "         filename: " . $assetCreated->filename() . PHP_EOL;
    echo "     filename CDN: " . $assetCreated->filenameCDN() . PHP_EOL;
} else {
    echo $response->getErrorMessage();
}
```

### Deleting an Asset

To delete an asset, you can use the `delete()` method. The `delete()` method requires the asset ID (you want to delete) as parameter:

```php
$assetApi = $c->assetApi($spaceId);
echo "DELETING " . $assetId . PHP_EOL;
$response = $assetApi->delete($assetId);
$deletedAsset = $response->data();
echo "DELETED ASSET, ID : " . $deletedAsset->get("id") . PHP_EOL;
```

## Handling tags

### Getting the TagApi instance

To handle tags, get tags,create an asset, update an asset, or delete an asset, you can start getting the instance of `TagApi` that allows you to access the methods for handling tags.

```php
use Storyblok\Mapi\MapiClient;
$client = new MapiClient($storyblokPersonalAccessToken);

$spaceId = "spaceid";
$tagApi = $client->tagApi($spaceId);
```

### Getting the tags list

To get the tags list you can use the `tagApi` and the `TagsData`.

```php
$pageNumber=1;
$itemsPerPage= 5;
$response = $tagApi->page($pageNumber, $itemsPerPage);
echo "Total Tags: " . $response->total() . PHP_EOL;
/** @var TagsData $tags */
$tags = $response->data();
foreach ($tags as $key => $tag) {
    echo $tag->name() . PHP_EOL;
    echo $tag->taggingsCount() . PHP_EOL;
    echo $tag->tagOnStories() . PHP_EOL;
    echo "---" . PHP_EOL;
}
```

### Creating a new tag

To create a new tag, you can define the name of the tag using the `create` method:
```php
$assetName = "tag-" . random_int(100, 999);
$response = $tagApi->create($assetName);
if ($response->isOk()) {
    $tagCreated = $response->data();
    $name = $tagCreated->name();
		echo "TAG Created: " . $name . PHP_EOL;

} else {
    echo $response->getErrorMessage();
}
```

## A practical example

Now we want to upload a new image, and then create a new simple story that includes the new image.

```php
use Storyblok\Mapi\Data\StoryblokData;
use Storyblok\Mapi\Data\StoryData;
use Storyblok\Mapi\MapiClient;

$client = new MapiClient($storyblokPersonalAccessToken);

$spaceId = "your-space-id";
$storyApi = $client->storyApi($spaceId);
$assetApi = $client->assetApi($spaceId);

echo "UPLOADING ASSET..." . PHP_EOL;
$response = $assetApi->upload("image.png");
/** @var AssetData $assetCreated */
$assetCreated = $response->data();
echo "Asset created, ID: " . $assetCreated->id() . PHP_EOL;

echo "PREPARING STORY DATA..." . PHP_EOL;
$content = new StoryblokData();
$content->set("component", "article-page");
$content->set("title", "New Article");
$content->set("body", "This is the content");
$content->set("image.id", $assetCreated->id());
$content->set("image.fieldtype", "asset");
$content->set("image.filename",$assetCreated->filename());

$story = new StoryData();
$story->setName("An Article");
$story->setSlug("an-article-" . random_int(10000, 99999));
$story->setContent($content->toArray());

echo "CREATING STORY..." . PHP_EOL;
$response = $storyApi->create($story);

echo $response->getLastCalledUrl() . PHP_EOL;
echo $response->asJson() . PHP_EOL;
echo $response->getResponseStatusCode() . PHP_EOL;
if ($response->isOk()) {
  	/** @var StoryData $storyCreated */
    $storyCreated = $response->data();
    echo "Story created, ID: " . $storyCreated->id() . PHP_EOL;
    echo "             UUID: " . $storyCreated->uuid() . PHP_EOL;
    echo "             SLUG: " . $storyCreated->slug() . PHP_EOL;
} else {
    echo $response->getErrorMessage();
}
```



## Documentation

Refer to the official documentation for detailed API descriptions and additional usage examples: https://www.storyblok.com/docs/api/management/getting-started

## Contributing

Feel free to open issues or submit pull requests to improve the package.

## License

This SDK is licensed under the MIT License. See the LICENSE file for details.
