<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Fusion\Validators;

use TYPO3\Flow\Tests\UnitTestCase;
use TYPO3\TypoScript\Core\Runtime as FusionRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Fusion\Validators\ValidatorListImplementation;

class ValidatorListImplementationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createsListsOfValidatorDefinitions()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $validatorListImplementation = new ValidatorListImplementation($fusionRuntime, '', '');

        $validatorDefinition1 = $this->createMock(ValidatorDefinitionInterface::class);
        $validatorDefinition2 = $this->createMock(ValidatorDefinitionInterface::class);

        $this->inject($validatorListImplementation, 'properties', [
            'validator1' => [
                '__objectType' => 'SomeFusionObject'
            ],
            'validator2' => [
                '__objectType' => 'SomeOtherFusionObject'
            ]
        ]);

        $fusionRuntime->expects($this->exactly(2))
            ->method('render')
            ->withConsecutive(['/validator1'], ['/validator2'])
            ->will($this->onConsecutiveCalls(
                $validatorDefinition1,
                $validatorDefinition2
            ));

        $validatorDefinition1->expects($this->once())
            ->method('getName')
            ->willReturn('validatorName1');

        $validatorDefinition2->expects($this->once())
            ->method('getName')
            ->willReturn('validatorName2');

        $validatorDefinitionMap = $validatorListImplementation->evaluate();

        $this->assertSame($validatorDefinition1, $validatorDefinitionMap['validatorName1']);
        $this->assertSame($validatorDefinition2, $validatorDefinitionMap['validatorName2']);
    }

    /**
     * @test
     */
    public function doesNotRequireExplicitFusionObjectAssignmentsForValidators()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $validatorListImplementation = new ValidatorListImplementation($fusionRuntime, '', '');

        $validatorDefinition1 = $this->createMock(ValidatorDefinitionInterface::class);
        $validatorDefinition2 = $this->createMock(ValidatorDefinitionInterface::class);

        $this->inject($validatorListImplementation, 'properties', [
            'validator1' => [],
            'validator2' => []
        ]);

        $fusionRuntime->expects($this->exactly(2))
            ->method('render')
            ->withConsecutive(
                ['/validator1<PackageFactory.AtomicFusion.Forms:Validator>'],
                ['/validator2<PackageFactory.AtomicFusion.Forms:Validator>']
            )
            ->will($this->onConsecutiveCalls(
                $validatorDefinition1,
                $validatorDefinition2
            ));

        $validatorDefinition1->expects($this->once())
            ->method('getName')
            ->willReturn('validatorName1');

        $validatorDefinition2->expects($this->once())
            ->method('getName')
            ->willReturn('validatorName2');

        $validatorDefinitionMap = $validatorListImplementation->evaluate();

        $this->assertSame($validatorDefinition1, $validatorDefinitionMap['validatorName1']);
        $this->assertSame($validatorDefinition2, $validatorDefinitionMap['validatorName2']);
    }
}
