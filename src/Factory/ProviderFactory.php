<?php

namespace App\Factory;

use App\Provider\InsuranceProviderInterface;
use App\Provider\Acme\AcmeProvider;
use InvalidArgumentException;

class ProviderFactory
{
    private static ?self $instance = null;
    private array $providers = [];

    private function __construct()
    {
        $this->providers['acme'] = new AcmeProvider();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getProvider(string $name): InsuranceProviderInterface
    {
        if (!isset($this->providers[$name])) {
            throw new InvalidArgumentException("Provider '$name' has not registered yet!");
        }
        return $this->providers[$name];
    }
}