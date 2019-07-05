CREATE TABLE IF NOT EXISTS `import_definitions_log`
(
    `id`         INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `definition` INT NOT NULL,
    `o_id`       INT NOT NULL
);
