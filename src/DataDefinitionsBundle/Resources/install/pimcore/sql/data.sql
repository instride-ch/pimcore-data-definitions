CREATE TABLE IF NOT EXISTS `data_definitions_import_log`
(
    `id`         INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `definition` INT NOT NULL,
    `o_id`       INT NOT NULL
);
