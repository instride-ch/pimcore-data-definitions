@filter @simple_filter
Feature: Running an import with a simple filter

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
      | name       | name     | true    |
    And the import-definitions filter is "simple"
    And there is a file test.csv with content:
      """
      name,doFilter
      test1,1
      test2,1
      test3,0
      """
    And I run the import-definitions with params:
      | key  | value    |
      | file | test.csv |
    Then there should be "1" data-objects for definition