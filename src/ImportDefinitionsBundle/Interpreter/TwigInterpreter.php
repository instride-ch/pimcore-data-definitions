<?php
/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2017 Divante (http://www.divante.co)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle\Interpreter;

use ImportDefinitionsBundle\Model\DefinitionInterface;
use ImportDefinitionsBundle\Model\Mapping;
use Pimcore\Model\DataObject\Concrete;
use Twig\Environment;

class TwigInterpreter implements InterpreterInterface
{
    /**
     * @var \Twig\Environment
     */
    private $twig;

    /**
     * @param \Twig\Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     *{@inheritdoc}
     */
    public function interpret(
        Concrete $object,
        $value,
        Mapping $map,
        $data,
        DefinitionInterface $definition,
        $params,
        $configuration
    ) {
        return $this->twig->createTemplate($configuration['template'])->render([
            'value' => $value,
            'object' => $object,
            'map' => $map,
            'data' => $data,
            'definition' => $definition,
            'params' => $params,
            'configuration' => $configuration,
        ]);
    }
}
