<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Service\Resolver;

use TYPO3\Flow\Tests\UnitTestCase;
use TYPO3\Flow\ObjectManagement\ObjectManagerInterface;
use TYPO3\Flow\Reflection\ReflectionService;
use TYPO3\Flow\Validation\ValidatorResolver as FlowValidatorResolver;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Resolver\ValidatorResolver;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Validators\ValidatorInterface;

class ValidatorResolverTest extends UnitTestCase
{
    const RESOLVER_TARGET_CLASS = ValidatorInterface::class;

    /**
     * @test
     */
    public function deliversValidatorObjectAccordingToValidatorDefinition()
    {
        $flowValidatorResolver = $this->createMock(FlowValidatorResolver::class);
        $flowValidatorResolver->method('createValidator')->with('SomeValidator', ['SomeOption'])
            ->willReturn('TheValidator');

        $validatorDefinition = $this->createMock(ValidatorDefinitionInterface::class);
        $validatorDefinition->method('getImplementationClassName')->willReturn('SomeValidator');
        $validatorDefinition->method('getOptions')->willReturn(['SomeOption']);

        $validatorResolver = new ValidatorResolver();
        $this->inject($validatorResolver, 'flowValidatorResolver', $flowValidatorResolver);

        $this->assertEquals('TheValidator',  $validatorResolver->resolve($validatorDefinition));
    }

    /**
     * @test
     * @expectedException \PackageFactory\AtomicFusion\Forms\Domain\Exception\ResolverException
     * @expectedExceptionCode 1476602082
     * @expectedExceptionMessage Error in ValidatorResolver: Forwarded Exception
     */
    public function forwardsExceptionsFromFlowValidatorResolver()
    {
        $flowValidatorResolver = $this->createMock(FlowValidatorResolver::class);
        $flowValidatorResolver->method('createValidator')->with('SomeValidator', [])
            ->will($this->throwException(new \Exception('Forwarded Exception')));

        $validatorDefinition = $this->createMock(ValidatorDefinitionInterface::class);
        $validatorDefinition->method('getImplementationClassName')->willReturn('SomeValidator');
        $validatorDefinition->method('getOptions')->willReturn([]);

        $validatorResolver = new ValidatorResolver();
        $this->inject($validatorResolver, 'flowValidatorResolver', $flowValidatorResolver);

        $validatorResolver->resolve($validatorDefinition);
    }
}
