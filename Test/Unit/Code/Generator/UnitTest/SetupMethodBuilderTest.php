<?php
namespace Olmer\UnitTestsGenerator\Test\Unit\Code\Generator\UnitTest;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Olmer\UnitTestsGenerator\Code\Generator\UnitTest\SetupMethodBuilder
 */
class SetupMethodBuilderTest extends TestCase
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
     * @var \Olmer\UnitTestsGenerator\Code\Generator\UnitTest\SetupMethodBuilder
     */
    private $testObject;

    /**
     * Main set up method
     */
    public function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->testObject = $this->objectManager->getObject(
            \Olmer\UnitTestsGenerator\Code\Generator\UnitTest\SetupMethodBuilder::class
        );
    }

    /**
     * @return array
     */
    public function dataProviderForTestBuild()
    {
        return [
            'No params, simple test object' => [
                'setupMethodParamsDefinition' => [],
                'testObjectCreationCode' => "\$this->testObject = \$this->objectManager->getObject(\nTestObject::class,\n"
                    . "    [\n    ]\n);",
                'expectedResult' => [
                    'name' => 'setUp',
                    'body' => "\$this->objectManager = new ObjectManager(\$this);\n\n"
                        . "\$this->testObject = \$this->objectManager->getObject(\nTestObject::class,\n"
                        . "    [\n    ]\n);",
                    'parameters' => [],
                    'docblock' => [
                        'shortDescription' => 'Main set up method',
                    ],
                    'returnType' => 'void'
                ]
            ],
            'Some params, simple test object' => [
                'setupMethodParamsDefinition' => [
                    "\$this->fooFactory = \$this->createMock(FooFactory::class);",
                    "\$this->fooFactory->method('create')->willReturn(\$this->createMock(Foo::class);"
                ],
                'testObjectCreationCode' => "\$this->testObject = \$this->objectManager->getObject(\nTestObject::class,\n"
                    . "    [\n    ]\n);",
                'expectedResult' => [
                    'name' => 'setUp',
                    'body' => "\$this->objectManager = new ObjectManager(\$this);\n"
                        . "\$this->fooFactory = \$this->createMock(FooFactory::class);\n"
                        . "\$this->fooFactory->method('create')->willReturn(\$this->createMock(Foo::class);\n"
                        . "\$this->testObject = \$this->objectManager->getObject(\nTestObject::class,\n"
                        . "    [\n    ]\n);",
                    'parameters' => [],
                    'docblock' => [
                        'shortDescription' => 'Main set up method',
                    ],
                    'returnType' => 'void'
                ]
            ]
        ];
    }

    /**
     * @dataProvider dataProviderForTestBuild
     */
    public function testBuild(array $setupMethodParamsDefinition, string $testObjectCreationCode, array $expectedResult)
    {
        $result = $this->testObject->build($setupMethodParamsDefinition, $testObjectCreationCode);
        $this->assertEquals($expectedResult, $result);
    }
}
