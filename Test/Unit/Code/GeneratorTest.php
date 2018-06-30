<?php
namespace Olmer\UnitTestsGenerator\Test\Unit\Code;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \Olmer\UnitTestsGenerator\Code\Generator
 */
class GeneratorTest extends TestCase
{
    /**
     * Mock testGeneratorInstance
     *
     * @var \Olmer\UnitTestsGenerator\Code\Generator\UnitTest|PHPUnit_Framework_MockObject_MockObject
     */
    private $testGeneratorInstance;

    /**
     * Mock testGenerator
     *
     * @var \Olmer\UnitTestsGenerator\Code\Generator\UnitTestFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $testGenerator;

    /**
     * Mock ioObject
     *
     * @var \Magento\Framework\Code\Generator\Io|PHPUnit_Framework_MockObject_MockObject
     */
    private $ioObject;

    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Object to test
     *
     * @var \Olmer\UnitTestsGenerator\Code\Generator
     */
    private $testObject;

    /**
     * Main set up method
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->testGeneratorInstance = $this->createMock(\Olmer\UnitTestsGenerator\Code\Generator\UnitTest::class);
        $this->testGenerator = $this->createMock(\Olmer\UnitTestsGenerator\Code\Generator\UnitTestFactory::class);
        $this->testGenerator->method('create')->willReturn($this->testGeneratorInstance);
        $this->ioObject = $this->createMock(\Magento\Framework\Code\Generator\Io::class);
        $this->testObject = $this->objectManager->getObject(
        \Olmer\UnitTestsGenerator\Code\Generator::class,
            [
                'testGenerator' => $this->testGenerator,
                'ioObject' => $this->ioObject,
            ]
        );
    }

    /**
     * @return array
     */
    public function dataProviderForTestProcess()
    {
        return [
            'Testcase 1' => [
                'prerequisites' => ['param' => 1],
                'expectedResult' => ['param' => 1]
            ]
        ];
    }

    /**
     * @dataProvider dataProviderForTestProcess
     */
    public function testProcess(array $prerequisites, array $expectedResult)
    {
        $this->assertEquals($expectedResult['param'], $prerequisites['param']);
    }
}
