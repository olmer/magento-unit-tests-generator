<?php

declare(strict_types=1);

namespace Olmer\UnitTestsGenerator\Code;

use Magento\Framework\{
    Code\Generator\Io,
    Filesystem\Driver\File as Reader
};

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
     * Generator constructor.
     *
     * @param Generator\UnitTestFactory $testGenerator
     * @param Io $ioObject
     * @param Reader $reader
     */
    public function __construct(
        Generator\UnitTestFactory $testGenerator,
        Io $ioObject,
        Reader $reader
    ) {
        $this->testGeneratorFactory = $testGenerator;
        $this->ioObject = $ioObject;
        $this->reader = $reader;
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
        return $this->parseClassName($fileContents);
    }

    /**
     * @param string $content
     *
     * @return string
     */
    private function parseClassName(string $content): string
    {
        $class = $namespace = '';
        $i = 0;
        $tokens = \token_get_all($content);
        $tokensCount = \count($tokens);
        for (; $i < $tokensCount; $i++) {
            if ($tokens[$i][0] === T_NAMESPACE) {
                for ($j = $i + 1; $j < $tokensCount; $j++) {
                    if ($tokens[$j][0] === T_STRING) {
                        $namespace .= '\\' . $tokens[$j][1];
                    } else {
                        if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                            break;
                        }
                    }
                }
            }
            if ($tokens[$i][0] === T_CLASS) {
                for ($j = $i + 1; $j < $tokensCount; $j++) {
                    if ($tokens[$j] === '{') {
                        $class = '\\' . $tokens[$i + 2][1];
                        break 2;
                    }
                }
            }
        }
        return $namespace . $class;
    }
}
