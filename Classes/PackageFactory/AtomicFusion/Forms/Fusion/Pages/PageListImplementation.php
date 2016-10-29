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

use TYPO3\Flow\Annotations as Flow;
use TYPO3\TypoScript\TypoScriptObjects\AbstractArrayTypoScriptObject;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\PageDefinition;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\PageDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\Factory\PageDefinitionFactoryInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\Factory\PageDefinitionMapFactoryInterface;

/**
 * Fusion object to create lists of page definitions
 */
class PageListImplementation extends AbstractArrayTypoScriptObject implements PageDefinitionMapFactoryInterface
{
    /**
     * Returns itself for later evaluation
     *
     * @return PageDefinitionMapFactoryInterface
     */
    public function evaluate()
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function createPageDefinitionMap(FormDefinitionInterface $formDefinition)
    {
        $result = [];

        foreach ($this->properties as $key => $configuration) {
            $pageDefinitionFactory = $this->renderPageDefinitionFactory($key, $configuration);
            $pageDefinition = $pageDefinitionFactory->createPageDefinition($formDefinition);

            $result[$pageDefinition->getName()] = $pageDefinition;
        }

        return $result;
    }

    /**
     * Render a single form page definition factory
     *
     * @param string $key
     * @param array $configuration
     * @return PageDefinitionFactoryInterface
     */
    protected function renderPageDefinitionFactory($key, $configuration)
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
