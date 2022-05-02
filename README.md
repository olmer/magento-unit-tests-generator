# Magento 2 module unit tests generator
Sometimes writing new unit test for class with multiple dependencies may be tedious, so this package is intended to simplify magento2 unit tests creation. Command reads source file and generates basic unit test structure for specified class. If unit test already exists - nothing will happen. Test class is placed into test object class' module under app/code/Vendor/Module/Test/Unit/...

### How to install

```bash
composer require olmer/magento-unit-tests-generator --dev
php bin/magento cache:clean
php bin/magento setup:di:compile
```

### How to generate a unit test for a specific class

```bash
php bin/magento dev:tests:generate-unit /app/code/Vendor/Module/path/to/file.php
```

### Examples

Source class
```php
<?php
declare(strict_types=1);

namespace Vendor\Reorder\Helper;

class Reorder
{
    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaFactory $criteria,
        FilterGroupFactory $filterGroup,
        FilterFactory $filter,
        OrderFactory $orderFactory,
        SortOrderFactory $sortOrderFactory
    ) {
        $this->orderRepo = $orderRepository;
        $this->searchCriteriaFactory = $criteria;
        $this->filterGroupFactory = $filterGroup;
        $this->filterFactory = $filter;
        $this->orderFactory = $orderFactory;
        $this->sortOrderFactory = $sortOrderFactory;
    }

    public function getFilename()
    {
        ...
    }
    
    public function getLastShippedOrder()
    {
        ...
    }
}

```
Generated test class
```php
<?php
namespace Vendor\Reorder\Test\Unit\Helper;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \Vendor\Reorder\Helper\Reorder
 */
class ReorderTest extends TestCase
{
    /**
     * Mock context
     *
     * @var \Magento\Framework\App\Helper\Context|PHPUnit\Framework\MockObject\MockObject
     */
    private $context;

    /**
     * Mock orderRepository
     *
     * @var \Magento\Sales\Api\OrderRepositoryInterface|PHPUnit\Framework\MockObject\MockObject
     */
    private $orderRepository;

    /**
     * Mock criteria
     *
     * @var \Magento\Framework\Api\SearchCriteriaFactory|PHPUnit\Framework\MockObject\MockObject
     */
    private $criteria;

    /**
     * Mock filterGroup
     *
     * @var \Magento\Framework\Api\Search\FilterGroupFactory|PHPUnit\Framework\MockObject\MockObject
     */
    private $filterGroup;

    /**
     * Mock filter
     *
     * @var \Magento\Framework\Api\FilterFactory|PHPUnit\Framework\MockObject\MockObject
     */
    private $filter;

    /**
     * Mock orderFactory
     *
     * @var \Magento\Sales\Model\OrderFactory|PHPUnit\Framework\MockObject\MockObject
     */
    private $orderFactory;

    /**
     * Mock sortOrderFactory
     *
     * @var \Magento\Framework\Api\SortOrderFactory|PHPUnit\Framework\MockObject\MockObject
     */
    private $sortOrderFactory;

    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Object to test
     *
     * @var \Vendor\Reorder\Helper\Reorder
     */
    private $testObject;

    /**
     * Main set up method
     */
    public function setUp() : void
    {
        $this->objectManager = new ObjectManager($this);
        $this->context = $this->createMock(\Magento\Framework\App\Helper\Context::class);
        $this->orderRepository = $this->createMock(\Magento\Sales\Api\OrderRepositoryInterface::class);
        $this->criteria = $this->createMock(\Magento\Framework\Api\SearchCriteriaFactory::class);
        $this->filterGroup = $this->createMock(\Magento\Framework\Api\Search\FilterGroupFactory::class);
        $this->filter = $this->createMock(\Magento\Framework\Api\FilterFactory::class);
        $this->orderFactory = $this->createMock(\Magento\Sales\Model\OrderFactory::class);
        $this->sortOrderFactory = $this->createMock(\Magento\Framework\Api\SortOrderFactory::class);
        $this->testObject = $this->objectManager->getObject(
        \Vendor\Reorder\Helper\Reorder::class,
            [
                'context' => $this->context,
                'orderRepository' => $this->orderRepository,
                'criteria' => $this->criteria,
                'filterGroup' => $this->filterGroup,
                'filter' => $this->filter,
                'orderFactory' => $this->orderFactory,
                'sortOrderFactory' => $this->sortOrderFactory,
            ]
        );
    }

    /**
     * @return array
     */
    public function dataProviderForTestGetLastShippedOrder()
    {
        return [
            'Testcase 1' => [
                'prerequisites' => ['param' => 1],
                'expectedResult' => ['param' => 1]
            ]
        ];
    }

    /**
     * @dataProvider dataProviderForTestGetLastShippedOrder
     */
    public function testGetLastShippedOrder(array $prerequisites, array $expectedResult)
    {
        $this->assertEquals($expectedResult['param'], $prerequisites['param']);
    }

    /**
     * @return array
     */
    public function dataProviderForTestIsModuleOutputEnabled()
    {
        return [
            'Testcase 1' => [
                'prerequisites' => ['param' => 1],
                'expectedResult' => ['param' => 1]
            ]
        ];
    }

    /**
     * @dataProvider dataProviderForTestIsModuleOutputEnabled
     */
    public function testIsModuleOutputEnabled(array $prerequisites, array $expectedResult)
    {
        $this->assertEquals($expectedResult['param'], $prerequisites['param']);
    }
}
```
