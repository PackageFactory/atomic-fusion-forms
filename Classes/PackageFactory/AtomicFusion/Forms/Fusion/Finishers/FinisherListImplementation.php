<?php
namespace PackageFactory\AtomicFusion\Forms\Fusion\Finishers;

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
use TYPO3\TypoScript\TypoScriptObjects\AbstractArrayTypoScriptObject;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FinisherDefinitionInterface;

/**
 * Fusion object to create lists of finisher definitions
 */
class FinisherListImplementation extends AbstractArrayTypoScriptObject
{
    /**
     * Returns a list of finisher definitions
     *
     * @return array<FinisherDefinitionInterface>
     */
    public function evaluate()
    {
        $result = [];

        foreach (array_keys($this->properties) as $key) {
            $finisherDefinition = $this->renderFinisherDefinition($key);

            $result[$finisherDefinition->getName()] = $finisherDefinition;
        }

        return $result;
    }

    /**
     * Render a single finisher definition
     *
     * @param string $key
     * @param array $configuration
     * @return FinisherDefinitionInterface
     */
    protected function renderFinisherDefinition($key)
    {
        return $this->tsRuntime->render(
            sprintf('%s/%s', $this->path, $key)
        );
    }
}
