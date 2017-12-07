<?php

namespace Framework\RestApi\Auth\Strategy;

use Framework\Base\Application\ApplicationAwareInterface;
use Framework\Base\Application\Exception\NotFoundException;
use Framework\Base\Model\BrunoInterface;
use Framework\Base\Repository\BrunoRepositoryInterface;
use Framework\RestApi\Exception\AuthenticationException;

/**
 * Interface AuthStrategyInterface
 * @package Framework\RestApi\Auth\Strategy
 */
interface AuthStrategyInterface extends ApplicationAwareInterface
{
    /**
     * Unique identifier for Model (email, name, id, ....)
     *
     * @param string $id
     *
     * @return AuthStrategyInterface
     */
    public function setIdentifier(string $id): AuthStrategyInterface;

    /**
     * Authorization string (password, token, key, secret...)
     *
     * @param string $authString
     *
     * @return AuthStrategyInterface
     */
    public function setAuthorization(string $authString): AuthStrategyInterface;

    /**
     * Model repository
     *
     * @param BrunoRepositoryInterface $repository
     *
     * @return AuthStrategyInterface
     */
    public function setRepository(BrunoRepositoryInterface $repository): AuthStrategyInterface;

    /**
     * Validates the auth params
     *
     * @param array $credentials
     *
     * @return BrunoInterface
     * @throws AuthenticationException
     * @throws NotFoundException
     */
    public function validate(array $credentials): BrunoInterface;
}
