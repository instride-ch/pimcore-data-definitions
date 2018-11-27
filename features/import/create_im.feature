@import
Feature: Adding a new simple import

  Background:
    Given there is a pimcore class "Product"
    And the definition has a input field "name"

  Scenario:
    Given there is a import-definition "Product" for definition
    And the import-definitions provider is "csv" with the configuration:
      | key         | value |
      | csvExample  | name  |
      | delimiter   | ,     |
      | enclosure   | "     |
    And  the import-definitions mapping is:
      | fromColumn | toColumn | primary |
      | name       | name     | x       |
    And there is a file test.csv with content:
      """
      name,
      test1,
      test2,
      test3
      """
    And I run the import-definitions with params:
      | key  | value    |
      | file | test.csv |
    Then there should be "3" data-objects for definition