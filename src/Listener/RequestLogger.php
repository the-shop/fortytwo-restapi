<?php

namespace Framework\RestApi\Listener;

use Framework\Base\Application\ApplicationAwareTrait;
use Framework\Base\Event\ListenerInterface;
use Framework\Http\Request\HttpRequestInterface;
use Framework\RestApi\Auth\RequestAuthorization;
use Framework\RestApi\RestApiApplicationInterface;

/**
 * Class RequestLogger
 * @package Framework\RestApi\Listener
 */
class RequestLogger implements ListenerInterface
{
    use ApplicationAwareTrait;

    /**
     * Handle an incoming request.
     *
     * @param $payload
     *
     * @return ListenerInterface
     */
    public function handle($payload)
    {
        /**
         * @var RestApiApplicationInterface $app
         */
        $app = $this->getApplication();
        /**
         * @var HttpRequestInterface $request
         */
        $request = $app->getRequest();
        /**
         * @var RequestAuthorization $requestAuth
         */
        $requestAuth = $app->getRequestAuthorization();

        $name = '';
        $id = '';

        if ($requestAuth !== null) {
            $id = $requestAuth->getId();
            $model = $requestAuth->getModel();
            if ($model !== null) {
                $name = $model->getAttribute('name');
            }
        }

        $logData = [
            'name' => $name,
            'userId' => $id,
            'date' => (new \DateTime())->format('d-m-Y H:i:s'),
            'ip' => $request->getClientIp(),
            'uri' => $request->getUri(),
            'method' => $request->getMethod()
        ];

        $app->getRepositoryManager()
            ->getRepositoryFromResourceName('logs')
            ->newModel()
            ->setAttributes($logData)
            ->save();

        return $this;
    }
}
