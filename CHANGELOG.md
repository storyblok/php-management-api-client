# Changelog

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
