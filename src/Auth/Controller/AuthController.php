<?php

namespace Framework\RestApi\Auth\Controller;

use Firebase\JWT\JWT;
use Framework\Base\Application\Exception\NotFoundException;
use Framework\Base\Model\BrunoInterface;
use Framework\Base\Service\EmailService;
use Framework\Http\Controller\Http as HttpController;
use Framework\RestApi\Auth\RequestAuthorization;
use Framework\RestApi\Auth\Strategy\AuthStrategyInterface;
use Framework\RestApi\Exception\AuthenticationException;

/**
 * Class AuthController
 * @package Framework\RestApi\Auth\Controller
 */
class AuthController extends HttpController
{
    /**
     * @return BrunoInterface
     * @throws AuthenticationException
     * @throws NotFoundException
     * @throws \RuntimeException
     */
    public function authenticate(): BrunoInterface
    {
        $authModels = $this->getRepositoryManager()
                           ->getAuthenticatableModels();

        $post = $this->getPost();
        $model = $exception = null;
        $attemptStrategies = [];

        foreach ($authModels as $resourceName => $params) {
            if (count(
                    array_diff(
                        $params['credentials'],
                        array_keys($post)
                    )
                ) === 0
                && count($post) === count($params['credentials'])
            ) {
                $attemptStrategies[] = [
                    'repository' => $this->getRepositoryFromResourceName($resourceName),
                    'class' => $this->getApplication()
                                    ->getRegisteredAuthStrategy($params['strategy']),
                    'credentials' => $params['credentials'],
                ];
            }
        }

        if (empty($attemptStrategies) === true) {
            throw new \RuntimeException('Auth strategy not implemented');
        }

        foreach ($attemptStrategies as $strategy) {
            try {
                /**
                 * @var AuthStrategyInterface $auth
                 * @var BrunoInterface        $model
                 */
                $auth = new $strategy['class']($post, $strategy['repository']);
                $model = $auth->validate($strategy['credentials']);
            } catch (AuthenticationException $e) {
                $exception = $e;
            } catch (NotFoundException $e) {
                $exception = $e;
            }
        }

        if ($model === null) {
            throw $exception;
        }

        $requestAuth = new RequestAuthorization();
        $requestAuth->setResourceName($model->getCollection())
                    ->setId($model->getId())
                    ->setRole($model->getAttribute('role'))
                    ->setModel($model);

        $this->getApplication()
             ->setRequestAuthorization($requestAuth);

        /**
         * @todo implement key generation, adjustable time on token expiration, algorithm selection
         */
        JWT::$timestamp = time();
        $key = 'rV)7Djb{DpEpY5ex';
        $payload = [
            'iss' => 'framework.the-shop.io',
            'exp' => JWT::$timestamp + 3600,
            'modelId' => $requestAuth->getId(),
            'resourceName' => $requestAuth->getResourceName(),
            'aclRole' => $requestAuth->getRole(),
        ];
        $alg = 'HS384';
        $jwt = JWT::encode($payload, $key, $alg);

        $this->getApplication()
             ->getResponse()
             ->addHeader('Authorization', "Bearer $jwt");

        return $model;
    }

    /**
     * @return string
     * @throws NotFoundException
     * @throws \Exception
     */
    public function forgotPassword()
    {
        // Check if there is email field
        $postParams = $this->getPost();
        if (isset($postParams['email']) === false) {
            throw new NotFoundException('Email field missing.', 404);
        }

        // Load user model
        $model = $this->getRepositoryFromResourceName('users')
                      ->loadOneBy(
                          [
                              'email' => $postParams['email'],
                          ]
                      );

        // If no model found, throw exception
        if (!$model) {
            throw new NotFoundException('User not found.', 404);
        }

        // Generate random token and timestamp and set to profile
        $passwordResetToken = md5(
            uniqid(
                rand(),
                true
            )
        );
        $passwordResetTime = (new \DateTime())->format('U');
        $model->setAttributes(
            [
                'passwordResetToken' => $passwordResetToken,
                'passwordResetTime' => $passwordResetTime,
            ]
        );

        // Try to save model and send password reset email
        $model->save();
        $modelAttributes = $model->getAttributes();

        $webDomain = $this->getApplication()
                          ->getConfiguration()
                          ->getPathValue('env.WEB_DOMAIN');

        $webDomain .= 'reset-password';
        $subject = 'Password reset confirmation link!';

        $html = /** @lang text */
            "<html>
                <body>
                    <p> Please, visit this link below to change your password.</p>
                    <p>
                        <a href='{$webDomain}?token={$modelAttributes['passwordResetToken']}'>
                            Click here to set a new password.
                        </a>
                    </p>
                </body>
            </html>";

        $appConfig = $this->getApplication()
                          ->getConfiguration();

        /** @var EmailService $mailSender */
        $mailSender = $this->getApplication()
                           ->getService(EmailService::class);

        if ($mailSender->sendEmail(
            $appConfig->getPathValue('env.PRIVATE_MAIL_FROM'),
            $subject,
            $model->getAttribute('email'),
            $html
        )
        ) {
            return 'You will shortly receive an email with the link to reset your password.';
        }

        throw new \Exception('Issue with sending password reset email.');
    }

    /**
     * @return string
     * @throws NotFoundException
     * @throws \Exception
     * @throws \HttpRuntimeException
     * @throws \InvalidArgumentException
     */
    public function resetPassword()
    {
        $postParams = $this->getPost();

        // Check if token is provided
        if (isset($postParams['token']) === false) {
            throw new NotFoundException('Token not provided.', 404);
        }

        // Load user model
        $model = $this->getRepositoryFromResourceName('users')
                      ->loadOneBy(
                          [
                              'passwordResetToken' => $postParams['token'],
                          ]
                      );

        // If no model found, throw exception
        if ($model === null) {
            throw new NotFoundException('Invalid token provided.', 404);
        }

        $modelAttributes = $model->getAttributes();

        // Check timestamps
        $unixNow = (int)(new \DateTime())->format('U');

        if ($unixNow - $modelAttributes['passwordResetTime'] > (24 * 60 * 60)) {
            throw new \HttpRuntimeException('Token has expired.', 400);
        }

        if (isset($postParams['newPassword']) === false
            || isset($postParams['repeatNewPassword']) === false
            || empty($postParams['newPassword']) === true
            || empty($postParams['repeatNewPassword']) === true
        ) {
            throw new \InvalidArgumentException(
                'newPassword and repeatNewPassword fields must be provided and must not be empty!',
                403
            );
        }

        $newPassword = $postParams['newPassword'];
        $repeatNewPassword = $postParams['repeatNewPassword'];

        // Check passwords
        if ($newPassword !== $repeatNewPassword) {
            throw new \InvalidArgumentException('Passwords mismatch');
        }

        // Reset token and set new password
        $model->setAttribute('passwordResetToken', '');
        $model->setAttribute('password', $newPassword);

        // Try to save model and send confirmation email
        $model->save();
        $subject = 'Password successfully changed!';

        $html = /** @lang text */
            "<html>
                <body>
                    <p> Hey, you have successfully changed your password.</p>
                </body>
            </html>";

        /**
         * @var EmailService $mailSender
         */
        $appConfig = $this->getApplication()
                          ->getConfiguration();

        $mailSender = $this->getApplication()
                           ->getService(EmailService::class);

        $mailSender->sendEmail(
            $appConfig->getPathValue('env.PRIVATE_MAIL_FROM'),
            $subject,
            $model->getAttribute('email'),
            $html
        );

        return 'Password successfully changed.';
    }
}
