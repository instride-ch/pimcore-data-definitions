@interpreter @interpreter_checkbox
Feature: Adding a import with a interpreter
  The Interpreter will transform a boolean value

  Background:
    Given there is a pimcore class "Product"
    And the definition has a input field "name"
    And the definition has a checkbox field "active"
    And there is a import-definition "Product" for definition
    And the import-definitions provider is "csv" with the configuration:
      | key         | value |
      | csvExample  | name  |
      | delimiter   | ,     |
      | enclosure   | "     |
    And  the import-definitions mapping is:
      | fromColumn | toColumn    | primary | interpreter |
      | name       | name        | true    |             |
      | active     | active      | false   | checkbox    |


  Scenario: When I run the import with a integer, the field active field should be true
    Given there is a file test.csv with content:
      """
      name,active
      test1,1
      """
    And I run the import-definitions with params:
      | key  | value    |
      | file | test.csv |
    Then there should be "1" data-objects for definition
    And the field "active" for object of the definition should have the value "true"

  Scenario: When I run the import with a string, the field active field should be true
    Given there is a file test.csv with content:
      """
      name,active
      test1,true
      """
    And I run the import-definitions with params:
      | key  | value    |
      | file | test.csv |
    Then there should be "1" data-objects for definition
    And the field "active" for object of the definition should have the value "true"

  Scenario: When I run the import with a integer, the field active field should be false
    Given there is a file test.csv with content:
      """
      name,active
      test1,0
      """
    And I run the import-definitions with params:
      | key  | value    |
      | file | test.csv |
    Then there should be "1" data-objects for definition
    And the field "active" for object of the definition should have the value "false"

  Scenario: When I run the import with a string, the field active field should be false
    Given there is a file test.csv with content:
      """
      name,active
      test1,false
      """
    And I run the import-definitions with params:
      | key  | value    |
      | file | test.csv |
    Then there should be "1" data-objects for definition
    And the field "active" for object of the definition should have the value "false"

