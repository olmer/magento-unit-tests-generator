<?php

declare(strict_types=1);

namespace Olmer\UnitTestsGenerator\Code\Generator\UnitTest;

class SetupMethodBuilder
{
    /**
     * @param array $setupMethodParamsDefinition
     * @param string $testObjectCreationCode
     *
     * @return array
     */
    public function build(array $setupMethodParamsDefinition, string $testObjectCreationCode): array
    {
        return [
            'name' => 'setUp',
            'parameters' => [],
            'body' => "\$this->objectManager = new ObjectManager(\$this);\n"
                . \implode("\n", $setupMethodParamsDefinition) . "\n"
                . $testObjectCreationCode,
            'docblock' => [
                'shortDescription' => 'Main set up method',
            ],
            'returnType' => 'void'
        ];
    }
}
