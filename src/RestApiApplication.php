<?php

namespace Framework\RestApi;

use Framework\Base\Application\BaseApplication;
use Framework\Base\Request\RequestInterface;
use Framework\Http\Render\Json;
use Framework\Http\Request\Request;
use Framework\Http\Response\Response;
use Framework\Http\Router\Dispatcher;
use Framework\RestApi\Auth\RequestAuthorization;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Class RestApiApplication
 * @package Framework\RestApi
 */
class RestApiApplication extends BaseApplication implements RestApiApplicationInterface
{
    /**
     * @var RequestAuthorization
     */
    private $requestAuthorization = null;

    /**
     * @var array
     */
    private $aclRules = [];

    /**
     * @var array
     */
    private $registeredAuthStrategies = [];

    /**
     * RestApiApplication constructor.
     *
     * @param RestApiConfigurationInterface|null $applicationConfiguration
     */
    public function __construct(RestApiConfigurationInterface $applicationConfiguration = null)
    {
        if ($applicationConfiguration === null) {
            $applicationConfiguration = new RestApiConfiguration();
        }
        $this->setRenderer(new Json());
        $this->setDispatcher(new Dispatcher());
        $this->setResponse(new Response());

        parent::__construct($applicationConfiguration);
    }

    /**
     * @inheritdoc
     */
    public function buildRequest(): RequestInterface
    {
        $request = new Request();

        $helperRequest = SymfonyRequest::createFromGlobals();

        $ctHeader = $helperRequest->headers->get('Content-Type');
        if (strpos($ctHeader, 'application/json') === 0) {
            $data = json_decode($helperRequest->getContent(), true);
            $helperRequest->request->replace(is_array($data) ? $data : []);
        }

        $request->setServer($helperRequest->server->all())
                ->setClientIp($helperRequest->getClientIp())
                ->setPost($helperRequest->request->all())
                ->setQuery($helperRequest->query->all())
                ->setFiles($helperRequest->files->all())
                ->setCookies($helperRequest->cookies->all())
                ->setUri($helperRequest->getRequestUri());

        $requestMethod = $request->getMethod();

        if (($requestMethod === 'PUT' || $requestMethod === 'PATCH')
            && (strpos($ctHeader, 'application/json') === 0) === false
        ) {
            $request->setPost($request->getQuery());
        }

        unset($_POST);
        unset($_GET);
        unset($_FILES);
        unset($_COOKIE);

        $this->setRequest($request);

        return $request;
    }

    /**
     * @return RequestAuthorization|null
     */
    public function getRequestAuthorization()
    {
        return $this->requestAuthorization;
    }

    /**
     * @param RequestAuthorization $requestAuthorization
     *
     * @return RestApiApplicationInterface
     */
    public function setRequestAuthorization(RequestAuthorization $requestAuthorization): RestApiApplicationInterface
    {
        $this->requestAuthorization = $requestAuthorization;

        return $this;
    }

    /**
     * @return array
     */
    public function getAclRules(): array
    {
        return $this->aclRules;
    }

    /**
     * @param array $aclConfig
     *
     * @return RestApiApplicationInterface
     */
    public function setAclRules(array $aclConfig = []): RestApiApplicationInterface
    {
        $this->aclRules = $aclConfig;

        return $this;
    }

    /**
     * @return array
     */
    public function getRegisteredAuthStrategies(): array
    {
        return $this->registeredAuthStrategies;
    }

    /**
     * @param string $strategyName
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getRegisteredAuthStrategy(string $strategyName): string
    {
        if (isset($this->registeredAuthStrategies[$strategyName]) === false) {
            throw new \InvalidArgumentException('No strategy registered with that name');
        }

        return $this->registeredAuthStrategies[$strategyName];
    }

    /**
     * @param string $name
     * @param string $fullyQualifiedClassName
     *
     * @return RestApiApplicationInterface
     * @throws \RuntimeException
     */
    public function registerAuthStrategy(string $name, string $fullyQualifiedClassName): RestApiApplicationInterface
    {
        if (isset($this->registeredAuthStrategies[$name]) === true) {
            throw new \RuntimeException('Strategy already registered under that name');
        }

        $this->registeredAuthStrategies[$name] = $fullyQualifiedClassName;

        return $this;
    }
}
