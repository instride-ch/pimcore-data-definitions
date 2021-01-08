@interpreter @interpreter_mapping
Feature: Adding a import with a interpreter
  The Interpreter will map a from-field to a static field

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
      | fromColumn | toColumn | primary |
      | name       | name     | true    |
      | tags       | tags     | false   |
    And the import-definitions mapping for column "tags" uses interpreter "mapping" with config:
      """
      {
          "mapping": [
            {"from": "blub", "to": "blub2"},
            {"from": "blub2", "to": "blub3"},
            {"from": "blub3", "to": "blub4"}
          ],
          "return_null_when_not_found": true
      }
      """


  Scenario: When I run the import with a valid mapping entry, it should use the mapping value
    Given there is a file test.csv with content:
      """
      name,tags
      test1,blub
      """
    And I run the import-definitions with params:
      | key  | value    |
      | file | test.csv |
    Then there should be "1" data-objects for definition
    And the field "tags" for object of the definition should have the value "blub2"

  Scenario: When I run the import with a invalid mapping entry, it should return null
    Given there is a file test.csv with content:
      """
      name,tags
      test1,blub123
      """
    And I run the import-definitions with params:
      | key  | value    |
      | file | test.csv |
    Then there should be "1" data-objects for definition
    And the field "tags" for object of the definition should have the value "null"

    Scenario: When I run the import with a invalid mapping entry, it should return null
      Given the import-definitions mapping for column "tags" uses interpreter "mapping" with config:
      """
      {
          "mapping": [
            {"from": "blub", "to": "blub2"},
            {"from": "blub2", "to": "blub3"},
            {"from": "blub3", "to": "blub4"}
          ],
          "return_null_when_not_found": false
      }
      """
      And there is a file test.csv with content:
        """
        name,tags
        test1,blub123
        """
      And I run the import-definitions with params:
        | key  | value    |
        | file | test.csv |
      Then there should be "1" data-objects for definition
      And the field "tags" for object of the definition should have the value "blub123"

