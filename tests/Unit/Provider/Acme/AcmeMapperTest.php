<?php

namespace Tests\Unit\Provider\Acme;

use App\Model\Request\CustomerRequest;
use App\Provider\Acme\AcmeMapper;
use App\Provider\Acme\AcmeRequest;
use DateTime;
use PHPUnit\Framework\TestCase;

class AcmeMapperTest extends TestCase
{
    private AcmeMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new AcmeMapper();
    }

    /** @test */
    public function test_map_customer_request_to_acme_request_with_no_previous_insurance(): void
    {
        // Arrange
        $customerRequest = new CustomerRequest(
            holder: 'CONDUCTOR_PRINCIPAL',
            occasionalDriver: 'NO',
            prevInsurance_exists: 'NO',
            prevInsurance_expirationDate: null,
            prevInsurance_years: 8
        );

        // Act
        $result = $this->mapper->map($customerRequest);

        // Assert
        $this->assertInstanceOf(AcmeRequest::class, $result);
        $this->assertEquals('S', $result->getCondPpalEsTomador());
        $this->assertEquals('N', $result->getConductorUnico());
        $this->assertEquals(0, $result->getNroCondOca());
        $this->assertEquals('N', $result->getSeguroEnVigor());
        $this->assertEquals(8, $result->getAnosSegAnte());
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}$/',
            $result->getFecCot()
        );
    }

    /** @test */
    public function test_map_customer_request_to_acme_request_with_expired_previous_insurance(): void
    {
        // Arrange
        $customerRequest = new CustomerRequest(
            holder: 'CONDUCTOR_PRINCIPAL',
            occasionalDriver: 'NO',
            prevInsurance_exists: 'SI',
            prevInsurance_expirationDate: (new DateTime('-1 month'))->format('Y-m-d'),
            prevInsurance_years: 8
        );

        // Act
        $result = $this->mapper->map($customerRequest);

        // Assert
        $this->assertInstanceOf(AcmeRequest::class, $result);
        $this->assertEquals('S', $result->getCondPpalEsTomador());
        $this->assertEquals('N', $result->getConductorUnico());
        $this->assertEquals(0, $result->getNroCondOca());
        $this->assertEquals('N', $result->getSeguroEnVigor());
        $this->assertEquals(8, $result->getAnosSegAnte());
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}$/',
            $result->getFecCot()
        );
    }

    /** @test */
    public function test_map_customer_request_to_acme_request_with_active_previous_insurance(): void
    {
        // Arrange
        $customerRequest = new CustomerRequest(
            holder: 'NON_CONDUCTOR_PRINCIPAL',
            occasionalDriver: 'SI',
            prevInsurance_exists: 'SI',
            prevInsurance_expirationDate: (new DateTime('+1 month'))->format('Y-m-d'),
            prevInsurance_years: 5
        );

        // Act
        $result = $this->mapper->map($customerRequest);

        // Assert
        $this->assertEquals('N', $result->getCondPpalEsTomador());
        $this->assertEquals('S', $result->getConductorUnico());
        $this->assertEquals(1, $result->getNroCondOca());
        $this->assertEquals('S', $result->getSeguroEnVigor());
        $this->assertEquals(5, $result->getAnosSegAnte());
    }
}