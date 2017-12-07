<?php

namespace Framework\RestApi\Auth;

use Framework\Base\Model\BrunoInterface;

/**
 * Class RequestAuthorization
 * @package Framework\RestApi\Auth
 */
class RequestAuthorization
{
    /**
     * @var string|null
     */
    private $resourceName = null;

    /**
     * @var mixed|null
     */
    private $id = null;

    /**
     * @var string
     */
    private $role = 'guest';

    /**
     * @var BrunoInterface|null
     */
    private $model = null;

    /**
     * @return null|string
     */
    public function getResourceName()
    {
        return $this->resourceName;
    }

    /**
     * @param null|string $resourceName
     *
     * @return RequestAuthorization
     */
    public function setResourceName($resourceName): RequestAuthorization
    {
        $this->resourceName = $resourceName;

        return $this;
    }

    /**
     * @return mixed|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed|null $id
     *
     * @return RequestAuthorization
     */
    public function setId($id): RequestAuthorization
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     *
     * @return RequestAuthorization
     */
    public function setRole(string $role): RequestAuthorization
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return BrunoInterface|null
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param $model
     *
     * @return RequestAuthorization
     */
    public function setModel($model): RequestAuthorization
    {
        $this->model = $model;

        return $this;
    }
}
