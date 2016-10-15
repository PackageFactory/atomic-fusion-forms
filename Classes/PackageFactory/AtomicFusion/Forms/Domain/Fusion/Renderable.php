<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Fusion;

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
use TYPO3\TypoScript\Core\Runtime as FusionRuntime;

class Renderable implements FusionAwareInterface
{
    use FusionAwareTrait;

    /**
     * @var string
     */
    protected $path;

    /**
     * Constructor
     *
     * @param string $path
     */
    public function __construct($path)
    {
        if (!$this->fusionRuntime->canRender($path)) {
            throw new \Exception(sprintf('Cannot render path `%s`.', $path), 1476531111);
        }

        $this->path = $path;
    }

    /**
     * Render the given fusion path
     *
     * @return string
     */
    public function render()
    {
        return $this->fusionRuntime->render($this->path);
    }
}
