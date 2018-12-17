@interpreter @interpreter_conditional
Feature: Adding a import with a interpreter
  The Interpreter will will return 1 of two possible values based on the input value

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
      | fromColumn | toColumn | primary | interpreter | interpreterConfig |
      | name       | name     | true    |             |                   |
      | name       | name2    | false   | conditional | {"condition":"value == \"test1\"","true_interpreter":{"type":"expression","interpreterConfig":{"expression":"data['name'] ~ '-true'"}},"false_interpreter":{"type":"expression","interpreterConfig":{"expression":"data['name'] ~ '-false'"}}} |


  Scenario: When I run the import with a true value, the true_interpreter should get executed
    Given there is a file test.csv with content:
      """
      name
      test1
      """
    And I run the import-definitions with params:
      | key  | value    |
      | file | test.csv |
    Then there should be "1" data-objects for definition
    And the field "name2" for object of the definition should have the value "test1-true"

  Scenario: When I run the import with a false value, the false_interpreter should get executed
    Given there is a file test.csv with content:
      """
      name
      test2
      """
    And I run the import-definitions with params:
      | key  | value    |
      | file | test.csv |
    Then there should be "1" data-objects for definition
    And the field "name2" for object of the definition should have the value "test2-false"
