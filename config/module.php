<?php

use Framework\Base\Application\BaseApplication;
use Framework\Base\Application\Exception\ExceptionHandler;
use Framework\RestApi\Auth\Strategy\PasswordAuthStrategy;
use Framework\RestApi\Listener\ExceptionFormatter;
use Framework\RestApi\Listener\ResponseFormatter;

return [
    'routes' => [
        [
            'post',
            '/login',
            '\Framework\RestApi\Auth\Controller\AuthController::authenticate',
        ],
        [
            'post',
            '/forgotPassword',
            '\Framework\RestApi\Auth\Controller\AuthController::forgotPassword',
        ],
        [
            'post',
            '/resetPassword',
            '\Framework\RestApi\Auth\Controller\AuthController::resetPassword',
        ],
    ],
    'listeners' => [
        BaseApplication::EVENT_APPLICATION_RENDER_RESPONSE_PRE => [
            ResponseFormatter::class,
        ],
        ExceptionHandler::EVENT_EXCEPTION_HANDLER_HANDLE_PRE => [
            ExceptionFormatter::class,
        ],
    ],
    'env' => [
        'PRIVATE_MAIL_FROM' => getenv('PRIVATE_MAIL_FROM'),
        'PRIVATE_MAIL_NAME' => getenv('PRIVATE_MAIL_NAME'),
        'PRIVATE_MAIL_SUBJECT' => getenv('PRIVATE_MAIL_SUBJECT'),
        'WEB_DOMAIN' => getenv('WEB_DOMAIN')
    ],
    'authStrategies' => [
        'password' => PasswordAuthStrategy::class,
    ],
];
