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
    public function bootstrap()
    {
        // Let's read all files from module config folder and set to Configuration
        $configDirPath = realpath(dirname(__DIR__)) . '/config/';
        $this->setModuleConfiguration($configDirPath);

        /**
         * @var \Framework\RestApi\RestApiApplicationInterface $application
         */
        $application = $this->getApplication();
        $appConfig = $application->getConfiguration();

        // Add listeners to application
        $listeners = $appConfig->getPathValue('listeners');
        foreach ($listeners as $event => $arrayHandlers) {
            foreach ($arrayHandlers as $handlerClass) {
                $application->listen($event, $handlerClass);
            }
        }

        // Register Auth Strategies
        $authStrategies = $appConfig->getPathValue('authStrategies');
        foreach ($authStrategies as $name => $fullyQualifiedClassName) {
            $application->registerAuthStrategy($name, $fullyQualifiedClassName);
        }
    }
}
