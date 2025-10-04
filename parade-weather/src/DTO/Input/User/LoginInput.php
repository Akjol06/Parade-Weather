<?php

namespace App\DTO\Input\User;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class LoginInput
{
    #[Assert\NotBlank]
    #[Groups(['login'])]
    public string $email;

    #[Assert\NotBlank(message: 'The "Password" field is required.')]
    #[Assert\Length(
        min: 6,
        minMessage: 'Password must contain at least {{ limit }} characters.'
    )]
    #[Groups(['login'])]
    public string $password;
}
