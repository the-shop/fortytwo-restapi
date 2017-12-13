<?php

namespace Framework\RestApi\Listener;

use Framework\Base\Application\ApplicationAwareTrait;
use Framework\Base\Event\ListenerInterface;

/**
 * Class RegistrationListener
 * @package Framework\RestApi\Listener
 */
class RegistrationListener implements ListenerInterface
{
    use ApplicationAwareTrait;

    /**
     * @param $payload
     *
     * @return null
     */
    public function handle($payload)
    {
        $authModels = $this->getApplication()
                           ->getRepositoryManager()
                           ->getAuthenticatableModels();

        if (isset($authModels[$payload['resourceName']]) === true) {
            $post = $this->getApplication()
                         ->getRequest()
                         ->getPost();

            $definedModelAttributes = $this->getApplication()
                                           ->getRepositoryManager()
                                           ->getRegisteredModelFields($payload['resourceName']);


            foreach ($definedModelAttributes as $attribute => $options) {
                if (isset($post[$attribute]) === false
                    && empty($options['default']) === false
                ) {
                    $post[$attribute] = $options['default'];
                }
            }

            $this->getApplication()
                 ->getRequest()
                 ->setPost($post);
        }
        return null;
    }
}
