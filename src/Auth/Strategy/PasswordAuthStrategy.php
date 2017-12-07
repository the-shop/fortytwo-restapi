<?php

namespace Framework\RestApi\Auth\Strategy;

use Framework\Base\Application\Exception\NotFoundException;
use Framework\Base\Model\BrunoInterface;
use Framework\Base\Repository\BrunoRepositoryInterface;
use Framework\RestApi\Exception\AuthenticationException;

/**
 * Class PasswordAuthStrategy
 * @package Framework\RestApi\Auth\Strategy
 */
class PasswordAuthStrategy extends AuthStrategy
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $password;

    /**
     * @var BrunoRepositoryInterface
     */
    private $repository;

    /**
     * PasswordAuthStrategy constructor.
     *
     * @param array                    $post
     * @param BrunoRepositoryInterface $repository
     */
    public function __construct(array $post, BrunoRepositoryInterface $repository)
    {
        parent::__construct(reset($post), end($post), $repository);
    }

    /**
     * @param string $id
     *
     * @return AuthStrategyInterface
     */
    public function setIdentifier(string $id): AuthStrategyInterface
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string $authString
     *
     * @return AuthStrategyInterface
     */
    public function setAuthorization(string $authString): AuthStrategyInterface
    {
        $this->password = $authString;

        return $this;
    }

    /**
     * @param array $credentials
     *
     * @return BrunoInterface
     * @throws AuthenticationException
     * @throws NotFoundException
     */
    public function validate(array $credentials): BrunoInterface
    {
        $model = $this->getRepository()
                      ->loadOneBy(['email' => $this->getId()]);

        if ($model === null) {
            throw new NotFoundException('Model not found.');
        }

        $authorizationName = end($credentials);

        if ($model->getAttribute($authorizationName) === null ||
            password_verify($this->getPassword(), $model->getAttribute($authorizationName)) === false
        ) {
            throw new AuthenticationException('Invalid credentials');
        }

        return $model;
    }

    /**
     * @return BrunoRepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param BrunoRepositoryInterface $repository
     *
     * @return AuthStrategyInterface
     */
    public function setRepository(BrunoRepositoryInterface $repository): AuthStrategyInterface
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
}
