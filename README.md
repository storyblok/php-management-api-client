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

## Initializing the MapiClient

Initialize the `MapiClient` with your personal access token to interact with the API:

```php
<?php
require 'vendor/autoload.php';

use Storyblok\Mapi\MapiClient;

// Initialize the client for the EU region with your personal access token
$c = MapiClient::initEU($storyblokPersonalAccessToken);
$spaceApi = $c->spaceApi();
```

You can use the methods `initEU` for accessing the Europe region and `initUS` for the US region.

Once you have `$spaceApi` you can retrieve and handle spaces via `all()` method, `get()`, `delete()` `create()` etc.

## Handling the Personal Access Token
Instead of handling the access token directly in the source code, you should consider to handle it via environment variables.
For example, you can create (if not already exists), the `.env` file and set a parameter for storing the Personal Access Token.

Then for loading the environment variable you can use the PHP dotenv package:

```php
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$storyblokPersonalAccessToken = $_ENV['SECRET_KEY'];
$clientEU = MapiClient::initEU($storyblokPersonalAccessToken);
```

> The PHP dotenv package is here: <https://github.com/vlucas/phpdotenv>

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
