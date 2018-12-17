@interpreter @interpreter_expression
Feature: Adding a import with a interpreter
  The Interpreter will execute a Symfony Expression

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


  Scenario: When I run the import, there should be a default value set
    Given the import-definitions mapping is:
      | fromColumn | toColumn | primary | interpreter | interpreterConfig |
      | name       | name     | true    |             |                   |
      | name2      | name2    | false   | expression  | {"expression": "value ~ '-expression'"} |
    And there is a file test.csv with content:
      """
      name,name2
      test1,name2
      """
    And I run the import-definitions with params:
      | key  | value    |
      | file | test.csv |
    Then there should be "1" data-objects for definition
    And the field "name2" for object of the definition should have the value "name2-expression"

  Scenario: When I run the import with a more complicated expression, there should be a default value set
    Given the import-definitions mapping is:
      | fromColumn | toColumn | primary | interpreter | interpreterConfig |
      | name       | name     | true    |             |                   |
      | name2      | name2    | false   | expression  | {"expression": "strtoupper(substr(value, 0, 1))"} |
    And there is a file test.csv with content:
      """
      name,name2
      test1,name2
      """
    And I run the import-definitions with params:
      | key  | value    |
      | file | test.csv |
    Then there should be "1" data-objects for definition
    And the field "name2" for object of the definition should have the value "N"