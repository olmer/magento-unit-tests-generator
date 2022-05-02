<?php
namespace Olmer\UnitTestsGenerator\Test\Unit\Code\Generator\UnitTest;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Olmer\UnitTestsGenerator\Code\Generator\UnitTest\TestObjectCreationBuilder
 */
class TestObjectCreationBuilderTest extends TestCase
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
     * @var \Olmer\UnitTestsGenerator\Code\Generator\UnitTest\TestObjectCreationBuilder
     */
    private $testObject;

    /**
     * Main set up method
     */
    public function setUp() : void
    {
        $this->objectManager = new ObjectManager($this);

        $this->testObject = $this->objectManager->getObject(
            \Olmer\UnitTestsGenerator\Code\Generator\UnitTest\TestObjectCreationBuilder::class,
        );
    }

    /**
     * @return array
     */
    public function dataProviderForTestBuild()
    {
        return [
            'No params' => [
                'objectCreationParams' => [],
                'expectedResult' => "\$this->testObject = \$this->objectManager->getObject(\nFoo::class,\n"
                        . "    [\n\n    ]\n);"
            ],
            'Some params' => [
                'objectCreationParams' => [
                    "        'param1' => \$this->param1,",
                    "        'param2' => \$this->param2,"
                ],
                'expectedResult' => "\$this->testObject = \$this->objectManager->getObject(\nFoo::class,\n"
                    . "    [\n"
                    . "        'param1' => \$this->param1,\n"
                    . "        'param2' => \$this->param2,\n"
                    . "    ]\n);"
            ]
        ];
    }

    /**
     * @dataProvider dataProviderForTestBuild
     */
    public function testBuild(array $objectCreationParams, string $expectedResult)
    {
        $result = $this->testObject->build('Foo', $objectCreationParams);
        $this->assertEquals($expectedResult, $result);
    }
}
