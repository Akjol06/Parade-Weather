<?php

namespace App\Service\Auth;

use App\DTO\Input\User\RegisterInput;
use App\Entity\User;
use App\Service\ValidationHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationService
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidationHelper $validationHelper,
    ) {
    }

    public function register(RegisterInput $data): User
    {
        $repo = $this->em->getRepository(User::class);

        if ($data->email && $repo->findOneBy(['email' => $data->email])) {
            throw new \DomainException('A user with this email is already registered.');
        }

        $this->validationHelper->validateOrException($data);

        $user = new User();
        $user->setEmail($data->email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $data->password));
        $user->setRoles([User::ROLE_USER]);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
