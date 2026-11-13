<?php

namespace Codebrainbv\Smartpay\Resources;

use Codebrainbv\Smartpay\Resources\BaseResource;

class ResourceFactory
{
    /**
     * Create resource object from Api result, filtered by properties that should exist.
     *
     * @param object $apiResult
     */
    public static function createFromApiResult($apiResult, BaseResource $resource)
    {
        // Unwrap nested 'order' object if present (for Status endpoint)
        if (property_exists($apiResult, 'order') && is_object($apiResult->order)) {
            $apiResult = $apiResult->order;
        }

        // Filter by properties that should exist
        $resourceProperties = $resource->getAllowedProperties();

        // Check if the api result contains the required properties
        foreach ($resourceProperties as $property) {
            if (property_exists($apiResult, $property)) {
                $value = $apiResult->{$property};
                // Convert stdClass to array for array-typed properties
                if (is_object($value)) {
                    $value = self::objectToArray($value);
                }
                $resource->{$property} = $value;
            }
        }

        return $resource;
    }

    /**
     * Recursively convert stdClass objects to arrays.
     *
     * @param mixed $object
     * @return mixed
     */
    private static function objectToArray($object)
    {
        if (is_object($object)) {
            $object = get_object_vars($object);
        }

        if (is_array($object)) {
            return array_map(function ($item) {
                return self::objectToArray($item);
            }, $object);
        }

        return $object;
    }
}
