<?php

namespace Fhoke\Bluetown;

class Validator
{
    public static function requiredFields(array $required, array $data)
    {
        \array_map(function ($required_item) use ($data) {
            if (!isset($data[$required_item])) {
                $field_name = \ucfirst(\str_replace('_', ' ', $required_item));

                throw new \InvalidArgumentException("{$field_name} is required.");
            }
        }, $required);
    }

    public static function passwordsMatch(string $password, string $password_confirm)
    {
        if ($password !== $password_confirm) {
            throw new \Exception("The passwords don't match.");
        }
    }

    public static function cvFile(array $file_attrs)
    {
        if (!in_array($file_attrs['type'], ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])) {
            throw new \Exception("Only PDF, DOC and DOCX files are allowed.");
        }

        if ($file_attrs['size'] > 5000000) {
            throw new \Exception("Only files 5MB and under are allowed.");
        }
    }
}
