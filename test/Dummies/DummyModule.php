<?php

namespace Framework\RestApi\Test\Dummies;

use Framework\Base\Module\BaseModule;
use Framework\Base\Test\Dummies\TestRepository;
use Framework\RestApi\RestApiApplicationInterface;
use Framework\RestApi\RestApiConfigurationInterface;

/**
 * Class DummyModule
 * @package Framework\RestApi\Test\Dummies
 */
class DummyModule extends BaseModule
{
    /**
     *
     */
    public function loadConfig()
    {
        // Let's read all files from module config folder and set to Configuration
        $configFile = realpath(dirname(__FILE__)) . '/dummy_module.php';

        $this->getApplication()
             ->getConfiguration()
             ->readFromPhp($configFile);
    }

    /**
     * @inheritdoc
     */
    public function bootstrap()
    {
        /**
         * @var RestApiApplicationInterface $app
         */
        $app = $this->getApplication();
        /**
         * @var RestApiConfigurationInterface $config
         */
        $config = $app->getConfiguration();

        // Format models configuration
        $modelsConfiguration = $this->generateModelsConfiguration(
            $config->getPathValue('models')
        );

        $repositoryManager = $app->getRepositoryManager();

        // Register resources, repositories and model fields
        $repositoryManager->registerResources($modelsConfiguration['resources'])
                          ->registerModelFields($modelsConfiguration['modelFields']);
    }

    /**
     * @param $modelsConfig
     *
     * @return array
     */
    private function generateModelsConfiguration(array $modelsConfig)
    {
        $generatedConfiguration = [
            'resources' => [],
            'modelFields' => [],
        ];
        foreach ($modelsConfig as $modelName => $options) {
            $generatedConfiguration['resources'][$options['collection']] = TestRepository::class;
            $generatedConfiguration['modelFields'][$options['collection']] = $options['fields'];
        }

        return $generatedConfiguration;
    }
}
