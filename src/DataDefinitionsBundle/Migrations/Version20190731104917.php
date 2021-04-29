<?php

declare(strict_types=1);

namespace Wvision\Bundle\DataDefinitionsBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20190731104917 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        if ($schema->hasTable('import_definitions_log')) {
            // delete just created table before renaming the old table
            if ($schema->hasTable('data_definitions_import_log')) {
                $this->addSql("DROP TABLE `data_definitions_import_log`");
            }

            $this->addSql('ALTER TABLE `import_definitions_log` RENAME TO `data_definitions_import_log`;');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
