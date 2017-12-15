<?php

use Framework\Base\Service\EmailService;
use Framework\Base\Test\Dummies\TestDatabaseAdapter;
use Framework\Base\Test\Dummies\TestModel;
use Framework\Base\Test\Dummies\TestRepository;
use Framework\RestApi\Test\Dummies\DummySendGrid;

return [
    'routePrefix' => '',
    'routes' => [
        [
            'get',
            '/users',
            '\Framework\RestApi\Test\Dummies\DummyController::getUsers'
        ],
    ],
    'repositories' => [
        TestModel::class => TestRepository::class,
    ],
    'modelAdapters' => [
        'tests' => [
            TestDatabaseAdapter::class,
        ],
        'users' => [
            TestDatabaseAdapter::class,
        ],
    ],
    'primaryModelAdapter' => [
        'tests' => TestDatabaseAdapter::class,
        'users' => TestDatabaseAdapter::class,
    ],
    'services' => [
        EmailService::class => [
            'mailerInterface' => DummySendGrid::class,
            'mailerClient' => [
                'classPath' => DummySendGrid::class,
                'constructorArguments' => [],
            ],
        ],
    ],
    "models" => [
        "User" => [
            "collection" => "users",
            "authenticatable" => true,
            "authStrategy" => "password",
            "credentials" => [
                "email",
                "password",
            ],
            "aclRoleField" => "role",
            "fields" => [
                "_id" => [
                    "primaryKey" => true,
                    "label" => "ID",
                    "type" => "string",
                    "disabled" => true,
                    "required" => false,
                    "default" => "",
                ],
                "name" => [
                    "label" => "Name",
                    "type" => "string",
                    "required" => true,
                    "validation" => [
                        "string",
                    ],
                    "default" => "",
                ],
                "email" => [
                    "label" => "Email",
                    "type" => "string",
                    "required" => true,
                    "validation" => [
                        "string",
                        "email",
                        "unique",
                    ],
                    "default" => "",
                ],
                "password" => [
                    "label" => "Password",
                    "type" => "password",
                    "required" => true,
                    "validation" => [],
                    "default" => null,
                ],
                "newPassword" => [
                    "label" => "New Password",
                    "type" => "password",
                    "required" => false,
                    "validation" => [],
                    "default" => null,
                ],
                "repeatNewPassword" => [
                    "label" => "Repeat New Password",
                    "type" => "password",
                    "required" => false,
                    "validation" => [],
                    "default" => null,
                ],
                "role" => [
                    "label" => "Role",
                    "type" => "string",
                    "required" => false,
                    "validation" => [
                        "string",
                    ],
                    "default" => "standard",
                ],
                "admin" => [
                    "label" => "Admin",
                    "type" => "boolean",
                    "required" => false,
                    "validation" => [
                        "boolean",
                    ],
                ],
                "passwordForgot" => [
                    "label" => "Password Forgot",
                    "type" => "boolean",
                    "required" => false,
                    "default" => false,
                ],
                "passwordResetToken" => [
                    "label" => "Password Reset Token",
                    "type" => "string",
                    "required" => false,
                    "default" => "",
                ],
                "passwordResetTime" => [
                    "label" => "Password Reset Time",
                    "type" => "integer",
                    "required" => false,
                    "default" => 0,
                ],
            ],
        ],
        "Test" => [
            "collection" => "tests",
            "authenticatable" => true,
            "authStrategy" => "password",
            "credentials" => [
                "email",
                "password",
            ],
            "aclRoleField" => "role",
            "fields" => [
                "_id" => [
                    "primaryKey" => true,
                    "label" => "ID",
                    "type" => "string",
                    "disabled" => true,
                    "required" => false,
                    "default" => "",
                ],
                "name" => [
                    "label" => "Name",
                    "type" => "string",
                    "required" => true,
                    "validation" => [
                        "string",
                    ],
                    "default" => "",
                ],
                "email" => [
                    "label" => "Email",
                    "type" => "string",
                    "required" => true,
                    "validation" => [
                        "string",
                        "email",
                        "unique",
                    ],
                    "default" => "",
                ],
                "password" => [
                    "label" => "Password",
                    "type" => "password",
                    "required" => true,
                    "validation" => [],
                    "default" => null,
                ],
                "newPassword" => [
                    "label" => "New Password",
                    "type" => "password",
                    "required" => false,
                    "validation" => [],
                    "default" => null,
                ],
                "repeatNewPassword" => [
                    "label" => "Repeat New Password",
                    "type" => "password",
                    "required" => false,
                    "validation" => [],
                    "default" => null,
                ],
                "role" => [
                    "label" => "Role",
                    "type" => "string",
                    "required" => false,
                    "validation" => [
                        "string",
                    ],
                    "default" => "",
                ],
                "admin" => [
                    "label" => "Admin",
                    "type" => "boolean",
                    "required" => false,
                    "validation" => [
                        "boolean",
                    ],
                ],
                "passwordForgot" => [
                    "label" => "Password Forgot",
                    "type" => "boolean",
                    "required" => false,
                    "default" => false,
                ],
                "passwordResetToken" => [
                    "label" => "Password Reset Token",
                    "type" => "string",
                    "required" => false,
                    "default" => "",
                ],
                "passwordResetTime" => [
                    "label" => "Password Reset Time",
                    "type" => "integer",
                    "required" => false,
                    "default" => 0,
                ],
            ],
        ],
    ],
    "acl" => [
        "routes" => [
            "public" => [
                "GET" => [],
                "POST" => [
                    [
                        "route" => "/login",
                        "allows" => [
                            "admin",
                            "standard",
                            "guest",
                        ],
                    ],
                    [
                        "route" => "/forgotPassword",
                        "allows" => [
                            "admin",
                            "standard",
                            "guest",
                        ],
                    ],
                    [
                        "route" => "/resetPassword",
                        "allows" => [
                            "admin",
                            "standard",
                            "guest",
                        ],
                    ],
                ],
                "PUT" => [],
                "PATCH" => [],
                "DELETE" => [],
            ],
            "private" => [
                "GET" => [
                    [
                        "route" => "/users",
                        "allows" => [
                            "admin",
                            "standard",
                        ],
                    ],
                ],
                "POST" => [],
                "PUT" => [],
                "PATCH" => [],
                "DELETE" => [],
            ],
        ],
        "roles" => [
            "admin" => [
                "permissions" => [],
            ],
            "standard" => [
                "permissions" => [],
            ],
            "guest" => [
                "permissions" => [],
            ],
        ],
    ],
];
