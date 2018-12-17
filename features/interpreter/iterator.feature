@interpreter @interpreter_iterator
Feature: Adding a import with a interpreter
  The Interpreter will loop through the value and run sub-interpreters

  Background:
    Given there is a pimcore class "Product"
    And the definition has a input field "name"
    And the definition has a input field "tags"
    And there is a import-definition "Product" for definition
    And the import-definitions provider is "csv" with the configuration:
      | key         | value |
      | csvExample  | name  |
      | delimiter   | ,     |
      | enclosure   | "     |
    And  the import-definitions mapping is:
      | fromColumn | toColumn | primary | interpreter | interpreterConfig |
      | name       | name     | true    |             |                   |
      | tags       | tags     | false   | nested      | {"interpreters":[{"type":"expression","interpreterConfig":{"expression":"explode(';', value)"}},{"type":"iterator","interpreterConfig":{"interpreter":{"type":"expression","interpreterConfig":{"expression":"substr(value, 0, 1)"}}}},{"type":"expression","interpreterConfig":{"expression":"implode(';', value)"}}]} |


  Scenario: When I run the import, there should be a default value set
    Given there is a file test.csv with content:
      """
      name,tags
      test1,super;duper;object
      """
    And I run the import-definitions with params:
      | key  | value    |
      | file | test.csv |
    Then there should be "1" data-objects for definition
    And the field "tags" for object of the definition should have the value "s;d;o"
