<?php

namespace Framework\RestApi\Test\Auth\Strategy;

use Framework\Base\Application\Exception\NotFoundException;
use Framework\Base\Repository\BrunoRepositoryInterface;
use Framework\Base\Test\Dummies\TestModel;
use Framework\Base\Test\Dummies\TestRepository;
use Framework\RestApi\Auth\Strategy\PasswordAuthStrategy;
use Framework\RestApi\Exception\AuthenticationException;
use Framework\RestApi\Test\UnitTest;

/**
 * Class PasswordAuthStrategyTest
 * @package Framework\RestApi\Test\Auth\Strategy
 */
class PasswordAuthStrategyTest extends UnitTest
{
    /**
     * @var BrunoRepositoryInterface
     */
    private $repository;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();

        $resourceConfig = ['tests' => TestRepository::class];

        $this->getApplication()
             ->getRepositoryManager()
             ->registerResources($resourceConfig);

        $this->repository = $this->getApplication()
                                 ->getRepositoryManager()
                                 ->getRepositoryFromResourceName('tests');

        $model = new TestModel();

        $model->defineModelAttributes(
            $this->getApplication()
                 ->getConfiguration()
                 ->getPathValue('models.Test.fields')
        )
              ->setApplication($this->getApplication())
              ->setAttribute('email', 'test@test.com')
              ->setAttribute('password', 'test123');

        $this->repository->getPrimaryAdapter()
                         ->setLoadOneResult($model);
    }

    /**
     *
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     *
     */
    public function testIsInstantiableAndSettersAndGetters()
    {
        $post = ['two', 'elements'];
        $repository = new TestRepository();

        $strategy = new PasswordAuthStrategy($post, $repository);

        $this::assertInstanceOf(PasswordAuthStrategy::class, $strategy);

        $this::assertEquals($repository, $strategy->getRepository());
        $this::assertEquals($post[0], $strategy->getId());
        $this::assertEquals($post[1], $strategy->getPassword());
    }

    /**
     *
     */
    public function testValidationSuccess()
    {
        $post = ['email' => 'test@test.com', 'password' => 'test123'];
        $strategy = new PasswordAuthStrategy($post, $this->repository);

        $this::assertInstanceOf(
            TestModel::class,
            $strategy->validate(['email', 'password'])
        );
    }

    /**
     *
     */
    public function testValidationModelNotFound()
    {
        $this->repository->getPrimaryAdapter()
                         ->setLoadOneResult(null);

        $post = ['email' => 'test@testic.com', 'password' => 'test123'];
        $strategy = new PasswordAuthStrategy($post, $this->repository);

        $this::expectException(NotFoundException::class);
        $strategy->validate(['email', 'password']);
    }

    /**
     *
     */
    public function testValidationInvalidCredentials()
    {
        $post = ['email' => 'test@test.com', 'password' => 'pw1234'];
        $strategy = new PasswordAuthStrategy($post, $this->repository);

        $this::expectException(AuthenticationException::class);
        $strategy->validate(['email', 'password']);
    }
}
