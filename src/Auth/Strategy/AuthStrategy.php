<?php

namespace Framework\RestApi\Auth\Strategy;

use Framework\Base\Application\ApplicationAwareTrait;
use Framework\Base\Repository\BrunoRepositoryInterface;

/**
 * Class AuthStrategy
 * @package Framework\RestApi\Auth\Strategy
 */
abstract class AuthStrategy implements AuthStrategyInterface
{
    use ApplicationAwareTrait;

    /**
     * AuthStrategy constructor.
     *
     * @param string                   $id
     * @param string                   $authString
     * @param BrunoRepositoryInterface $repository
     */
    public function __construct(string $id, string $authString, BrunoRepositoryInterface $repository)
    {
        $this->setIdentifier($id)
             ->setAuthorization($authString)
             ->setRepository($repository);
    }
}
