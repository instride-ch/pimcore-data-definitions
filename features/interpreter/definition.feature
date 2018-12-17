@interpreter @interpreter_definition
Feature: Adding a import with a interpreter
  The Interpreter will call another definition

  Background:
    Given there is a pimcore class "Category"
    And the definition has a input field "name"
    Given there is a pimcore class "Product"
    And the definition has a input field "name"
    And the definition has a href field "category"
    And there is a import-definition "Category" for class "Category"
    And the import-definition "Category" provider is "raw" with the configuration:
      | key         | value   |
      | headers     | catname |
    And  the import-definitions mapping is:
      | fromColumn | toColumn | primary |
      | catname    | name     | true    |
    And there is a import-definition "Product" for class "Product"
    And the import-definition "Product" provider is "csv" with the configuration:
      | key         | value |
      | csvExample  | name  |
      | delimiter   | ,     |
      | enclosure   | "     |
    And  the import-definitions mapping is:
      | fromColumn | toColumn | primary |
      | name       | name     | true    |
      | catname    | category | false   |
    And the import-definition "Product" mapping for column "category" uses interpreter "definition" for import-definition "Category"

  Scenario: When I run the import, there should be a default value set
    Given there is a file test.csv with content:
      """
      name,catname
      test1,shows
      """
    And I run the import-definition "Product" with params:
      | key  | value    |
      | file | test.csv |
    Then there should be "1" data-objects for class "Product"
    Then there should be "1" data-objects for class "Category"
    And the field "name" for object of class "Product" should have the value "test1"
    And the field "name" for object of class "Category" should have the value "shows"
