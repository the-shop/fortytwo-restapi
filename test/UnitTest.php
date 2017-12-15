<?php

namespace Framework\RestApi\Test;

use Framework\Base\Application\BaseApplication;
use Framework\Base\Module as BaseModule;
use Framework\Http\Module as HttpModule;
use Framework\Http\Test\UnitTest as TestCase;
use Framework\RestApi\Listener\Acl;
use Framework\RestApi\Module as RestApiModule;
use Framework\RestApi\RestApiConfiguration;
use Framework\RestApi\Test\Dummies\DummyModule;
use Framework\RestApi\Test\Dummies\DummyRestApiApplication;

/**
 * All tests should extend this class
 * Class UnitTest
 * @package Framework\RestApi\Test
 */
class UnitTest extends TestCase
{
    /**
     * UnitTest constructor.
     *
     * @param null   $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $config = new RestApiConfiguration();

        $config->setRegisteredModules(
            [
                BaseModule::class,
                HttpModule::class,
                RestApiModule::class,
                DummyModule::class,
            ]
        );

        $this->setApplication(new DummyRestApiApplication($config));

        // Remove render events from the application
        $this->getApplication()
             ->removeEventListeners(BaseApplication::EVENT_APPLICATION_RENDER_RESPONSE_PRE);
    }

    /**
     * Helper method for generating random E-mail
     *
     * @param int $length
     *
     * @return string
     */
    protected function generateRandomEmail(int $length = 10)
    {
        $email = $this->generateRandomString($length);

        $email .= '@test.com';

        return $email;
    }

    /**
     * @param int $length
     *
     * @return string
     */
    protected function generateRandomString(int $length = 10)
    {
        // Generate random email
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $string = '';

        for ($i = 0; $i < $length; $i ++) {
            $string .= $characters[rand(0, $charactersLength - 1)];
        }

        return $string;
    }
}
