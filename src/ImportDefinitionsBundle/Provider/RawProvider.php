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
 * @copyright  Copyright (c) 2016-2018 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace ImportDefinitionsBundle\Provider;

use ImportDefinitionsBundle\Model\Mapping\FromColumn;

class RawProvider implements ProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function testData($configuration)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns($configuration)
    {
        $headers = explode(',', $configuration['headers']);
        $returnHeaders = [];

        if (\count($headers) > 0) {
            //First line are the headers
            foreach ($headers as $header) {
                if (!$header) {
                    continue;
                }

                $headerObj = new FromColumn();
                $headerObj->setIdentifier($header);
                $headerObj->setLabel($header);

                $returnHeaders[] = $headerObj;
            }
        }

        return $returnHeaders;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($configuration, $definition, $params, $filter = null)
    {
        return $params['data'];
    }
}