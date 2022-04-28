<?php
namespace Olmer\UnitTestsGenerator\Test\Unit\Code\Generator;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \Olmer\UnitTestsGenerator\Code\Generator\ClassNameParser
 */
class ClassNameParserTest extends TestCase
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
     * @var \Olmer\UnitTestsGenerator\Code\Generator\ClassNameParser
     */
    private $testObject;

    /**
     * Main set up method
     */
    public function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->testObject = $this->objectManager->getObject(
        \Olmer\UnitTestsGenerator\Code\Generator\ClassNameParser::class
        );
    }

    /**
     * @return array
     */
    public function dataProviderForTestParseClassName()
    {
        return [
            'basic Magento class' => [
                'content' => '<?php' . PHP_EOL . 'declare(strict_types=1);' . PHP_EOL . 'namespace Olmer\UnitTestsGenerator\Code\Generator;class ClassNameParser{}',
                'expectedResult' => '\Olmer\UnitTestsGenerator\Code\Generator\ClassNameParser'
            ]
        ];
    }

    /**
     * @dataProvider dataProviderForTestParseClassName
     *
     * @param string $content
     * @param string $expected
     *
     * @return void
     */
    public function testParseClassName(string $content, string $expected)
    {
        $result = $this->testObject->parseClassName($content);
        $this->assertEquals($expected, $result);
    }
}
