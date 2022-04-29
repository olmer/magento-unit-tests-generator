<?php

declare(strict_types=1);

namespace Olmer\UnitTestsGenerator\Code\Generator\UnitTest;

class ConstructorArgumentsResolver
{
    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return array
     */
    public function resolve(\ReflectionClass $reflectionClass): array
    {
        $constructorArguments = [];

        try {
            $method = $reflectionClass->getMethod('__construct');
            foreach ($method->getParameters() as $parameter) {
                if (!$parameter->getClass()) {
                    continue;
                }
                $constructorArguments[] = [
                    'name' => $parameter->getName(),
                    'class' => $parameter->getClass()->getName()
                ];
            }
        } catch (\ReflectionException $e) {
            return $constructorArguments;
        }

        return $constructorArguments;
    }
}
