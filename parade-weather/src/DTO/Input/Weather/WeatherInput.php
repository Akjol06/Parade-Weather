<?php

namespace App\DTO\Input\Weather;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class WeatherInput
{
    #[Assert\NotBlank]
    #[Groups(['weather:write'])]
    public ?string $location = null;

    #[Assert\NotBlank]
    #[Groups(['weather:write'])]
    public \DateTimeImmutable $startDate;

    #[Assert\NotBlank]
    #[Groups(['weather:write'])]
    public \DateTimeImmutable $endDate;
}
