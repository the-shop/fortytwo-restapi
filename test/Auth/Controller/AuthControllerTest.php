<?php

namespace Framework\RestApi\Test\Auth\Controller;

use Framework\Base\Test\Dummies\TestModel;
use Framework\Base\Test\Dummies\TestRepository;
use Framework\Http\Response\HttpResponseInterface;
use Framework\Http\Response\Response;
use Framework\RestApi\Test\UnitTest;

/**
 * Class AuthControllerTest
 * @package Framework\RestApi\Test\Auth\Controller
 */
class AuthControllerTest extends UnitTest
{
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

        $repository = $this->getApplication()
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
              ->setAttribute('password', 'test123')
              ->setAttribute('role', 'standard');

        $repository->getPrimaryAdapter()
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
    public function testAuthenticationSuccess()
    {
        $post = [
            'email' => 'test@test.com',
            'password' => 'test123',
        ];

        /**
         * @var HttpResponseInterface $response
         */
        $response = $this->makeHttpRequest('POST', '/login', $post);

        $this::assertInstanceOf(Response::class, $response);
        $this::assertInternalType('object', $response->getBody());
        $this::assertInstanceOf(
            TestModel::class,
            $response->getBody()
        );
        $this::assertArrayHasKey('Authorization', $response->getHeaders());
    }

    /**
     * Test Auth strategy not registered/matched, strategy not registered
     */
    public function testAuthenticationFail()
    {
        $post = [
            'email' => 'test@test.com',
            'password' => 'test123',
            'oneTooMany' => 'fail',
        ];

        $response = $this->makeHttpRequest('POST', '/login', $post);

        $this::assertEquals('Auth strategy not implemented', $response->getBody()['errors'][0]);
        $this::assertEquals(500, $response->getCode());

        $authModel['tests'] = ['strategy' => 'Imagined', 'credentials' => ['email', 'password', 'oneTooMany']];

        $this->getApplication()
             ->getRepositoryManager()
             ->addAuthenticatableModels($authModel);

        $response = $this->makeHttpRequest('POST', '/login', $post);

        $this::assertEquals('No strategy registered with that name', $response->getBody()['errors'][0]);
        $this::assertEquals(500, $response->getCode());
    }
}
