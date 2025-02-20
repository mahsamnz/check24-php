<?php

namespace App\Model\Request;

use Symfony\Component\Validator\Constraints as Assert;

class CustomerRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Holder is required')]
        #[Assert\Choice(
            choices: ['CONDUCTOR_PRINCIPAL', 'NON_CONDUCTOR_PRINCIPAL'],
            message: 'Invalid holder value. Must be either CONDUCTOR_PRINCIPAL or NON_CONDUCTOR_PRINCIPAL'
        )]
        private string $holder,

        #[Assert\NotBlank(message: 'OccasionalDriver is required')]
        #[Assert\Choice(
            choices: ['SI', 'NO'],
            message: 'OccasionalDriver must be either SI or NO'
        )]
        private string $occasionalDriver,

        #[Assert\NotBlank(message: 'PrevInsurance exists is required')]
        #[Assert\Choice(
            choices: ['SI', 'NO'],
            message: 'PrevInsurance exists must be either SI or NO'
        )]
        private string $prevInsurance_exists,

        #[Assert\When(
            expression: "this.getPrevInsuranceExists() === 'SI'",
            constraints: [
                new Assert\NotBlank(message: 'PrevInsurance expiration date is required when previous insurance exists'),
                new Assert\Regex([
                    'pattern' => '/^\d{4}-\d{2}-\d{2}$/',
                    'message' => 'Date must be in YYYY-MM-DD format'
                ])
            ]
        )]
        private ?string $prevInsurance_expirationDate,

        #[Assert\NotBlank(message: 'PrevInsurance years is required')]
        #[Assert\Type(
            type: 'integer',
            message: 'PrevInsurance years must be an integer'
        )]
        private ?int $prevInsurance_years
    ) {}

    /**
     * @return string
     */
    public function getHolder(): string
    {
        return $this->holder;
    }

    /**
     * @return string
     */
    public function getOccasionalDriver(): string
    {
        return $this->occasionalDriver;
    }

    /**
     * @return string
     */
    public function getPrevInsuranceExists(): string
    {
        return $this->prevInsurance_exists;
    }

    /**
     * @return string|null
     */
    public function getPrevInsuranceExpirationDate(): ?string
    {
        return $this->prevInsurance_expirationDate;
    }

    /**
     * @return int|null
     */
    public function getPrevInsuranceYears(): ?int
    {
        return $this->prevInsurance_years;
    }

}