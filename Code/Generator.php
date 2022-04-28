<?php

declare(strict_types=1);

namespace Olmer\UnitTestsGenerator\Code;

use Magento\Framework\{
    Code\Generator\Io,
    Filesystem\Driver\File as Reader
};
use Olmer\UnitTestsGenerator\Code\Generator\ClassNameParser;

class Generator
{
    /**
     * @var Generator\UnitTestFactory
     */
    private $testGeneratorFactory;
    /**
     * @var \Magento\Framework\Code\Generator\Io
     */
    private $ioObject;
    /**
     * @var Reader
     */
    private $reader;
    /**
     * @var ClassNameParser
     */
    private $classNameParser;

    /**
     * Generator constructor.
     *
     * @param Generator\UnitTestFactory $testGenerator
     * @param Io $ioObject
     * @param Reader $reader
     */
    public function __construct(
        Generator\UnitTestFactory $testGenerator,
        Io $ioObject,
        Reader $reader,
        ClassNameParser $classNameParser
    ) {
        $this->testGeneratorFactory = $testGenerator;
        $this->ioObject = $ioObject;
        $this->reader = $reader;
        $this->classNameParser = $classNameParser;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function process(string $path)
    {
        $sourceClass = $this->getClassName($path);
        if (!\class_exists($sourceClass)) {
            return null;
        }

        $resultClass = \explode('\\', trim($sourceClass, '\\'));
        \array_splice($resultClass, 2, 0, 'Test\\Unit');
        $resultClass = \implode('\\', $resultClass) . 'Test';
        if (\class_exists($resultClass)) {
            return null;
        }
        $generator = $this->testGeneratorFactory->create([
            'sourceClassName' => $sourceClass,
            'resultClassName' => $resultClass,
            'ioObject'        => $this->ioObject,
        ]);

        return $generator->generate();
    }

    /**
     * @param string $path
     *
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getClassName(string $path): string
    {
        if (\class_exists($path)) {
            return $path;
        }

        $fileContents = $this->reader->fileGetContents($path);
        return $this->classNameParser->parseClassName($fileContents);
    }
}
