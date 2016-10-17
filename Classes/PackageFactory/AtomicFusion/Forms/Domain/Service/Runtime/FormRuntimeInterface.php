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
use TYPO3\Flow\Mvc\ActionRequest;
use TYPO3\Flow\Http\Response;
use TYPO3\Flow\Error\Result;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FinisherRuntime;

/**
 * Method definitions for FormRuntime
 */
interface FormRuntimeInterface
{
    /**
     * Get the Form definition
     *
     * @return FormDefinition
     */
    public function getFormDefinition();

    /**
     * Get the request
     *
     * @return ActionRequest
     */
    public function getRequest();

    /**
     * Get the form state
     *
     * @return FormState
     */
    public function getFormState();

    /**
     * Get an argument by path
     *
     * @param string $path
     * @return mixed
     */
    public function getArgument($path);

    /**
     * Get a value by path
     *
     * @param string $path
     * @return mixedn
     */
    public function getValue($path);

    /**
     * Check, whether the form (or page) should be processed
     *
     * @return boolean
     */
    public function shouldProcess();

    /**
     * Process current page
     *
     * @return void
     */
    public function process();

    /**
     * Checks, whether the form (or page) should be validated
     *
     * @return boolean
     */
    public function shouldValidate();

    /**
     * Validate current page
     *
     * @return void
     */
    public function validate();

    /**
     * Checks, whether the processors should be rolled back
     *
     * @return boolean
     */
    public function shouldRollback();

    /**
     * Rollback, if something went wrong
     *
     * @return void
     */
    public function rollback();

    /**
     * Checks, whether the form should be finished
     *
     * @return boolean
     */
    public function shouldFinish();

    /**
     * Finish the form
     *
     * @param Resopnse $parentResponse
     * @return FinisherRuntime
     */
    public function finish(Response $parentResponse);
}
