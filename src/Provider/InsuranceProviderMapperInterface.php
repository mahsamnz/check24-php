<?php

namespace App\Provider;

use App\Model\Request\CustomerRequest;

interface InsuranceProviderMapperInterface
{
    public function map(CustomerRequest $customerRequest): object;
}