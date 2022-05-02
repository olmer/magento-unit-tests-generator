<?php

declare(strict_types=1);

namespace Olmer\UnitTestsGenerator\Code\Generator\UnitTest;

class TestObjectCreationBuilder
{
    /**
     * @param string $sourceClassName
     * @param array $objectCreationParams
     *
     * @return string
     */
    public function build(
        string $sourceClassName,
        array $objectCreationParams
    ): string {
        return "\$this->testObject = \$this->objectManager->getObject(\n"
            . $sourceClassName . "::class,\n"
            . "    [\n"
            . \implode("\n", $objectCreationParams)
            . "\n    ]\n"
            . ");";
    }
}
