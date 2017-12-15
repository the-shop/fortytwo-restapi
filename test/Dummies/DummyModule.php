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

        // Let's read all files from module config folder and set to Configuration
        $configFile = realpath(dirname(__FILE__)) . '/dummy_module.php';

        $config->readFromPhp($configFile);

        // Add routes to dispatcher
        $app->getDispatcher()
            ->addRoutes($config->getPathValue('routes'));

        // Add acl rules
        $app->setAclRules($config->getPathValue('acl'));

        // Format models configuration
        $modelsConfiguration = $this->generateModelsConfiguration(
            $config->getPathValue('models')
        );

        $repositoryManager = $app->getRepositoryManager();

        $modelAdapters = $config->getPathValue('modelAdapters');
        // Register model adapters
        foreach ($modelAdapters as $model => $adapters) {
            foreach ($adapters as $adapter) {
                $repositoryManager->addModelAdapter($model, new $adapter());
            }
        }

        $primaryModelAdapter = $config->getPathValue('primaryModelAdapter');
        // Register model primary adapters
        foreach ($primaryModelAdapter as $model => $primaryAdapter) {
            $repositoryManager->setPrimaryAdapter($model, new $primaryAdapter());
        }

        $services = $config->getPathValue('services');
        foreach ($services as $serviceName => $conf) {
            $app->registerService(new $serviceName($conf));
        }


        // Register resources, repositories and model fields
        $repositoryManager->registerResources($modelsConfiguration['resources'])
                          ->registerRepositories($config->getPathValue('repositories'))
                          ->registerModelFields($modelsConfiguration['modelFields'])
                          ->addAuthenticatableModels($config->getAuthenticatables());
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
