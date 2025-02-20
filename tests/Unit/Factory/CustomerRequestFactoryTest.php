<?php

declare(strict_types=1);

namespace Tests\Unit\Factory;

use App\Factory\CustomerRequestFactory;
use App\Model\Request\CustomerRequest;
use App\Exception\ValidationException;
use PHPUnit\Framework\TestCase;

class CustomerRequestFactoryTest extends TestCase
{
    /** @test */
    public function test_create_customer_request_from_valid_json(): void
    {
        $json = <<<JSON
        {
            "holder": "CONDUCTOR_PRINCIPAL",
            "occasionalDriver": "NO",
            "prevInsurance_exists": "NO",
            "prevInsurance_years": 10
        }
        JSON;

        $request = CustomerRequestFactory::createFromJson($json);

        $this->assertInstanceOf(CustomerRequest::class, $request);
        $this->assertEquals('CONDUCTOR_PRINCIPAL', $request->getHolder());
        $this->assertEquals('NO', $request->getOccasionalDriver());
        $this->assertEquals('NO', $request->getPrevInsuranceExists());
        $this->assertNull($request->getPrevInsuranceExpirationDate());
        $this->assertEquals(10, $request->getPrevInsuranceYears());
    }

    /** @test */
    public function test_create_customer_request_from_valid_array(): void
    {
        $data = [
            'holder' => 'CONDUCTOR_PRINCIPAL',
            'occasionalDriver' => 'SI',
            'prevInsurance_exists' => 'SI',
            'prevInsurance_expirationDate' => '2024-03-14',
            'prevInsurance_years' => 5
        ];

        $request = CustomerRequestFactory::createFromArray($data);

        $this->assertInstanceOf(CustomerRequest::class, $request);
        $this->assertEquals('CONDUCTOR_PRINCIPAL', $request->getHolder());
        $this->assertEquals('SI', $request->getOccasionalDriver());
        $this->assertEquals('SI', $request->getPrevInsuranceExists());
        $this->assertEquals('2024-03-14', $request->getPrevInsuranceExpirationDate());
        $this->assertEquals('5', $request->getPrevInsuranceYears());
    }

    /** @test */
    public function tetst_throws_exception_for_invalid_json(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid request format');

        CustomerRequestFactory::createFromJson('{invalid json}');
    }

    /** @test */
    public function test_throws_exception_for_required_holder_value(): void
    {
        $this->expectException(ValidationException::class);

        $data = [
            'occasionalDriver' => 'SI',
            'prevInsurance_exists' => 'SI',
            'prevInsurance_expirationDate' => '2024-03-14',
            'prevInsurance_years' => 5
        ];

        CustomerRequestFactory::createFromArray($data);
    }

    /** @test */
    public function test_throws_exception_for_invalid_holder_value(): void
    {
        $this->expectException(ValidationException::class);

        $data = [
            'holder' => 'INVALID_VALUE',
            'occasionalDriver' => 'SI',
            'prevInsurance_exists' => 'SI',
            'prevInsurance_expirationDate' => '2024-03-14',
            'prevInsurance_years' => 5
        ];

        CustomerRequestFactory::createFromArray($data);
    }

    /** @test */
    public function test_throws_exception_for_required_occasional_driver_value(): void
    {
        $this->expectException(ValidationException::class);

        $data = [
            "holder" => "CONDUCTOR_PRINCIPAL",
            'prevInsurance_exists' => 'SI',
            'prevInsurance_expirationDate' => '2024-03-14',
            'prevInsurance_years' => 5
        ];

        CustomerRequestFactory::createFromArray($data);
    }

    /** @test */
    public function test_throws_exception_for_invalid_occasional_driver_value(): void
    {
        $this->expectException(ValidationException::class);

        $data = [
            "holder" => "CONDUCTOR_PRINCIPAL",
            'occasionalDriver' => 'INVALID_VALUE',
            'prevInsurance_exists' => 'SI',
            'prevInsurance_expirationDate' => '2024-03-14',
            'prevInsurance_years' => 5
        ];

        CustomerRequestFactory::createFromArray($data);
    }

    /** @test */
    public function test_throws_exception_for_required_prev_insurance_exists_value(): void
    {
        $this->expectException(ValidationException::class);

        $data = [
            "holder" => "CONDUCTOR_PRINCIPAL",
            'occasionalDriver' => 'SI',
            'prevInsurance_expirationDate' => '2024-03-14',
            'prevInsurance_years' => 5
        ];

        CustomerRequestFactory::createFromArray($data);
    }

    /** @test */
    public function test_throws_exception_for_invalid_prev_insurance_exists_value(): void
    {
        $this->expectException(ValidationException::class);

        $data = [
            "holder" => "CONDUCTOR_PRINCIPAL",
            'occasionalDriver' => 'SI',
            'prevInsurance_exists' => 'INVALID_VALUE',
            'prevInsurance_expirationDate' => '2024-03-14',
            'prevInsurance_years' => 5
        ];

        CustomerRequestFactory::createFromArray($data);
    }

    /** @test */
    public function test_throws_exception_for_required_prev_insurance_expiration_date_value(): void
    {
        $this->expectException(ValidationException::class);

        $data = [
            "holder" => "CONDUCTOR_PRINCIPAL",
            'occasionalDriver' => 'SI',
            'prevInsurance_exists' => 'SI',
            'prevInsurance_years' => 5
        ];

        CustomerRequestFactory::createFromArray($data);
    }

    /** @test */
    public function test_throws_exception_for_invalid_prev_insurance_expiration_date_value(): void
    {
        $this->expectException(ValidationException::class);

        $data = [
            "holder" => "CONDUCTOR_PRINCIPAL",
            'occasionalDriver' => 'SI',
            'prevInsurance_exists' => 'SI',
            'prevInsurance_expirationDate' => 'INVALID_VALUE',
            'prevInsurance_years' => 5
        ];

        CustomerRequestFactory::createFromArray($data);
    }

    /** @test */
    public function test_throws_exception_for_required_prev_insurance_years_value(): void
    {
        $this->expectException(ValidationException::class);

        $data = [
            "holder" => "CONDUCTOR_PRINCIPAL",
            'occasionalDriver' => 'SI',
            'prevInsurance_exists' => 'SI',
            'prevInsurance_expirationDate' => '2024-03-14',
        ];

        CustomerRequestFactory::createFromArray($data);
    }

    /** @test */
    public function test_throws_exception_for_invalid_prev_insurance_years_value(): void
    {
        $this->expectException(ValidationException::class);

        $data = [
            "holder" => "CONDUCTOR_PRINCIPAL",
            'occasionalDriver' => 'SI',
            'prevInsurance_exists' => 'SI',
            'prevInsurance_expirationDate' => '2024-03-14',
            'prevInsurance_years' => 'INVALID_VALUE'
        ];

        CustomerRequestFactory::createFromArray($data);
    }
}