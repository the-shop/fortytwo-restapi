<?php

namespace Framework\RestApi\Listener;

use Framework\Base\Application\ApplicationAwareTrait;
use Framework\Base\Application\Exception\MethodNotAllowedException;
use Framework\Base\Event\ListenerInterface;
use Framework\Http\Request\HttpRequestInterface;
use Framework\RestApi\Auth\RequestAuthorization;
use Framework\RestApi\RestApiApplicationInterface;

/**
 * Class Acl
 * @package Framework\RestApi\Listeners
 */
class Acl implements ListenerInterface
{
    use ApplicationAwareTrait;

    /**
     * @param $payload
     *
     * @return mixed
     * @throws MethodNotAllowedException
     */
    public function handle($payload)
    {
        /**
         * @var RestApiApplicationInterface $app
         */
        $app = $this->getApplication();

        if (empty($app->getAclRules()) === false) {

            /**
             * @var HttpRequestInterface $request
             */
            $request = $app->getRequest();

            $routeParameters = $app->getDispatcher()
                                   ->getRouteParameters();

            $method = $request->getMethod();

            // Remove route prefix
            $routePrefix = $app->getConfiguration()
                               ->getPathValue('routePrefix');

            $uri = str_replace($routePrefix, '', $request->getUri());

            // Transform uri to actually registered route so we can compare that route with acl
            foreach ($routeParameters as $param => $value) {
                $modifiedParam = "{$param}";
                $uri = str_replace($value, $modifiedParam, $uri);
            }

            $aclRoutesRules = $app->getAclRules()['routes'];

            $reqAuthorization = $app->getRequestAuthorization();

            // If route is public and allowed for user role, ALLOW
            if ($this->checkRoutes($uri, $aclRoutesRules['public'][$method], $reqAuthorization) === true) {
                return $this;
            }

            // If route is private and allowed for user role, ALLOW
            if ($this->checkRoutes($uri, $aclRoutesRules['private'][$method], $reqAuthorization) === true) {
                return $this;
            }

            // If no rules defined for this route and user role or no permissions, DENY
            throw new MethodNotAllowedException('Permission denied.', 403);
        }

        return null;
    }

    /**
     * @param string               $requestedRoute
     * @param array                $routesDefinition
     * @param RequestAuthorization $requestAuthorization
     *
     * @return bool
     */
    private function checkRoutes(
        string $requestedRoute,
        array $routesDefinition = [],
        RequestAuthorization $requestAuthorization)
    {
        $role = $requestAuthorization->getRole();

        foreach ($routesDefinition as $routeDefinition) {
            if (strtolower($routeDefinition['route']) === $requestedRoute
                && in_array($role, $routeDefinition['allows']) === true) {
                return true;
            }
        }

        return false;
    }
}
