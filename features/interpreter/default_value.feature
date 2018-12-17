@interpreter @interpreter_default_value
Feature: Adding a import with a interpreter
  The Interpreter will return a static value

  Background:
    Given there is a pimcore class "Product"
    And the definition has a input field "name"
    And the definition has a input field "name2"
    And there is a import-definition "Product" for definition
    And the import-definitions provider is "csv" with the configuration:
      | key         | value |
      | csvExample  | name  |
      | delimiter   | ,     |
      | enclosure   | "     |
    And  the import-definitions mapping is:
      | fromColumn | toColumn | primary | interpreter   | interpreterConfig |
      | name       | name     | true    |               |                   |
      | name       | name2    | false   | default_value | {"value": "blub"} |


  Scenario: When I run the import, there should be a default value set
    Given there is a file test.csv with content:
      """
      name
      test1
      """
    And I run the import-definitions with params:
      | key  | value    |
      | file | test.csv |
    Then there should be "1" data-objects for definition
    And the field "name2" for object of the definition should have the value "blub"
