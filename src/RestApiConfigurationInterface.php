<?php

namespace Framework\RestApi;

use Framework\Base\Application\ApplicationConfigurationInterface;

/**
 * Interface RestApiConfigurationInterface
 * @package Framework\RestApi
 */
interface RestApiConfigurationInterface extends ApplicationConfigurationInterface
{
    /**
     * @return array
     */
    public function getAuthenticatables(): array;
}
