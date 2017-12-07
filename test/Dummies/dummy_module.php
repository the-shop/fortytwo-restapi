<?php

use Framework\Base\Test\Dummies\TestDatabaseAdapter;
use Framework\Base\Test\Dummies\TestModel;
use Framework\Base\Test\Dummies\TestRepository;

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
    "models" => [
        "User" => [
            "collection" => "users",
            "authenticatable" => true,
            "authStrategy" => "password",
            "credentials" => [
                "email",
                "password"
            ],
            "aclRoleField" => "role",
            "fields" => [
                "_id" => [
                    "primaryKey" => true,
                    "label" => "ID",
                    "type" => "string",
                    "disabled" => true,
                    "required" => false,
                    "default" => ""
                ],
                "name" => [
                    "label" => "Name",
                    "type" => "string",
                    "required" => true,
                    "validation" => [
                        "string"
                    ],
                    "default" => ""
                ],
                "email" => [
                    "label" => "Email",
                    "type" => "string",
                    "required" => true,
                    "validation" => [
                        "string",
                        "email",
                        "unique"
                    ],
                    "default" => ""
                ],
                "password" => [
                    "label" => "Password",
                    "type" => "password",
                    "required" => true,
                    "validation" => [],
                    "default" => null
                ],
                "role" => [
                    "label" => "Role",
                    "type" => "string",
                    "required" => false,
                    "validation" => [
                        "string"
                    ],
                    "default" => ""
                ],
                "admin" => [
                    "label" => "Admin",
                    "type" => "boolean",
                    "required" => false,
                    "validation" => [
                        "boolean"
                    ],
                ],
            ],
        ],
        "Test" => [
            "collection" => "tests",
            "authenticatable" => true,
            "authStrategy" => "password",
            "credentials" => [
                "email",
                "password"
            ],
            "aclRoleField" => "role",
            "fields" => [
                "_id" => [
                    "primaryKey" => true,
                    "label" => "ID",
                    "type" => "string",
                    "disabled" => true,
                    "required" => false,
                    "default" => ""
                ],
                "name" => [
                    "label" => "Name",
                    "type" => "string",
                    "required" => true,
                    "validation" => [
                        "string"
                    ],
                    "default" => ""
                ],
                "email" => [
                    "label" => "Email",
                    "type" => "string",
                    "required" => true,
                    "validation" => [
                        "string",
                        "email",
                        "unique"
                    ],
                    "default" => ""
                ],
                "password" => [
                    "label" => "Password",
                    "type" => "password",
                    "required" => true,
                    "validation" => [],
                    "default" => null
                ],
                "role" => [
                    "label" => "Role",
                    "type" => "string",
                    "required" => false,
                    "validation" => [
                        "string"
                    ],
                    "default" => ""
                ],
                "admin" => [
                    "label" => "Admin",
                    "type" => "boolean",
                    "required" => false,
                    "validation" => [
                        "boolean"
                    ],
                ],
            ],
        ],
    ],
    "acl" => [
        "routes" => [
            "public" => [
                "GET" => [
                    [
                        "route" => "/login",
                        "allows" => [
                            "admin",
                            "standard",
                            "guest",
                        ],
                    ],
                ],
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
