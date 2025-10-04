<?php

namespace App\Controller\Api\Auth;

use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private string $jwtSecret,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = $request->toArray();

            $email = $data['email'] ?? null;
            $password = $data['password'] ?? null;

            if (!$email || !$password) {
                return new JsonResponse(['error' => 'Email и пароль обязательны'], 400);
            }

            $user = $this->userRepository->findOneBy(['email' => $email]);

            if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
                return new JsonResponse(['error' => 'Неверный email или пароль'], 401);
            }

            $token = JWT::encode([
                'identifier' => $user->getUserIdentifier(),
                'exp' => time() + 86400 * 7, // 7 дней
            ], $this->jwtSecret, 'HS256');

            return new JsonResponse([
                'token' => $token,
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'roles' => $user->getRoles(),
                ],
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
