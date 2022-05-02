<?php

declare(strict_types=1);

namespace Olmer\UnitTestsGenerator\Code\Generator;

use Magento\Framework\Code\Generator\DefinedClasses;
use Magento\Framework\Code\Generator\Io;
use \Magento\Framework\Code\Generator\CodeGeneratorInterface;
use Olmer\UnitTestsGenerator\Code\Generator\UnitTest\ConstructorArgumentsResolver;
use Olmer\UnitTestsGenerator\Code\Generator\UnitTest\SetupMethodBuilder;
use Olmer\UnitTestsGenerator\Code\Generator\UnitTest\TestObjectCreationBuilder;

class UnitTest extends \Magento\Framework\Code\Generator\EntityAbstract
{
    /**
     * Entity type
     */
    const ENTITY_TYPE = 'unitTest';
    /**
     * @var \ReflectionClass
     */
    private $sourceReflectionClass;
    /**
     * @var array
     */
    private $constructorArguments;
    /**
     * @var ConstructorArgumentsResolver
     */
    private $constructorArgumentsResolver;
    /**
     * @var SetupMethodBuilder
     */
    private $setupMethodBuilder;
    /**
     * @var TestObjectCreationBuilder
     */
    private $testObjectCreationBuilder;

    public function __construct(
        ConstructorArgumentsResolver $constructorArgumentsResolver,
        SetupMethodBuilder $setupMethodBuilder,
        TestObjectCreationBuilder $testObjectCreationBuilder,
        $sourceClassName = null,
        $resultClassName = null,
        Io $ioObject = null,
        CodeGeneratorInterface $classGenerator = null,
        DefinedClasses $definedClasses = null
    ) {
        $this->constructorArgumentsResolver = $constructorArgumentsResolver;
        $this->setupMethodBuilder = $setupMethodBuilder;
        $this->testObjectCreationBuilder = $testObjectCreationBuilder;
        parent::__construct($sourceClassName, $resultClassName, $ioObject, $classGenerator, $definedClasses);
    }

    /**
     * @return \ReflectionClass
     * @throws \ReflectionException
     */
    private function getSourceReflectionClass(): \ReflectionClass
    {
        if ($this->sourceReflectionClass === null) {
            $this->sourceReflectionClass = new \ReflectionClass($this->getSourceClassName());
        }
        return $this->sourceReflectionClass;
    }

    /**
     * @return array
     */
    protected function _getClassDocBlock()
    {
        $description = '@covers ' . $this->getSourceClassName();
        return ['shortDescription' => $description];
    }

    /**
     * Retrieve class properties
     *
     * @return array
     */
    protected function _getClassProperties()
    {
        $properties = array_map(function($e) {
            return [
                'name' => $e['name'],
                'visibility' => 'private',
                'docblock' => [
                    'shortDescription' => "Mock {$e['name']}",
                    'tags' => [['name' => 'var', 'description' => '\\' . "{$e['class']}|" . \PHPUnit\Framework\MockObject\MockObject::class]],
                ],
            ];
        }, $this->getConstructorArgumentsWithFactoryInstances($this->getConstructorArguments()));

        $properties[] = [
            'name' => 'objectManager',
            'visibility' => 'private',
            'docblock' => [
                'shortDescription' => 'Object Manager instance',
                'tags' => [['name' => 'var', 'description' => '\\' . \Magento\Framework\ObjectManagerInterface::class]],
            ],
        ];

        $properties[] = [
            'name' => 'testObject',
            'visibility' => 'private',
            'docblock' => [
                'shortDescription' => 'Object to test',
                'tags' => [['name' => 'var', 'description' => $this->getSourceClassName()]],
            ],
        ];

        return $properties;
    }

    /**
     * Get default constructor definition for generated class
     *
     * @return array
     */
    protected function _getDefaultConstructorDefinition()
    {
        return [];
    }

    /**
     * Returns list of methods for class generator
     *
     * @return array
     */
    protected function _getClassMethods()
    {
        $setupMethodParamsDefinition = $this->getSetupMethodParamsDefinition(
            $this->getConstructorArgumentsWithFactoryInstances(
                $this->getConstructorArguments()
            )
        );
        $testObjectCreationCode = $this->testObjectCreationBuilder->build(
            $this->getSourceClassName(),
            $this->getObjectCreationParams($this->getConstructorArguments())
        );
        $setUp = $this->setupMethodBuilder->build($setupMethodParamsDefinition, $testObjectCreationCode);

        return \array_merge([$setUp], $this->getTestMethodsWithProviders());
    }

    /**
     * @return string
     */
    protected function _generateCode()
    {
        $this->addDefaultUses();
        $this->addExtends();

        return parent::_generateCode();
    }

    /**
     * @param array $arguments
     *
     * @return array
     */
    private function getSetupMethodParamsDefinition(array $arguments): array
    {
        $result = [];
        foreach ($arguments as $argument) {
            $result[] = "\$this->{$argument['name']}"
                . " = \$this->createMock(\\" . "{$argument['class']}::class);";
            if (\preg_match('/\w+Factory$/', $argument['class']) === 1) {
                $instanceName = $argument['name'] . 'Instance';
                $result[] = "\$this->{$argument['name']}"
                    . "->method('create')"
                    . "->willReturn(\$this->$instanceName);";
            }
        }
        return $result;
    }

    /**
     * @param string $sourceClassName
     * @param array $objectCreationParams
     *
     * @return string
     */
    private function getTestObjectCreation(
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

    /**
     * @param array $constructorArguments
     *
     * @return array
     */
    private function getObjectCreationParams(array $constructorArguments): array
    {
        return \array_map(function ($e) {
            return "        '{$e['name']}' => \$this->{$e['name']},";
        }, $constructorArguments);
    }

    /**
     * @return array
     */
    private function getConstructorArguments(): array
    {
        if ($this->constructorArguments === null) {
            try {
                $constructor = $this->getSourceReflectionClass()->getMethod('__construct');
                $this->constructorArguments = $this->constructorArgumentsResolver->resolve($constructor);
            } catch (\ReflectionException $e) {
                $this->constructorArguments = [];
            }
        }

        return $this->constructorArguments;
    }

    /**
     * @param array $arguments
     *
     * @return array
     */
    private function getConstructorArgumentsWithFactoryInstances(array $arguments): array
    {
        $result = [];
        foreach ($arguments as $argument) {
            if (\preg_match('/\w+Factory$/', $argument['class']) === 1) {
                $result[] = [
                    'name' => $argument['name'] . 'Instance',
                    'class' => \substr($argument['class'], 0, -7)
                ];
            }
            $result[] = $argument;
        }
        return $result;
    }

    /**
     * @return array
     */
    private function getTestMethodsWithProviders(): array
    {
        $methods = [];
        try {
            $publicMethods = $this->getSourceReflectionClass()->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($publicMethods as $method) {
                if (!($method->isConstructor() || $method->isFinal() || $method->isStatic() || $method->isDestructor())
                    && !in_array($method->getName(), ['__sleep', '__wakeup', '__clone'])
                ) {
                    $methods[] = $this->getDataProvider($method);
                    $methods[] = $this->getTestMethod($method);
                }
            }
        } catch (\ReflectionException $e) {
            return $methods;
        }

        return $methods;
    }

    /**
     * Retrieve method info
     *
     * @param \ReflectionMethod $method
     * @return array
     */
    private function getTestMethod(\ReflectionMethod $method)
    {
        $testMethodName = 'test' . \ucfirst($method->getName());
        $methodInfo = [
            'name' => $testMethodName,
            'parameters' => [
                [
                    'name' => 'prerequisites',
                    'type' => 'array',
                ],
                [
                    'name' => 'expectedResult',
                    'type' => 'array',
                ],
            ],
            'body' => "\$this->assertEquals(\$expectedResult['param'], \$prerequisites['param']);",
            'docblock' => [
                'tags' => [['name' => 'dataProvider', 'description' => 'dataProviderFor' . \ucfirst($testMethodName)]],
            ],
        ];

        return $methodInfo;
    }

    /**
     * Retrieve method info
     *
     * @param \ReflectionMethod $method
     * @return array
     */
    private function getDataProvider(\ReflectionMethod $method): array
    {
        $testMethodName = 'test' . \ucfirst($method->getName());
        $methodInfo = [
            'name' => 'dataProviderFor' . \ucfirst($testMethodName),
            'body' => $this->getDataProviderBody(),
            'docblock' => [
                'tags' => [['name' => 'return', 'description' => 'array']],
            ],
        ];

        return $methodInfo;
    }

    /**
     * @return string
     */
    private function getDataProviderBody(): string
    {
        return <<<BODY
return [
    'Testcase 1' => [
        'prerequisites' => ['param' => 1],
        'expectedResult' => ['param' => 1]
    ]
];
BODY;
    }

    /**
     * add uses, which are needed for unit testing
     */
    private function addDefaultUses()
    {
        $this->_classGenerator->addUse(
            \Magento\Framework\TestFramework\Unit\Helper\ObjectManager::class
        );
        $this->_classGenerator->addUse(
            \PHPUnit\Framework\TestCase::class
        );
        $this->_classGenerator->addUse(
            \PHPUnit\Framework\MockObject\MockObject::class
        );
    }

    /**
     * add extends from testcase class
     */
    private function addExtends()
    {
        $this->_classGenerator->setExtendedClass(\PHPUnit\Framework\TestCase::class);
    }

    /**
     * @param string $sourceCode
     *
     * @return string
     */
    protected function _fixCodeStyle($sourceCode)
    {
        $sourceCode = parent::_fixCodeStyle($sourceCode);
        $sourceCode = preg_replace('/(\sprivate\s\$\w+)\s\=\snull\;/', '$1;', $sourceCode);
        return $sourceCode;
    }
}
