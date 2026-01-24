<?php
/**
 * Data Definitions.
 *
 * This source file is available under the Data Definitions Commercial License (DDCL).
 * Full copyright and license information is available in LICENSE.md
 * which is distributed with this source code.
 *
 * @copyright  Copyright (c) CORS GmbH (https://www.cors.gmbh) in combination with instride AG (https://instride.ch)
 * @license    DDCL
 */

namespace Instride\Bundle\DataDefinitionsBundle\Behat\Service;

class ClassStorage implements ClassStorageInterface
{
    private array $storage = [];

    public function get(string $className): string
    {
        if (!isset($this->storage[$className])) {
            throw new \InvalidArgumentException(sprintf('There is no class name for "%s"!', $className));
        }

        return $this->storage[$className];
    }

    public function has(string $className): bool
    {
        return isset($this->storage[$className]);
    }

    public function set(string $className): string
    {
        $this->storage[$className] = $this->getBehatClassName($className);

        return $this->storage[$className];
    }

    private function getBehatClassName(string $className): string
    {
        return sprintf('Behat%s%s', $className, uniqid());
    }
}

