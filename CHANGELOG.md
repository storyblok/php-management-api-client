# Changelog

## 1.5.1 - WIP
- Adding `OptionValue`, `addOption()`, and `addOptionValue()` for building `FieldOption` and `FieldOptions` choices without manual array shapes

## 1.5.0 - 2026-05-16
- Adding specialized component schema field classes in `Data/Fields/Schema/`: `FieldTextarea`, `FieldMarkdown`, `FieldDatetime`, `FieldOption`, `FieldOptions`, `FieldMultilink`, `FieldTable`, `FieldPlugin`, and `FieldSection`
- Extending `FieldGeneric::make()` to return the new specialized schema field classes for known Storyblok field types while keeping `FieldGeneric` as the fallback for unknown/custom types
- Adding typed helpers for common schema options: option/options sources and datasource slugs, multilink link types and target blank support, plugin `field_type`, and default values where applicable
- Adding `FieldRichtext::componentWhitelist()` and `FieldRichtext::setComponentWhitelist()` for configuring embedded component restrictions
- Keeping raw `get()` and `toArray()` behavior unchanged so unmodeled schema attributes remain accessible and payload-compatible
- Adding runtime field content value classes in `Data/Fields/`: `FieldValueInterface`, `MultilinkField`, `RichtextField`, `TableField`, and `PluginField`
- Making the existing `AssetField` implement `FieldValueInterface`
- Adding `StoryComponent::setMultilink()`, `StoryComponent::setRichtext()`, `StoryComponent::setTable()`, and `StoryComponent::setPlugin()` helpers while keeping raw `StoryComponent::set()` usage unchanged
- Making component metadata setters fluent, including `setName()`, `setDisplayName()`, `setImage()`, `setPreviewField()`, `setRoot()`, and `setNestable()`
- Adding `Component::addFields()` for adding multiple schema fields without changing `pos`
- Adding `Component::appendFields()` for appending multiple schema fields in order
- Adding `::make()` named constructors to specialized schema field classes while keeping existing constructors and `FieldGeneric::make()` hydration behavior unchanged

## 1.4.3 - 2026-05-15
- Adding `ManagementApi::patch()` helper for generic PATCH requests


## 1.4.2 - 2026-05-11
- Fixing `WorkflowStageApi` update payload

## 1.4.1 - 2026-05-04
- Adding `Component::maxPos(): int` returning the highest `pos` value across all schema entries (fields and tabs); returns `-1` for an empty schema; use `maxPos() + 1` to get the next available position when appending a field with an explicit `pos`
- Adding `Component::appendField(FieldInterface $field): self` for appending a field at the end of the schema; automatically sets `pos` to `maxPos() + 1` without shifting any existing entry

## 1.4.0 - 2026-05-03
- Adding `Component::getFields(?string $tab = null)` returning typed `FieldInterface` objects, sorted by `pos`, tabs excluded; optional `$tab` parameter filters to fields belonging to that tab
- Adding `Component::getTabs()` returning tab entries sorted by `pos`
- Adding `Component::getFieldTab(string $fieldName)` returning the display name of the tab a field belongs to, or `null`
- Adding `FieldInterface` with shared accessors: `key()`, `type()`, `pos()`, `displayName()`, `required()`, `translatable()`, `noTranslate()`, `description()`, `tooltip()`
- Adding `FieldGeneric` base class implementing `FieldInterface` with a `make()` factory that dispatches to specialized subclasses by type
- Adding specialized field classes in `Data/Fields/Schema/`: `FieldText`, `FieldNumber`, `FieldBoolean`, `FieldRichtext`, `FieldBloks`, `FieldAsset`, `FieldMultiasset`
- Adding `FieldInterface::get(int|string $key, mixed $defaultValue = null): mixed` for accessing raw field attributes not covered by typed methods, with dot-notation support for nested keys
- Adding `FieldInterface::toArray(): array` to expose field attributes as a plain array suitable for API payloads
- Adding fluent builder support to all field classes: constructor now accepts only the field key; `TYPE` constant sets the type automatically; shared setters on `FieldGeneric` (`setPos()`, `setDisplayName()`, `setRequired()`, `setTranslatable()`, `setNoTranslate()`, `setDescription()`, `setTooltip()`) and type-specific setters on each specialized class all return `static` for chaining
- Adding `Component::addField(FieldInterface $field): self` for adding typed field objects to a component schema using the fluent builder
- Adding `Component::insertField(FieldInterface $field, int $atPos): self` for inserting a field at a specific position; automatically shifts every existing schema entry (fields and tabs) at `pos >= $atPos` up by one to keep positions consistent

## 1.3.0 - 2026-04-30
- `Space` constructor `$name` is now optional (default `''`); when omitted or empty, the `name` field is not added to the payload so the API leaves the existing name untouched on update
- Adding `Space::forUpdate(array $fields)` static factory for partial updates; sends only the specified fields without forcing a `name` into the payload
- Fixing `IterableDataTrait::offsetGet()` to return the same typed object as `foreach` iteration; `$collection[n]` now returns the typed item class (e.g. `StoryCollectionItem`, `AssetFolder`, `Space`) instead of plain `StoryblokData`
- `StoriesParams::withParent` type narrowed from `string|int|null` to `int|null`; `string` was never a valid parent ID. Note: passing `0` sends `with_parent=0` to the API, but the Storyblok API treats `0` as falsy and ignores it, use client-side filtering for root-level stories instead.
- `StoriesParams::inRelease` type narrowed from `string|int|null` to `int|null`; release IDs are numeric

## 1.2.0 - 2026-04-21
- Adding `AssetApi::update()` method for updating asset metadata (alt, title, asset_folder_id, internal_tag_ids, etc.)
- Adding `AssetFolderApi` endpoint for managing asset folders (list, get, create, update, delete)
- Adding `AssetFolder`, `AssetFolders` data classes
- Adding `AssetFolderResponse`, `AssetFoldersResponse` response classes
- Adding `InternalTagApi` endpoint for managing internal tags (list, get, create, update, delete)
- Adding `InternalTag`, `InternalTags` data classes
- Adding `InternalTagResponse`, `InternalTagsResponse` response classes
- Adding `InternalTagsParams` query parameter class for filtering by object type and search
- Adding `setIsFolder()` and `setDefaultRoot()` methods to `Story` for creating folders with a default content type
- Adding `isFolder()` accessor to `StoryBaseData` for checking whether a story is a folder
- Adding `setContentTypes()` and `setLockSubfoldersContentTypes()` methods to `StoryComponent` for restricting allowed content types in folders
- Adding `Story::asFolder()` static factory and `StoryApi::createFolder()` positional helper for creating folders with all UI fields (name, slug, parent, default content type, allowed content types, lock sub-folders, disable visual editor)
- Adding `Story::setDisableFeEditor()` setter for toggling the Visual Editor on a story or folder
- Adding `StoryblokUtils::slugify()` utility for generating URL-friendly slugs from names


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
