<?php

declare(strict_types=1);

namespace Olmer\UnitTestsGenerator\Code\Generator\UnitTest;

class ConstructorArgumentsResolver
{
    /**
     * @param \ReflectionMethod $constructor
     *
     * @return array
     */
    public function resolve(\ReflectionMethod $constructor): array
    {
        $constructorArguments = [];

        try {
            foreach ($constructor->getParameters() as $parameter) {
                if (!$parameter->getType()) {
                    continue;
                }
                $constructorArguments[] = [
                    'name' => $parameter->getName(),
                    'class' => $parameter->getType()->getName()
                ];
            }
        } catch (\ReflectionException $e) {
            return $constructorArguments;
        }

        return $constructorArguments;
    }
}
