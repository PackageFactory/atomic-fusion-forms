<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Service\State;

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
use Neos\Flow\Error\Result;

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
     * Add an argument
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function addArgument($name, $value);

    /**
     * Merge new arguments into the existing ones
     *
     * @param array $arguments
     * @return void
     */
    public function mergeArguments(array $arguments);

    /**
     * Get the arguments
     *
     * @return array
     */
	public function getArguments();

    /**
     * Get an argument by path
     *
     * @param string $path
     * @return mixed
     */
    public function getArgument($path);

    /**
     * Set the values
     *
     * @param array $values
     * @return void
     */
    public function setValues(array $values);

    /**
     * Add a value
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function addValue($name, $value);

    /**
     * Get the values
     *
     * @return array
     */
    public function getValues();

    /**
     * Get a value by path
     *
     * @param string $path
     * @return mixedn
     */
    public function getValue($path);

    /**
     * Get the validation result
     *
     * @return Result
     */
    public function getValidationResult();

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
