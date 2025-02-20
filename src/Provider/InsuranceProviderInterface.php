<?php

namespace App\Provider;

use App\Model\Request\CustomerRequest;

interface InsuranceProviderInterface
{
    public function transformRequest(CustomerRequest $request): object;

    public function formatResponse(object $request): string;

    public function getOutputFormat(): string;
}