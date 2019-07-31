<?php

namespace Wvision\Bundle\DataDefinitionsBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Pimcore\Migrations\Migration\AbstractPimcoreMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20180917132630 extends AbstractPimcoreMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if ($this->container->has('import_definitions.repository.definition')) {
            $definitions = $this->container->get('import_definitions.repository.definition')->findAll();
        }
        else {
            $definitions = $this->container->get('data_definitions.repository.import_definition')->findAll();
        }

        foreach ($definitions as $definition) {
            $definition->save();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}


