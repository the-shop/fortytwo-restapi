<?php

namespace Framework\RestApi\Test\Dummies;

use Framework\Http\Controller\Http;

/**
 * Class DummyController
 * @package Framework\RestApi\Test\Dummies
 */
class DummyController extends Http
{
    /**
     * @return \Framework\Base\Model\BrunoInterface[]
     */
    public function getUsers()
    {
        $repository = $this->getRepositoryFromResourceName('users');

        $query = $repository->getPrimaryAdapter()
                            ->newQuery()
                            ->setCollection('users')
                            ->setDatabase(
                                $this->getApplication()
                                     ->getConfiguration()
                                     ->getPathValue('env.DATABASE_ADDRESS')
                            );

        $models = $repository->loadMultiple($query);

        return $models;
    }
}
