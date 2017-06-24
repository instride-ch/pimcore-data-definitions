<?php

namespace Wvision\Bundle\ImportDefinitionsBundle\ProcessManager;

use ProcessManagerBundle\Model\ExecutableInterface;
use ProcessManagerBundle\Process\Pimcore;

final class ImportDefinitionProcess extends Pimcore
{
    /**
     * {@inheritdoc}
     */
    function run(ExecutableInterface $executable)
    {
        $settings = $executable->getSettings();

        $settings['command'] = sprintf('importdefinitions:run -d %s -p "%s"', $settings['definition'], addslashes($settings['params']));

        $executable->setSettings($settings);

        return parent::run($executable);
    }
}