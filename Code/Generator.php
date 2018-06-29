<?php

declare(strict_types=1);

namespace Olmer\UnitTestsGenerator\Code;

use Magento\Framework\{
    Filesystem\Driver\File,
    Code\Generator\Io
};

class Generator
{
    /**
     * @var Generator\UnitTestFactory
     */
    private $testGeneratorFactory;
    /**
     * @var File
     */
    private $driver;
    /**
     * @var \Magento\Framework\Code\Generator\Io
     */
    private $ioObject;

    /**
     * Generator constructor.
     *
     * @param Generator\UnitTestFactory $testGenerator
     */
    public function __construct(
        Generator\UnitTestFactory $testGenerator,
        File $driver,
        Io $ioObject
    ) {
        $this->testGeneratorFactory = $testGenerator;
        $this->driver = $driver;
        $this->ioObject = $ioObject;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function process(string $path)
    {
        $classes = \get_declared_classes();

        include $path;

        $diff = \array_diff(get_declared_classes(), $classes);
        $sourceClass = \reset($diff);

        $resultClass = \explode('\\', $sourceClass);
        \array_splice($resultClass, 2, 0, 'Test\\Unit');
        $resultClass = \implode('\\', $resultClass) . 'Test';

        if (!\class_exists($sourceClass) || \class_exists($resultClass)) {
            return null;
        }

        $generator = $this->testGeneratorFactory->create([
            'sourceClassName' => $sourceClass,
            'resultClassName' => $resultClass,
            'ioObject' => $this->ioObject
        ]);

        return $generator->generate();
    }
}
