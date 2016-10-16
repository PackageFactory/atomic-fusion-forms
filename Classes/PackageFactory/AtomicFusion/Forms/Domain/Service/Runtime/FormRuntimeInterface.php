<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Service;

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
use TYPO3\Flow\Error\Result;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\FinisherRuntime;

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
     * Process current page
     *
     * @return void
     */
    public function process();

    /**
     * Validate current page
     *
     * @return void
     */
    public function validate();

    /**
     * Rollback, if something went wrong
     *
     * @return void
     */
    public function rollback();


    /**
     * Finish the form
     *
     * @return FinisherRuntime
     */
    public function finish();
}
