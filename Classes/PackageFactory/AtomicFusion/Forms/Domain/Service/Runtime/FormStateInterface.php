<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime;

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

/**
 * Method definitions for the form state
 */
interface FormStateInterface
{
    /**
     * Set the arguments
     *
     * @param array $arguments
     * @return void
     */
	public function setArguments(array $arguments);

    /**
     * Get the arguments
     *
     * @return array
     */
	public function getArguments();

    /**
     * Set the current page
     *
     * @param string $pageIdentifier
     * @return void
     */
	public function setCurrentPage($pageIdentifier);

    /**
     * Get the current page
     *
     * @return string
     */
	public function getCurrentPage();

    /**
     * Check if the given page is the current page
     *
     * @param string  $pageIdentifier
     * @return boolean
     */
	public function isCurrentPage($pageIdentifier);

	/**
	 * Determine whether the form is initially called
	 *
	 * @return boolean
	 */
	public function isInitialCall();
}
