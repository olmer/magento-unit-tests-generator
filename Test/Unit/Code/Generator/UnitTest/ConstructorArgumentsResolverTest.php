<?php
namespace Olmer\UnitTestsGenerator\Test\Unit\Code\Generator\UnitTest;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \Olmer\UnitTestsGenerator\Code\Generator\UnitTest\ConstructorArgumentsResolver
 */
class ConstructorArgumentsResolverTest extends TestCase
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Object to test
     *
     * @var \Olmer\UnitTestsGenerator\Code\Generator\UnitTest\ConstructorArgumentsResolver
     */
    private $testObject;

    /**
     * @var \ReflectionMethod
     */
    private $constructor;

    /**
     * Main set up method
     */
    public function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->testObject = $this->objectManager->getObject(
        \Olmer\UnitTestsGenerator\Code\Generator\UnitTest\ConstructorArgumentsResolver::class,
        );
        $this->constructor = $this->createMock(\ReflectionMethod::class);
    }

    /**
     * @return array
     */
    public function dataProviderForTestResolve()
    {
        return [
            'Testcase 1' => [
                'constructorParametersData' => [
                    'name1' => 'class1',
                    'name2' => 'class2',
                    'name3' => null
                ],
                'result' => [
                    ['name' => 'name1', 'class' => 'class1'],
                    ['name' => 'name2', 'class' => 'class2']
                ]
            ]
        ];
    }

    /**
     * @dataProvider dataProviderForTestResolve
     */
    public function testResolve(array $constructorParametersData, array $expectedResult)
    {
        $parameters = [];
        foreach ($constructorParametersData as $name => $className) {
            $parameter = $this->createMock(\ReflectionParameter::class);
            $class = null;
            if ($className) {
                $class = $this->createMock(\ReflectionClass::class);
                $class->method('getName')->willReturn($className);
            }
            $parameter->method('getClass')->willReturn($class);
            $parameter->method('getName')->willReturn($name);
            $parameters[] = $parameter;
        }
        $this->constructor->method('getParameters')->willReturn($parameters);
        $result = $this->testObject->resolve($this->constructor);
        $this->assertEquals($expectedResult, $result);
    }
}
