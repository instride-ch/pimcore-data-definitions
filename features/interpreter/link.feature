@interpreter @interpreter_link
Feature: Adding a import with a interpreter
  The Interpreter will import an Link

  Background:
    Given there is a pimcore class "Product"
    And the definition has a link field "link"
    And there is a import-definition "Product" for definition
    And the import-definitions provider is "csv" with the configuration:
      | key         | value |
      | csvExample  | name  |
      | delimiter   | ,     |
      | enclosure   | "     |


  Scenario: When I run the import, there should be a default value set
    Given the import-definitions mapping is:
      | fromColumn | toColumn | primary | interpreter | interpreterConfig |
      | name       | name     | true    |             |                   |
      | link       | link     | false   | link        | {}  |
    And there is a file test.csv with content:
      """
      name,image
      test1,http://hbit.nl/
      """
    And I run the import-definitions with params:
      | key  | value    |
      | file | test.csv |
    Then there should be "1" data-objects for definition
    And the field "image" for object of the definition should be of type link
