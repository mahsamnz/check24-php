<?php

namespace App\Factory;

use App\Exception\ValidationException;
use App\Model\Request\CustomerRequest;
use App\Service\Serializer\SerializerService;
use App\Service\Validator\ValidationService;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class CustomerRequestFactory
{
    public static function createFromJson(string $json): CustomerRequest
    {
        try {
            $serializer = SerializerService::getInstance()->getSerializer();
            
            // Deserialize JSON to CustomerRequest object
            $request = $serializer->deserialize(
                $json,
                CustomerRequest::class,
                'json'
            );

            // Validate the request
            ValidationService::getInstance()->validate($request);

            return $request;
        } catch (ExceptionInterface $e) {
            throw new ValidationException('Invalid request format: ' . $e->getMessage());
        }
    }

    public static function createFromArray(array $data): CustomerRequest
    {
        try {
            $serializer = SerializerService::getInstance()->getSerializer();
            
            // Convert array to CustomerRequest object
            $request = $serializer->denormalize(
                $data,
                CustomerRequest::class
            );

            // Validate the request
            ValidationService::getInstance()->validate($request);

            return $request;
        } catch (ExceptionInterface $e) {
            throw new ValidationException('Invalid request format: ' . $e->getMessage());
        }
    }
}