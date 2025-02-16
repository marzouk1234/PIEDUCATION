<?php
namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class RolesDataTransformer implements DataTransformerInterface
{
    // Corrected method signature to be compatible with Symfony 5.4+.
    public function transform(mixed $value): mixed
    {
        // If the value is an array, return the first role
        return $value ? $value[0] : null;
    }

    public function reverseTransform(mixed $value): mixed
    {
        // If the value is a string, return it as an array
        return $value ? [$value] : [];
    }
}
