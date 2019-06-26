<?php
/**
 * Data Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2019 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace WVision\Bundle\DataDefinitionsBundle\Form\Type\ProcessManager;

use ProcessManagerBundle\Form\Type\AbstractStartupFormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ExportDefinitionObjectStartupForm extends AbstractStartupFormType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('root', TextType::class, [
                'required' => false
            ]);
    }
}

class_alias(ExportDefinitionObjectStartupForm::class, 'ImportDefinitionsBundle\Form\Type\ProcessManager\ExportDefinitionObjectStartupForm');
