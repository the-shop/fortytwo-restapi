<?php

namespace Framework\RestApi\Test\Auth\Controller;

use Framework\Base\Application\Exception\NotFoundException;
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

        $resourceConfig = [
            'tests' => TestRepository::class,
            'users' => TestRepository::class
        ];

        $this->getApplication()
             ->getRepositoryManager()
             ->registerResources($resourceConfig);

        $repository = $this->getApplication()
                           ->getRepositoryManager()
                           ->getRepositoryFromResourceName('users');

        $model = new TestModel();

        $model->defineModelAttributes(
            $this->getApplication()
                 ->getConfiguration()
                 ->getPathValue('models.User.fields')
        )
              ->setApplication($this->getApplication())
              ->setAttribute('email', 'test@test.com')
              ->setAttribute('password', 'test123')
              ->setAttribute('role', 'standard')
              ->setPrimaryKey($repository->getModelPrimaryKey())
              ->setCollection($repository->getCollection())
              ->setApplication($this->getApplication())
              ->setRepository($repository)
              ->setDatabaseAddress($this->getApplication()
                                        ->getConfiguration()
                                        ->getPathValue('env.DATABASE_ADDRESS'))
              ->setDatabase($this->getApplication()
                                 ->getConfiguration()
                                 ->getPathValue('env.DATABASE_NAME'));

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

        $authModel['users'] = ['strategy' => 'Imagined', 'credentials' => ['email', 'password', 'oneTooMany']];

        $this->getApplication()
             ->getRepositoryManager()
             ->addAuthenticatableModels($authModel);

        $response = $this->makeHttpRequest('POST', '/login', $post);

        $this::assertEquals('No strategy registered with that name', $response->getBody()['errors'][0]);
        $this::assertEquals(400, $response->getCode());
    }

    /**
     *
     */
    public function testForgotPassword()
    {
        $post = [
            'email' => 'test@test.com'
        ];

        $response = $this->makeHttpRequest('POST', '/forgotpassword', $post);

        $this::assertEquals(
            'You will shortly receive an email with the link to reset your password.',
            $response->getBody()
        );
        $this::assertEquals(200, $response->getCode());
    }

    /**
     *
     */
    public function testForgotPasswordNoEmail()
    {
        $post = [];

        $response = $this->makeHttpRequest('POST', '/forgotpassword', $post);

        $this::assertEquals('Email field missing.', $response->getBody()['errors'][0]);
        $this::assertEquals(400, $response->getCode());
    }

    /**
     *
     */
    public function testForgotPasswordUserNotFound()
    {
        $post = [
            'email' => 'testic@test.com'
        ];

        $this->getApplication()
             ->getRepositoryManager()
             ->getPrimaryAdapter('users')
             ->setLoadOneResult(null);

        $response = $this->makeHttpRequest('POST', '/forgotpassword', $post);

        $this::assertEquals('User not found.', $response->getBody()['errors'][0]);
        $this::assertEquals(404, $response->getCode());
    }

    /**
     *
     */
    public function testResetPassword()
    {
        $post = [
            'email' => 'test@test.com'
        ];

        $this->makeHttpRequest('POST', '/forgotpassword', $post);

        $token = $this->getApplication()
                      ->getRepositoryManager()
                      ->getRepositoryFromResourceName('users')
                      ->loadOneBy($post)
                      ->getAttribute('passwordResetToken');

        $post = [
            'token' => $token,
            'newPassword' => 'test12',
            'repeatNewPassword' => 'test12'
        ];

        $response = $this->makeHttpRequest('POST', '/resetpassword', $post);

        $this::assertEquals('Password successfully changed.', $response->getBody());
        $this::assertEquals(200, $response->getCode());
    }

    /**
     *
     */
    public function testResetPasswordTokenMissing()
    {
        $post = [];

        $response = $this->makeHttpRequest('POST', '/resetpassword', $post);

        $this::assertEquals('Token not provided.', $response->getBody()['errors'][0]);
        $this::assertEquals(404, $response->getCode());
    }

    /**
     *
     */
    public function testResetPasswordUserNotFound()
    {
        $post = [
            'email' => 'test@test.com'
        ];

        $this->makeHttpRequest('POST', '/forgotpassword', $post);

        $post = [
            'token' => '123',
            'newPassword' => 'test12',
            'repeatNewPassword' => 'test12'
        ];

        $this->getApplication()
             ->getRepositoryManager()
             ->getPrimaryAdapter('users')
             ->setLoadOneResult(null);

        $response = $this->makeHttpRequest('POST', '/resetpassword', $post);

        $this::assertEquals('Invalid token provided.', $response->getBody()['errors'][0]);
        $this::assertEquals(404, $response->getCode());
    }

    /**
     *
     */
    public function testResetPasswordTokenExpired()
    {
        $post = [
            'email' => 'test@test.com'
        ];

        $this->makeHttpRequest('POST', '/forgotpassword', $post);

        $model = $this->getApplication()
                      ->getRepositoryManager()
                      ->getRepositoryFromResourceName('users')
                      ->loadOneBy($post);

        $model->setAttribute(
            'passwordResetTime',
            $model->getAttribute('passwordResetTime') - (30*60*60)
        );

        $token = $model->getAttribute('passwordResetToken');

        $post = [
            'token' => $token
        ];

        $response = $this->makeHttpRequest('POST', '/resetpassword', $post);

        $this::assertEquals(
            'Token has expired.',
            $response->getBody()['errors'][0]
        );
        $this::assertEquals(500, $response->getCode());
    }

    /**
     *
     */
    public function testResetPasswordMissingNewPassword()
    {
        $post = [
            'email' => 'test@test.com'
        ];

        $this->makeHttpRequest('POST', '/forgotpassword', $post);

        $token = $this->getApplication()
                      ->getRepositoryManager()
                      ->getRepositoryFromResourceName('users')
                      ->loadOneBy($post)
                      ->getAttribute('passwordResetToken');

        $post = [
            'token' => $token,
            'repeatNewPassword' => 'test12'
        ];

        $response = $this->makeHttpRequest('POST', '/resetpassword', $post);

        $this::assertEquals(
            'newPassword and repeatNewPassword fields must be provided and must not be empty!',
            $response->getBody()['errors'][0]
        );
        $this::assertEquals(400, $response->getCode());
    }
}
