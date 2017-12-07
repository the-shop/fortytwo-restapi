<?php

namespace Framework\RestApi;

use Framework\Base\Application\ApplicationInterface;
use Framework\RestApi\Auth\RequestAuthorization;

/**
 * Interface RestApiApplicationInterface
 * @package Framework\RestApi
 */
interface RestApiApplicationInterface extends ApplicationInterface
{
    /**
     * @param RequestAuthorization $requestAuthorization
     *
     * @return RestApiApplicationInterface
     */
    public function setRequestAuthorization(RequestAuthorization $requestAuthorization): RestApiApplicationInterface;

    /**
     * @return null|RequestAuthorization
     */
    public function getRequestAuthorization();

    /**
     * @param array $aclConfig
     *
     * @return RestApiApplicationInterface
     */
    public function setAclRules(array $aclConfig = []): RestApiApplicationInterface;

    /**
     * @return array
     */
    public function getAclRules(): array;

    /**
     * @return array
     */
    public function getRegisteredAuthStrategies(): array;

    /**
     * @param string $strategyName
     *
     * @return string
     */
    public function getRegisteredAuthStrategy(string $strategyName): string;

    /**
     * @param string $name
     * @param string $fullyQualifiedClassName
     *
     * @return RestApiApplicationInterface
     */
    public function registerAuthStrategy(string $name, string $fullyQualifiedClassName): RestApiApplicationInterface;
}
