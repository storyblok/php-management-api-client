# Proof of Concept: Storyblok Management API PHP Client

The *Storyblok Management API PHP Client* library simplifies the integration with Storyblok's Management API in PHP applications. With easy-to-use methods, you can interact with your Storyblok space effectively.

> ⚠️ This is just a Proof of Concept, so it is a Work In Progress. We are adding more endpoint coverage, specific Response Data, etc.

## Installation

Install the package via Composer:

```shell
composer require storyblok/php-management-api-client:dev-main
```

> Since we are in the PoC phase, you need to install the package via Composer using `:dev-main` suffix within the package name.


Below is an example showcasing how to use the library to interact with the Management API.

## Initializing the `MapiClient`

Initialize the `MapiClient` with your personal access token to interact with the API:

```php
<?php
require 'vendor/autoload.php';

use Storyblok\Mapi\MapiClient;

/** @var MapiClient $client */
$client = MapiClient::initEU($storyblokPersonalAccessToken);
```

You can use the methods `initEU` to access the European region.

If you need access to other regions, you can use:

- `initUS()` for the US region
- `initAP()` for the Asian Pacific region
- `initCA()` for the Canadian region
- `initCN()` for the China region.

You can use a more generic `init()` method defining the second parameter (a string) for setting the region. In this case, you can use the string "US" or "AP" or "CA" or "CN":

```php
// Using the default region EU
$client = MapiClient::init($storyblokPersonalAccessToken);
// Using the region US as an alternative way of initUS()
$client = MapiClient::init($storyblokPersonalAccessToken, 'US');
```

## Handling the Personal Access Token
Instead of handling the access token directly in the source code, you should consider handling it via environment variables.
For example, you can create the `.env` file (if it does not already exist) and set a parameter for storing the Personal Access Token.

Then, for loading the environment variable, you can use the PHP "dotenv" package:

```php
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$storyblokPersonalAccessToken = $_ENV['SECRET_KEY'];
$client = MapiClient::init($storyblokPersonalAccessToken);
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



### Retrieving content with ManagementApi class

To retrieve content using the `ManagementApi` class:

1. Initialize the client.
2. Obtain an instance of the `ManagementApi` class.
3. Call the `get` method of the `ManagementApi` class with the appropriate parameters.

For example, to retrieve multiple internal tags, use the Internal Tags endpoint with the GET HTTP method:
[Retrieve Multiple Internal Tags](https://www.storyblok.com/docs/api/management/core-resources/internal-tags/retrieve-multiple-internal-tags).

Below is an example of initializing the client for the EU region (default) using a Personal Access Token:

```php
$client = MapiClient::init($storyblokPersonalAccessToken);
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

### Getting a page of stories


```php

use Storyblok\Mapi\MapiClient;

$c = MapiClient::initEU($storyblokPersonalAccessToken);
$storyApi = $c->storyApi();

$response = $storyApi($spaceId)->page();

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

### Getting the assets list

To get the assets list you can use the `assetApi` and the `AssetsData`.

```php
$assetApi = $c->assetApi($spaceId);
$response = $assetApi->page();
/** @var AssetsData $assets */
$assets = $response->data();

foreach ($assets as $key => $asset) {
    echo $asset->get("id");
    echo $asset->get("content_type");
    echo $asset->get("content_length");
    echo $asset->filenameCDN();
}
```

### Getting one asset

To get a specific asset you can use the assetApi and the AssetData.

```php
$assetApi = $c->assetApi($spaceId);
$response = $assetApi->get($assetId);
/** @var AssetData $asset */
$asset = $response->data();
echo $asset->filenameCDN();
```

### Uploading an Asset

To upload an asset, you can use the `upload()` method:

```php
$assetApi = $c->assetApi($spaceId);
echo "UPLOADING " . $filename . PHP_EOL;
$response = $assetApi->upload($filename);
$uploadedAsset = $response->data();
echo "UPLOADED ASSET, ID : " . $uploadedAsset->get("id") . PHP_EOL;
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

## Handling all the other Endpoints
If you need to handle an endpoint not yet supported by this package, you can use the `ManagementApi` class, which is, in the end, a wrapper on top of the HTTP methods and returns data as StoryblokData. Thus,  you can easily access the structured and nested JSON you can retrieve in the response.
For example for retrieving the assets:

```php
$response = $clientEU->managementApi()->get("spaces/{$spaceId}/assets/");
foreach ($response->data()->get("assets") as $key => $asset) {
    echo $asset->get("id") . "  " .
    $asset->get("filename") . PHP_EOL;
}
```

## Features

- **Region-Specific Initialization**: Easily configure the SDK for different Storyblok regions.
- **Space Management**: Retrieve details, list spaces, and perform actions like backups.
- **Easy Response Handling**: Access status codes and error messages and parsed data conveniently.

## Documentation

Refer to the official documentation for detailed API descriptions and additional usage examples.

## Contributing

Feel free to open issues or submit pull requests to improve the package.

## License

This SDK is licensed under the MIT License. See the LICENSE file for details.
