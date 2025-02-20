<?php

namespace App\Provider\Acme;


class AcmeRequest
{
    private string $condPpalEsTomador;
    private string $conductorUnico;
    private string $fecCot;
    private int $anosSegAnte;
    private int $nroCondOca;
    private string $seguroEnVigor;

    public function setCondPpalEsTomador(string $value): self
    {
        $this->condPpalEsTomador = $value;
        return $this;
    }

    public function setConductorUnico(string $value): self
    {
        $this->conductorUnico = $value;
        return $this;
    }

    public function setFecCot(string $value): self
    {
        $this->fecCot = $value;
        return $this;
    }

    public function setAnosSegAnte(int $value): self
    {
        $this->anosSegAnte = $value;
        return $this;
    }

    public function setNroCondOca(int $value): self
    {
        $this->nroCondOca = $value;
        return $this;
    }

    public function setSeguroEnVigor(string $value): self
    {
        $this->seguroEnVigor = $value;
        return $this;
    }

    public function getCondPpalEsTomador(): string
    {
        return $this->condPpalEsTomador;
    }

    public function getConductorUnico(): string
    {
        return $this->conductorUnico;
    }

    public function getFecCot(): string
    {
        return $this->fecCot;
    }

    public function getAnosSegAnte(): int
    {
        return $this->anosSegAnte;
    }

    public function getNroCondOca(): int
    {
        return $this->nroCondOca;
    }

    public function getSeguroEnVigor(): string
    {
        return $this->seguroEnVigor;
    }
}