<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Fusion\Validators;

use Neos\Flow\Tests\UnitTestCase;
use Neos\Fusion\Core\Runtime as FusionRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Fusion\Validators\ValidatorCollectionImplementation;

class ValidatorCollectionImplementationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createsListsOfValidatorDefinitions()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $validatorListImplementation = new ValidatorCollectionImplementation($fusionRuntime, '', '');

        $validatorDefinition1 = $this->createMock(ValidatorDefinitionInterface::class);
        $validatorDefinition2 = $this->createMock(ValidatorDefinitionInterface::class);

        $fusionRuntime->method('getCurrentContext')->willReturn([]);

        $fusionRuntime->expects($this->exactly(4))
            ->method('evaluate')
            ->withConsecutive(
                ['/collection', $validatorListImplementation],
                ['/itemName', $validatorListImplementation],
                ['/validatorRenderer', $validatorListImplementation],
                ['/validatorRenderer', $validatorListImplementation]
            )
            ->will($this->onConsecutiveCalls(
                ['Item1', 'Item2'],
                'TheItemName',
                $validatorDefinition1,
                $validatorDefinition2
            ));

        $fusionRuntime->expects($this->exactly(2))
            ->method('pushContextArray')
            ->withConsecutive(
                [['TheItemName' => 'Item1']],
                [['TheItemName' => 'Item2']]
            );

        $fusionRuntime->expects($this->exactly(2))->method('popContext');

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
