# Changelog

## 1.2.0 - WIP
- Adding `AssetApi::update()` method for updating asset metadata (alt, title, asset_folder_id, internal_tag_ids, etc.)


## 1.1.5 - 2026-04-08
- Adding `contentType()` accessor to `StoryCollectionItem` for retrieving the root component name in list responses
- Adding `hasUnpublishedChanges()` accessor to stories for checking draft changes status
- Adding `workflowStageId()` accessor to stories for retrieving the workflow stage ID (returns `null` when not set)
- Adding `color()` and `workflowId()` accessors to `WorkflowStageData`
- Fixing `updatedAt()` signature to accept a `$format` parameter, consistent with `publishedAt()`, `createdAt()`, and `firstPublishedAt()`

## 1.1.4 - 2026-03-23
- Adding `StoryApi::versions()` endpoint for listing story versions with pagination
- Fixing asset upload folder assignment: using `asset_folder_id` instead of `parent_id`

## 1.1.3 - 2026-03-01
- Adding `CollaboratorApi` endpoint for listing collaborators with pagination
- Adding `CollaboratorsParams` query parameter class for filtering collaborators by space IDs (`by_space_ids`)
- Adding `Collaborator`, `Collaborators` data classes with nested user field accessors
- Adding `CollaboratorsResponse` response class
- Adding `AppApi` endpoint for listing and getting apps from the Storyblok marketplace
- Adding `AppProvisionApi` endpoint for managing installed apps (list, get, install, uninstall)
- Adding `App`, `Apps`, `AppProvision`, `AppProvisions` data classes
- Adding `AppResponse`, `AppsResponse`, `AppProvisionResponse`, `AppProvisionsResponse` response classes
- Adding `AppsParams` query parameter class for filtering apps by space ID with pagination
- Adding `SpacesParams` with `search` parameter for filtering spaces by name in `SpaceApi::all()`
- Adding `isNestable()`, `isContentType()`, `isUniversal()`, and `getComponentTypeDetail()` helper methods for Component
- Adding User helper methods: `friendlyName()`, `altEmail()`, `phone()`, `lang()`, `loginStrategy()`, `jobRole()`, `partnerRole()`, `isEditor()`, `isSso()`
- Adding `getIntStrict()` helper method (returing a forced integer with default)


## 1.1.2 - 2026-02-02
- Adding `MessageResponse` for typing responses with message data
- Fixing upload assets endpoint URL

## 1.1.1 - 2026-01-30
- Adding `isExternalUrl()` helper method for Asset
- Adding `getIds()` helper method for Assets
- Adding `deleteMultipleAssets()` method for deleting multiple assets
- Full test coverage for Asset and Assets classes

## 1.1.0 - 2026-01-25
- Moving from PestPHP to PHPUnit (version 12)
- Deprecate API factory methods, prefer dependency injection
- Adding phpstan-phpunit for tests (level 9)

## 1.0.11 - 2026-01-05
- Adding parameters for updating a story

## 1.0.10 - 2025-12-12
- Introduce `shouldRetry` flag for automatic 429 retry handling

## 1.0.9 - 2025-12-10
- Story management improvements:
    - Adding `publish` parameter in `create()` method for publishing the story immediately
    - Adding `releaseId` parameter in the `create()` method for creating the story in a specific release
    - Adding getter and setter methods for the parent folder in Story. `setFolderId()` and `folderId()`


## 1.0.8 - 2025-12-08
- Space class improvement, added some helper methos like `isOwnedByUser()`, `domain()`, `isDemo()`, `fistToken()`, `removeDemoMode()`
- Added Space methods for handling environments/Preview URLs

## 1.0.7 - 2025-12-07
- Added support for creating workflow stage changes via the Management API.

## 1.0.6 - 2025-12-06
- Bulk retrieval: Calling `all()` now applies the retry mechanism to the first page as well. Fixes #23.
- Testing: Added extensive test cases covering bulk retrieval and pagination.

## 1.0.5 - 2025-12-03
- Added `AssetField` class
- Fix `setAsset()`, accepting an `Asset` object for Asset Msanagement and converting into an `AssetField`

## 1.0.4 - 2025-10-02
- Adding `hasTags()`, `tagListAsString()`, `tagListAsArray()` methods for stories
- Adding `ownerId()`, `updatedAt()` methods to Space class

## 1.0.3 - 2025-09-29
- Adding new parameter for duplicating space (for adding the space in the organization)
- Adding `update()` method for Space endpoint
- Implementing `hasWorkflowStage()` method for Stories


## 1.0.2 - 2025-05-28
- Implementing ComponentApi class for handling components

## 1.0.1 - 2025-05-09
- Fixing getting list of Stories. Issue #10
- Adding new plans in `StoryblokUtils::getPlanDescription()` method
- Reviewing README
- Adding dependencies license checker

## 1.0.0 - 2025-03-14
- Releasing v 1.0.0

## 0.1.0 - 2025-02-21
- Refactoring exception handling: Bubble up the exception
- Refactoring Space, Spaces and SpaceResponse
- Adding some helper method to Space like region() and planLevel()
- Refactoring Story / Stories , StoryResponse, StoriesResponse
- Adding StoryComponent class
- Refactoring Asset, Assets and AssetResponse
- Refactoring Tag, Tags and TagResponse
  - Adding `setExternalUrl()` to `Asset` data class, you can set an external URL for an asset
- Adding `publishedAt()` method to `Story` data class, you can retrieve the published at field (with formatting options, default is "Y-m-d")
- Adding `addBlok()` method to `StoryComponent` data class, so you can easily add nested component to your content
- Readme: A new example for nesting components

## 0.0.7 - 2025-02-09

- AssetApi full coverage tests
- WorkflowApi full coverage tests
- WorkflowStageApi full coverage tests
- TagApi full coverage tests
- Introducing stricter type for responses (Asset response)
- Introducing common PHP CS Fixer configuration

## 0.0.6 - 2025-02-07

- Adding helper methods to UserData
- UserData / UserApi tests

## 0.0.5 - 2025-02-05

- Re-structuring bulk operations and pagination with `StoryBulkApi` class
- Refactoring creating new Api instance from Api classes

## 0.0.4 - 2025-02-05

- Adding WorkflowStage Api class for handling workflow stages
- Adding bulk story creation with rate limit handling

## 0.0.3 - 2025-02-03

- Adding helper methods for SpaceData (id(), updatedAt())
- Adding WorkflowApi class for handling workflows

## 0.0.2 - 2025-02-01

- Assets filtering with `AssetsParams`
- Setting per page default constant
- Filtering stories with `StoriesParams`
- Publishing a story with `publish()`
- Unpublishing a story with `unpublish()`
- Query Filters for Stories

## 0.0.1 - 2025-01-28

Initial release
