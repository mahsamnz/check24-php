<?php

declare(strict_types=1);

namespace Tests\Unit\Provider;


use App\Model\Request\CustomerRequest;
use App\Provider\Acme\AcmeProvider;
use App\Provider\Acme\AcmeRequest;
use DateTime;
use PHPUnit\Framework\TestCase;
use Mockery;

class AcmeProviderTest extends TestCase
{
    private AcmeProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new AcmeProvider();
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    public function test_transforms_request_without_previous_insurance(): void
    {
        $customerRequest = new CustomerRequest(
            holder: 'CONDUCTOR_PRINCIPAL',
            occasionalDriver: 'NO',
            prevInsurance_exists: 'NO',
            prevInsurance_expirationDate: null,
            prevInsurance_years: 8
        );

        $result = $this->provider->transformRequest($customerRequest);

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
    public function test_transforms_request_with_previous_insurance(): void
    {
        $customerRequest = new CustomerRequest(
            holder: 'CONDUCTOR_PRINCIPAL',
            occasionalDriver: 'SI',
            prevInsurance_exists: 'SI',
            prevInsurance_expirationDate: (new DateTime('+1 month'))->format('Y-m-d'),
            prevInsurance_years: 8
        );

        $result = $this->provider->transformRequest($customerRequest);

        $this->assertInstanceOf(AcmeRequest::class, $result);
        $this->assertEquals('S', $result->getCondPpalEsTomador());
        $this->assertEquals('S', $result->getConductorUnico());
        $this->assertEquals(1, $result->getNroCondOca());
        $this->assertEquals('S', $result->getSeguroEnVigor());
        $this->assertEquals(8, $result->getAnosSegAnte());
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}$/',
            $result->getFecCot()
        );
    }

    /** @test */
    public function test_format_response(): void
    {
        $providerRequest = new AcmeRequest();
        $providerRequest->setCondPpalEsTomador('S');
        $providerRequest->setConductorUnico('N');
        $providerRequest->setFecCot((new DateTime())->format('Y-m-d\TH:i:s'));
        $providerRequest->setAnosSegAnte(8);
        $providerRequest->setNroCondOca(0);
        $providerRequest->setSeguroEnVigor('N');

        $result = $this->provider->formatResponse($providerRequest);

        $this->assertStringStartsWith('<?xml', $result);
        $this->assertStringContainsString('<TarificacionThirdPartyRequest>', $result);
        $this->assertStringContainsString('<CondPpalEsTomador>S</CondPpalEsTomador>', $result);
        $this->assertStringContainsString('<ConductorUnico>N</ConductorUnico>', $result);
        $this->assertStringContainsString('<AnosSegAnte>8</AnosSegAnte>', $result);
        $this->assertStringContainsString('<NroCondOca>0</NroCondOca>', $result);
        $this->assertStringContainsString('<SeguroEnVigor>N</SeguroEnVigor>', $result);
    }

    /** @test */
    public function test_get_output_format(): void
    {
        $this->assertEquals('xml', $this->provider->getOutputFormat());
    }
}