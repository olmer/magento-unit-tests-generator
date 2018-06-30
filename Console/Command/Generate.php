<?php
declare(strict_types=1);

namespace Olmer\UnitTestsGenerator\Console\Command;

use Symfony\Component\Console\{
    Command\Command,
    Input\InputInterface,
    Input\InputArgument,
    Output\OutputInterface
};
use Magento\Framework\{
    App\State as AppState,
    App\ObjectManagerFactory,
    ObjectManagerInterface,
    App\Area,
    App\AreaList,
    ObjectManager\ConfigLoaderInterface,
    Console\Cli
};
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Store\Model\StoreManager;

use Olmer\UnitTestsGenerator\Code\Generator;

class Generate extends Command
{
    const ARGUMENT_PATH = 'filepath';
    const CONSOLE_COMMAND_NAME = 'dev:tests:generate-unit';
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var ObjectManagerFactory
     */
    private $objectManagerFactory;

    /**
     * AbstractCommand constructor.
     *
     * @param ObjectManagerFactory $objectManagerFactory
     */
    public function __construct(ObjectManagerFactory $objectManagerFactory)
    {
        parent::__construct(null);
        $this->objectManagerFactory = $objectManagerFactory;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName(self::CONSOLE_COMMAND_NAME)
            ->setDescription('Generate unit test structure for defined class');

        $this->addArgument(
            self::ARGUMENT_PATH,
            InputArgument::REQUIRED,
            'Path to file to generate unit tests for'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $path = $input->getArgument(static::ARGUMENT_PATH);
            $result = $this->getModel()->process($path);
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');

            return Cli::RETURN_FAILURE;
        }

        $output->writeln('<info>Unit tests generated</info>');
        $output->writeln("<info>$result</info>");

        return Cli::RETURN_SUCCESS;
    }

    /**
     * @return Generator
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getModel()
    {
        if (!isset($this->model)) {
            $this->model = $this->getObjectManager()->get(Generator::class);
        }

        return $this->model;
    }

    /**
     * @return ObjectManagerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getObjectManager()
    {
        if (null == $this->objectManager) {
            $area = FrontNameResolver::AREA_CODE;
            $params = $_SERVER;
            $params[StoreManager::PARAM_RUN_CODE] = 'admin';
            $params[StoreManager::PARAM_RUN_TYPE] = 'store';
            $this->objectManager = $this->objectManagerFactory->create($params);
            /** @var AppState $appState */
            $appState = $this->objectManager->get(AppState::class);
            $appState->setAreaCode($area);
            $configLoader = $this->objectManager->get(ConfigLoaderInterface::class);
            $this->objectManager->configure($configLoader->load($area));
            /** @var AreaList $areaList */
            $areaList = $this->objectManager->get(AreaList::class);
            /** @var Area $area */
            $area = $areaList->getArea($appState->getAreaCode());
            $area->load(Area::PART_TRANSLATE);
        }

        return $this->objectManager;
    }
}
