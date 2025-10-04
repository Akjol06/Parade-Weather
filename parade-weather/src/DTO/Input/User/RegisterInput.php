<?php

namespace App\DTO\Input\User;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterInput
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[ApiProperty(openapiContext: ['example' => 'example@gmail.com'])]
    #[Groups(['user:read', 'user:write'])]
    public ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 6,
        minMessage: 'Password must contain at least {{ limit }} characters.'
    )]
    #[ApiProperty(openapiContext: ['example' => 'Pass1234'])]
    #[Groups(['user:read', 'user:write'])]
    public ?string $password = null;
}
