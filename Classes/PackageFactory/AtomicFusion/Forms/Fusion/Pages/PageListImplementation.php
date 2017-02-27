<?php
namespace PackageFactory\AtomicFusion\Forms\Fusion\Pages;

/**
 * This file is part of the PackageFactory.AtomicFusion.Forms package
 *
 * (c) 2016 Wilhelm Behncke <wilhelm.behncke@googlemail.com>
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Fusion\TypoScriptObjects\AbstractArrayTypoScriptObject;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\PageDefinition;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\PageDefinitionInterface;

/**
 * Fusion object to create lists of page definitions
 */
class PageListImplementation extends AbstractArrayTypoScriptObject
{
    /**
     * Returns itself for later evaluation
     *
     * @return array<PageDefinitionInterface>
     */
    public function evaluate()
    {
        $result = [];

        foreach ($this->properties as $key => $configuration) {
            $pageDefinition = $this->renderPageDefinition($key, $configuration);

            $result[$pageDefinition->getName()] = $pageDefinition;
        }

        return $result;
    }

    /**
     * Render a single form page definition
     *
     * @param string $key
     * @param array $configuration
     * @return PageDefinitionInterface
     */
    protected function renderPageDefinition($key, $configuration)
    {
        if (isset($configuration['__objectType'])) {
            return $this->tsRuntime->render(
                sprintf('%s/%s', $this->path, $key)
            );
        }

        return $this->tsRuntime->render(
            sprintf('%s/%s<PackageFactory.AtomicFusion.Forms:Page>', $this->path, $key)
        );
    }
}
