# Proof of Concept: Storyblok Management API PHP Client

The *Storyblok Management API PHP Client* library simplifies the integration with Storyblok's Management API in PHP applications. With easy-to-use methods, you can interact with your Storyblok space effectively.

> ⚠️ This is just a Proof of Concept, so it is a Work In Progress. We are adding more endpoint coverage, specific Response Data, etc.

## Installation

Install the package via Composer:

```
composer require storyblok/storyblok-management-api-client
```

> Installing the package via Composer will be available after this PoC will be accepted as package.


Below is an example showcasing how to use the library to interact with the Management API.

## Initializing the MapiClient

Initialize the `MapiClient` with your personal access token to interact with the API:

```
<?php
require 'vendor/autoload.php';

use YourNamespace\StoryblokManagement\MapiClient;

// Initialize the client for the EU region with your personal access token
$c = MapiClient::initEU($storyblokPersonalAccessToken);
$spaceApi = $c->spaceApi();
```

You can use the methods `initEU` for accessing the Europe region and `initUS` for the US region.

Once you have `$spaceApi` you can retrieve and handle spaces via `all()` method, `get()`, `delete()` `create()` etc.


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
$response = $clientEU->storyApi($spaceId)->page();

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
## Handling all the other Endpoints
If you need to handle an endpoint not yet supported by this package, you can use the `GenericApi` class, which is, in the end, a wrapper on top of the HTTP methods and returns data as StoryblokData. Thus,  you can easily access the structured and nested JSON you can retrieve in the response.
For example for retrieving the assets:

```php
$response = $clientEU->genericApi()->get("spaces/{$spaceId}/assets/");
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
