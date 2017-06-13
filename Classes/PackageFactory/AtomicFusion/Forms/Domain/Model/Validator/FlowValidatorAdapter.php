<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Model\Validator;

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
use Neos\Flow\Validation\ValidatorResolver as FlowValidatorResolver;
use PackageFactory\AtomicFusion\Forms\Domain\Exception\ResolverException;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Validator\ValidatorInterface;

/**
 * Defines methods for validators
 */
class FlowValidatorAdapter implements ValidatorInterface
{
    /**
     * @Flow\Inject
     * @var FlowValidatorResolver
     */
    protected $flowValidatorResolver;

    /**
     * @var string
     */
    protected $validator;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the validator name
     *
     * @param string $validator
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;
    }

    /**
     * Detect all propety setters and store the data in the options to pass
     * them to the flow validator later on
     *
     * @param $name
     * @param $arguments
     * @throws ResolverException
     */
    public function __call($functionName, $arguments) {
        if (strpos($functionName, 'set') === 0 && count($arguments) == 1){
            $propertyName = lcfirst(substr($functionName, 3));
            $this->options[$propertyName] = $arguments[0];
        } else {
            throw new ResolverException(
                sprintf('Error: call to undefined method %s in ValidatorResolver', $functionName),
                1497337932
            );
        }
    }

    /**
     * Checks if the given value is valid according to the validator, and returns
     * the Error Messages object which occurred.
     *
     * @param mixed $value The value that should be validated
     * @return ErrorResult
     * @api
     */
    public function validate($value) {
        $this->validator = $this->resolveValidator($this->validator, $this->options);
        return $this->validator->validate($value);
    }

    /**
     * Resolve and return the given flow validator
     *
     * @param string $className
     * @param mixed $options
     * @return ValidatorInterface
     * @throws ResolverException
     */
    public function resolveValidator(string $className, $options)
    {
        try {
            return $this->flowValidatorResolver->createValidator($className, $options);
        } catch (\Exception $e) {
            throw new ResolverException(
                sprintf('Error in ValidatorResolver: %s', $e->getMessage()),
                1476602082,
                $e
            );
        }
    }
}
