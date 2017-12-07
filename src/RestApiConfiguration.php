<?php

namespace Framework\RestApi;

use Framework\Base\Application\ApplicationConfiguration;

/**
 * Class RestApiConfiguration
 * @package Framework\RestApi
 */
class RestApiConfiguration extends ApplicationConfiguration implements RestApiConfigurationInterface
{
    /**
     * @return array
     * @throws \RuntimeException
     */
    public function getAuthenticatables(): array
    {
        $models = [];

        $modelsConfig = $this->getPathValue('models');

        if (empty($modelsConfig) === true) {
            throw new \RuntimeException('No models defined');
        }

        foreach ($modelsConfig as $modelName => $params) {
            if (isset($params['authenticatable']) === true &&
                $params['authenticatable'] === true &&
                isset($params['authStrategy']) === true &&
                isset($params['credentials']) === true &&
                is_array($params['credentials']) === true &&
                isset($params['aclRoleField']) === true
            ) {
                $models[$params['collection']] = [
                    'strategy' => $params['authStrategy'],
                    'credentials' => $params['credentials'],
                    'aclRole' => $params['aclRoleField'],
                ];
            }
        }

        return $models;
    }
}
