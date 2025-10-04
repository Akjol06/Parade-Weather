<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationHelper
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    /**
     * @throws \DomainException
     */
    public function validateOrException(object $dto): void
    {
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getPropertyPath().': '.$error->getMessage();
            }
            throw new \DomainException(implode("\n", $messages));
        }
    }
}
