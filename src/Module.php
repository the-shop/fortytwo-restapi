<?php

namespace Framework\RestApi;

use Framework\Base\Module\BaseModule;

/**
 * Class Module
 * @package Framework\RestApi
 */
class Module extends BaseModule
{
    /**
     * @inheritdoc
     */
    public function loadConfig()
    {
        // Let's read all files from module config folder and set to Configuration
        $configDirPath = realpath(dirname(__DIR__)) . '/config/';
        $this->setModuleConfiguration($configDirPath);
    }

    /**
     * @inheritdoc
     */
    public function bootstrap()
    {
        /**
         * @var \Framework\RestApi\RestApiApplicationInterface $application
         */
        $application = $this->getApplication();
        /**
         * @var RestApiConfigurationInterface $appConfig
         */
        $appConfig = $application->getConfiguration();

        // Register Auth Strategies
        if (
            empty($authStrategies = $appConfig->getPathValue('authStrategies')) === false
        ) {
            foreach ($authStrategies as $name => $fullyQualifiedClassName) {
                $application->registerAuthStrategy($name, $fullyQualifiedClassName);
            }
        }

        // Register Acl rules
        if (
            empty($acl = $appConfig->getPathValue('acl')) === false
        ) {
            $application->setAclRules($acl);
        }

        // Register authenticatable models
        if (
            empty($auth = $appConfig->getAuthenticatables()) === false
        ) {
            $application->getRepositoryManager()
                        ->addAuthenticatableModels($auth);
        }
    }
}
