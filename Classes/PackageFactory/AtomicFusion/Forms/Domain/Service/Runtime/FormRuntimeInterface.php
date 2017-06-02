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

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Http\Response;
use Neos\Error\Messages\Result;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FormStateInterface;

/**
 * Method definitions for FormRuntime
 */
interface FormRuntimeInterface
{
    /**
     * Get the Form definition
     *
     * @return FormDefinitionInterface
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
     * @return FormStateInterface
     */
    public function getFormState();

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
     * @param Response $parentResponse
     * @return Response
     */
    public function finish(Response $parentResponse);
}
