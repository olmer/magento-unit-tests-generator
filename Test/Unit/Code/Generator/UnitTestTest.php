<?php
namespace Olmer\UnitTestsGenerator\Test\Unit\Code\Generator;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @covers \Olmer\UnitTestsGenerator\Code\Generator\UnitTest
 */
class UnitTestTest extends TestCase
{
    /**
     * Mock ioObject
     *
     * @var \Magento\Framework\Code\Generator\Io|PHPUnit_Framework_MockObject_MockObject
     */
    private $ioObject;

    /**
     * Mock classGenerator
     *
     * @var \Magento\Framework\Code\Generator\CodeGeneratorInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $classGenerator;

    /**
     * Mock definedClasses
     *
     * @var \Magento\Framework\Code\Generator\DefinedClasses|PHPUnit_Framework_MockObject_MockObject
     */
    private $definedClasses;

    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Object to test
     *
     * @var \Olmer\UnitTestsGenerator\Code\Generator\UnitTest
     */
    private $testObject;

    /**
     * Main set up method
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->ioObject = $this->createMock(\Magento\Framework\Code\Generator\Io::class);
        $this->classGenerator = $this->createMock(\Magento\Framework\Code\Generator\CodeGeneratorInterface::class);
        $this->definedClasses = $this->createMock(\Magento\Framework\Code\Generator\DefinedClasses::class);
        $this->testObject = $this->objectManager->getObject(
        \Olmer\UnitTestsGenerator\Code\Generator\UnitTest::class,
            [
                'ioObject' => $this->ioObject,
                'classGenerator' => $this->classGenerator,
                'definedClasses' => $this->definedClasses,
            ]
        );
    }

    /**
     * @return array
     */
    public function dataProviderForTestGenerate()
    {
        return [
            'Testcase 1' => [
                'prerequisites' => ['param' => 1],
                'expectedResult' => ['param' => 1]
            ]
        ];
    }

    /**
     * @dataProvider dataProviderForTestGenerate
     */
    public function testGenerate(array $prerequisites, array $expectedResult)
    {
        $this->assertEquals($expectedResult['param'], $prerequisites['param']);
    }

    /**
     * @return array
     */
    public function dataProviderForTestGetErrors()
    {
        return [
            'Testcase 1' => [
                'prerequisites' => ['param' => 1],
                'expectedResult' => ['param' => 1]
            ]
        ];
    }

    /**
     * @dataProvider dataProviderForTestGetErrors
     */
    public function testGetErrors(array $prerequisites, array $expectedResult)
    {
        $this->assertEquals($expectedResult['param'], $prerequisites['param']);
    }

    /**
     * @return array
     */
    public function dataProviderForTestGetSourceClassName()
    {
        return [
            'Testcase 1' => [
                'prerequisites' => ['param' => 1],
                'expectedResult' => ['param' => 1]
            ]
        ];
    }

    /**
     * @dataProvider dataProviderForTestGetSourceClassName
     */
    public function testGetSourceClassName(array $prerequisites, array $expectedResult)
    {
        $this->assertEquals($expectedResult['param'], $prerequisites['param']);
    }

    /**
     * @return array
     */
    public function dataProviderForTestGetSourceClassNameWithoutNamespace()
    {
        return [
            'Testcase 1' => [
                'prerequisites' => ['param' => 1],
                'expectedResult' => ['param' => 1]
            ]
        ];
    }

    /**
     * @dataProvider dataProviderForTestGetSourceClassNameWithoutNamespace
     */
    public function testGetSourceClassNameWithoutNamespace(array $prerequisites, array $expectedResult)
    {
        $this->assertEquals($expectedResult['param'], $prerequisites['param']);
    }

    /**
     * @return array
     */
    public function dataProviderForTestInit()
    {
        return [
            'Testcase 1' => [
                'prerequisites' => ['param' => 1],
                'expectedResult' => ['param' => 1]
            ]
        ];
    }

    /**
     * @dataProvider dataProviderForTestInit
     */
    public function testInit(array $prerequisites, array $expectedResult)
    {
        $this->assertEquals($expectedResult['param'], $prerequisites['param']);
    }
}
