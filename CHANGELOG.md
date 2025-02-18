# Changelog

## 0.1.0 - WIP
- Refactoring exception handling: Bubble up the exception
- Refactoring Space, Spaces and SpaceResponse
- Adding some helper method to Space like region() and planLevel()
- Refactoring Story / Stories , StoryResponse, StoriesResponse
- Adding StoryComponent class
- Refactoring Asset, Assets and AssetResponse
- Refactoring Tag, Tags and TagResponse

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
