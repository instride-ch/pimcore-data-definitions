@interpreter @interpreter_external_image
Feature: Adding a import with a interpreter
  The Interpreter will import an external image

  Background:
    Given there is a pimcore class "Product"
    And the definition has a input field "name"
    And the definition has a external-image field "image"
    And there is a import-definition "Product" for definition
    And the import-definitions provider is "csv" with the configuration:
      | key         | value |
      | csvExample  | name  |
      | delimiter   | ,     |
      | enclosure   | "     |


  Scenario: When I run the import, there should be a default value set
    Given the import-definitions mapping is:
      | fromColumn | toColumn | primary | interpreter     | interpreterConfig |
      | name       | name     | true    |                 |                   |
      | image      | image    | false   | external_image  | {} |
    And there is a file test.csv with content:
      """
      name,image
      test1,https://images.unsplash.com/photo-1604614440637-e9ecd2fd8bf3
      """
    And I run the import-definitions with params:
      | key  | value    |
      | file | test.csv |
    Then there should be "1" data-objects for definition
    And the field "image" for object of the definition should be of type external-image
