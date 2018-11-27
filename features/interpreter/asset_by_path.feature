@interpreter @interpreter_asset_by_path
Feature: Adding a import with a cleaner
  The Interpreter will check for an image in Pimcore and assign it if found

  Background:
    Given there is a pimcore class "Product"
    And the definition has a input field "name"
    And the definition has a image field "image"
    And there is a asset with bundle file "@ImportDefinitionsBundle/Resources/fixtures/asset1.jpg" at path "/images"
    And there is a import-definition "Product" for definition
    And the import-definitions provider is "csv" with the configuration:
      | key         | value |
      | csvExample  | name  |
      | delimiter   | ,     |
      | enclosure   | "     |
    And  the import-definitions mapping is:
      | fromColumn | toColumn    | primary | interpreter   | interpreterConfig   |
      | name       | name        | true    |               |                     |
      | image      | image       | false   | asset_by_path | {"path": "/images"} |


  Scenario: When I run the import, the image field should be set
    Given there is a file test.csv with content:
      """
      name,image
      test1,asset1.jpg
      """
    And I run the import-definitions with params:
      | key  | value    |
      | file | test.csv |
    Then there should be "1" data-objects for definition
    And the field "image" for object of the definition should have the value of asset "/images/asset1.jpg"

  Scenario: When I run the import with an invalid image, the field should be null
    Given there is a file test.csv with content:
      """
      name,image
      test1,asset2.jpg
      """
    And I run the import-definitions with params:
      | key  | value    |
      | file | test.csv |
    Then there should be "1" data-objects for definition
    And the field "image" for object of the definition should have the value "false"
