<?php

namespace App\Provider\Acme;

use App\Model\Request\CustomerRequest;
use App\Provider\InsuranceProviderMapperInterface;
use DateTime;

class AcmeMapper implements InsuranceProviderMapperInterface
{
    private const YES = 'S';
    private const NO = 'N';

    public function map(CustomerRequest $customerRequest): AcmeRequest
    {
        $acmeRequest = new AcmeRequest();
        $this->mapIsMainDriver($customerRequest, $acmeRequest);
        $this->mapHasOtherDrivers($customerRequest, $acmeRequest);
        $this->mapNumberOfAdditionalDrivers($customerRequest, $acmeRequest);
        $this->mapPreviousInsuranceValidity($customerRequest, $acmeRequest);
        $this->mapPreviousInsuranceFullYears($customerRequest, $acmeRequest);
        $acmeRequest->setFecCot((new DateTime())->format('Y-m-d\TH:i:s'));

        return $acmeRequest;
    }

    private function mapIsMainDriver(CustomerRequest $request, AcmeRequest $acmeRequest): void {
        $acmeRequest->setCondPpalEsTomador(
            $request->getHolder() === 'CONDUCTOR_PRINCIPAL' ? self::YES : self::NO
        );
    }

    private function mapHasOtherDrivers(CustomerRequest $request, AcmeRequest $acmeRequest): void {
        $acmeRequest->setConductorUnico(
            $request->getOccasionalDriver() === 'NO' ? self::NO : self::YES
        );
    }

    private function mapNumberOfAdditionalDrivers(CustomerRequest $request, AcmeRequest $acmeRequest): void {
        $acmeRequest->setNroCondOca(
            $request->getOccasionalDriver() === 'SI' ? 1 : 0
        );
    }

    private function mapPreviousInsuranceValidity(CustomerRequest $request, AcmeRequest $acmeRequest): void {
        if ($request->getPrevInsuranceExists() === 'SI' && $request->getPrevInsuranceExpirationDate()) {
            $expirationDate = new DateTime($request->getPrevInsuranceExpirationDate());
            $now = new DateTime();
            $acmeRequest->setSeguroEnVigor($expirationDate > $now ? self::YES : self::NO);
        } else {
            $acmeRequest->setSeguroEnVigor(self::NO);
        }
    }

    private function mapPreviousInsuranceFullYears(CustomerRequest $request, AcmeRequest $acmeRequest): void {
        $acmeRequest->setAnosSegAnte(
            $request->getPrevInsuranceYears()
        );
    }
}