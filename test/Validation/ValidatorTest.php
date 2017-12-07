<?php

namespace Framework\RestApiTest\Validation;

use Framework\Base\Application\Exception\ValidationException;
use Framework\Base\Validation\Validator;
use Framework\RestApi\Test\UnitTest;

/**
 * Class ValidatorTest
 * @package Framework\RestApiTest\Validation
 */
class ValidatorTest extends UnitTest
{
    /**
     * Test alphabetic validation - failed
     */
    public function testAlphabeticValidationFailed()
    {
        $validator = new Validator();

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $validator->addValidation(1212, 'alphabetic')
                  ->validate();
    }

    /**
     * Test alphabetic validation - success
     */
    public function testAlphabeticValidationSuccess()
    {
        $validator = new Validator();

        $validator->addValidation('test', 'alphabetic')
                  ->validate();

        $this->assertEquals([], $validator->getFailed());
    }

    /**
     * Test array validation - failed
     */
    public function testArrayValidationFailed()
    {
        $validator = new Validator();

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $validator->addValidation('test', 'array')
                  ->validate();
    }

    /**
     * Test array validation - success
     */
    public function testArrayValidationSuccess()
    {
        $validator = new Validator();

        $validator->addValidation(['test'], 'array')
                  ->validate();

        $this->assertEquals([], $validator->getFailed());
    }

    /**
     * Test boolean validation - failed
     */
    public function testBooleanValidationFailed()
    {
        $validator = new Validator();

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $validator->addValidation('test', 'boolean')
                  ->validate();
    }

    /**
     * Test boolean validation - success
     */
    public function testBooleanValidationSuccess()
    {
        $validator = new Validator();

        $validator->addValidation(true, 'boolean')
                  ->validate();

        $this->assertEquals([], $validator->getFailed());
    }

    /**
     * Test email validation - failed
     */
    public function testEmailValidationFailed()
    {
        $validator = new Validator();
        $value = 'test@121212.1212';

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $validator->addValidation($value, 'email')
                  ->validate();
    }

    /**
     * Test email validation - success
     */
    public function testEmailValidationSuccess()
    {
        $validator = new Validator();

        $validator->addValidation('test@test.com', 'email')
                  ->validate();

        $this->assertEquals([], $validator->getFailed());
    }

    /**
     * Test float validation - failed
     */
    public function testFloatValidationFailed()
    {
        $validator = new Validator();
        $value = 'foo';

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $validator->addValidation($value, 'float')
                  ->validate();
    }

    /**
     * Test float validation - success
     */
    public function testFloatValidationSuccess()
    {
        $validator = new Validator();

        $validator->addValidation(12.12, 'float')
                  ->validate();

        $this->assertEquals([], $validator->getFailed());
    }

    /**
     * Test integer validation - failed
     */
    public function testIntegerValidationFailed()
    {
        $validator = new Validator();
        $value = 12.12;

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $validator->addValidation($value, 'integer')
                  ->validate();
    }

    /**
     * Test integer validation - success
     */
    public function testIntegerValidationSuccess()
    {
        $validator = new Validator();

        $validator->addValidation(12, 'integer')
                  ->validate();

        $this->assertEquals([], $validator->getFailed());
    }

    /**
     * Test nonEmpty validation - failed
     */
    public function testNonEmptyValidationFailed()
    {
        $validator = new Validator();
        $value = "";

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $validator->addValidation($value, 'nonempty')
                  ->validate();
    }

    /**
     * Test nonEmpty validation - success
     */
    public function testNonEmptyValidationSuccess()
    {
        $validator = new Validator();

        $validator->addValidation(12.12, 'nonempty')
                  ->validate();

        $this->assertEquals([], $validator->getFailed());
    }

    /**
     * Test string validation - failed
     */
    public function testStringValidationFailed()
    {
        $validator = new Validator();
        $value = true;

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $validator->addValidation($value, 'string')
                  ->validate();
    }

    /**
     * Test string validation - success
     */
    public function testStringValidationSuccess()
    {
        $validator = new Validator();

        $validator->addValidation('test', 'string')
                  ->validate();

        $this->assertEquals([], $validator->getFailed());
    }

    /**
     * Test alpha_dash validation - failed
     */
    public function testAlphaDashValidationFail()
    {
        $validator = new Validator();
        $value = 'test*/..+';

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $validator->addValidation($value, 'alpha_dash')
                  ->validate();
    }

    /**
     * Test alpha_dash validation - success
     */
    public function testAlphaDashValidationSuccess()
    {
        $validator = new Validator();

        $validator->addValidation('test-test', 'alpha_dash')
                  ->validate();

        $this->assertEquals([], $validator->getFailed());
    }

    /**
     * Test numeric validation - failed
     */
    public function testNumericValidationFail()
    {
        $validator = new Validator();
        $value = 'test1';

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $validator->addValidation($value, 'numeric')
                  ->validate();
    }

    /**
     * Test numeric validation - success
     */
    public function testNumericValidationSuccess()
    {
        $validator = new Validator();

        $validator->addValidation('123.32', 'numeric')
                  ->validate();

        $this->assertEquals([], $validator->getFailed());
    }

    /**
     * Test alpha_numeric validation - failed
     */
    public function testAlphaNumericValidationFail()
    {
        $validator = new Validator();
        $value = 'test112-';

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $validator->addValidation($value, 'alpha_numeric')
                  ->validate();
    }

    /**
     * Test alpha_numeric validation - success
     */
    public function testAlphaNumericValidationSuccess()
    {
        $validator = new Validator();

        $validator->addValidation('test12332', 'alpha_numeric')
                  ->validate();

        $this->assertEquals([], $validator->getFailed());
    }

    /**
     * Test multiple validations with validator with some failed and some success validations
     */
    public function testValidatorMultipleValidations()
    {
        $validator = new Validator();
        $value = 'test';
        $number = 12;

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Validation failed');

        $validator->addValidation($value, 'string')
                  ->addValidation($number, 'integer')
                  ->addValidation($value, 'array')
                  ->addValidation($value, 'alphabetic')
                  ->addValidation($value, 'boolean')
                  ->addValidation($value, 'email')
                  ->addValidation($number, 'float')
                  ->addValidation($number, 'nonempty')
                  ->addValidation($number, 'alpha_numeric')
                  ->addValidation($value, 'numeric')
                  ->addValidation($number, 'alpha_dash')
                  ->validate();
    }
}
