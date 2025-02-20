<?php

namespace App\Provider\Acme;

use App\Exception\ProviderException;
use App\Model\Request\CustomerRequest;
use App\Provider\InsuranceProviderInterface;
use App\Provider\InsuranceProviderMapperInterface;
use SimpleXMLElement;

class AcmeProvider implements InsuranceProviderInterface
{
    private InsuranceProviderMapperInterface $mapper;

    public function __construct()
    {
        $this->mapper = new AcmeMapper();
    }

    public function transformRequest(CustomerRequest $request): AcmeRequest
    {
        return $this->mapper->map($request);
    }

    /**
     * @throws ProviderException
     */
    public function formatResponse(object $request): string
    {
        if (!$request instanceof AcmeRequest) {
            throw new ProviderException('Invalid request type for ACME provider');
        }

        try {
            return $this->generateXml($request);
        } catch (\Exception $e) {
            throw new ProviderException('Failed to format response: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getOutputFormat(): string
    {
        return 'xml';
    }

    private function generateXml(AcmeRequest $request): string
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><TarificacionThirdPartyRequest/>');

        $datos = $xml->addChild('Datos');
        $this->addGeneralData($datos->addChild('DatosGenerales'), $request);
        $this->addInsuranceData($datos->addChild('DatosAseguradora'), $request);

        return $xml->asXML();
    }

    private function addGeneralData(SimpleXMLElement $element, AcmeRequest $request): void
    {
        $element->addChild('CondPpalEsTomador', $request->getCondPpalEsTomador());
        $element->addChild('ConductorUnico', $request->getConductorUnico());
        $element->addChild('FecCot', $request->getFecCot());
        $element->addChild('AnosSegAnte', (string)$request->getAnosSegAnte());
        $element->addChild('NroCondOca', (string)$request->getNroCondOca());
    }

    private function addInsuranceData(SimpleXMLElement $element, AcmeRequest $request): void
    {
        $element->addChild('SeguroEnVigor', $request->getSeguroEnVigor());
    }
}