<?php

namespace Framework\RestApi\Test\Listener;

use Framework\Base\Test\Dummies\TestModel;
use Framework\Base\Test\Dummies\TestRepository;
use Framework\RestApi\Test\UnitTest;

/**
 * Class AclTest
 * @package Framework\RestApi\Test\Listeners
 */
class AclTest extends UnitTest
{
    /**
     *
     */
    public function setUp()
    {
        parent::setUp();

        $resourceConfig = ['users' => TestRepository::class];

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
              ->setAttribute('email', 'user@user.com')
              ->setAttribute('password', 'user123')
              ->setAttribute('role', 'standard');

        $repository->getPrimaryAdapter()
                   ->setLoadOneResult([$model]);
    }

    /**
     *
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Test acl listener for routes, user has got no permission for requested route - exception
     */
    public function testAclRoutePermissionDenied()
    {
        $aclTestRules = [
            'routes' => [
                'public' => [
                    'GET' => [],
                ],
                'private' => [
                    'GET' => [],
                ],
            ],
        ];

        $this->getApplication()
             ->setAclRules($aclTestRules);

        $response = $this->makeHttpRequest('GET', '/users');

        $responseBody = $response->getBody();

        $this->assertArrayHasKey('error', $responseBody);
        $this->assertArrayHasKey('errors', $responseBody);

        $this->assertEquals(true, $responseBody['error']);
        $this->assertEquals(403, $response->getCode());
    }

    /**
     * Test acl listener for routes, user has got permission - allowed to visit requested route
     */
    public function testAclRuleAllowed()
    {
        $aclTestRules = [
            'routes' => [
                'public' => [
                    'GET' => [],
                ],
                'private' => [
                    'GET' => [
                        [
                            'route' => '/users',
                            'allows' => [
                                'guest',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->getApplication()
             ->setAclRules($aclTestRules);

        $response = $this->makeHttpRequest('GET', '/users');

        $this->assertEquals(200, $response->getCode());
    }
}
