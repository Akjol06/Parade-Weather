<?php

namespace App\Security;

use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class JwtUserAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private UserRepository $userRepository,
        private string $jwtSecret,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('user-token');
    }

    public function authenticate(Request $request): Passport
    {
        $jwt = $request->headers->get('user-token');

        if (!$jwt) {
            throw new CustomUserMessageAuthenticationException('JWT токен отсутствует');
        }

        try {
            $payload = JWT::decode($jwt, new Key($this->jwtSecret, 'HS256'));
        } catch (\Throwable $e) {
            throw new CustomUserMessageAuthenticationException('Невалидный JWT токен');
        }

        $identifier = $payload->identifier ?? null;

        if (!$identifier) {
            throw new CustomUserMessageAuthenticationException('Поле "identifier" отсутствует в токене');
        }

        return new SelfValidatingPassport(
            new UserBadge($identifier, function () use ($identifier) {
                if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                    $user = $this->userRepository->findOneBy(['email' => $identifier]);
                } else {
                    $user = $this->userRepository->findOneBy(['phone' => $identifier]);
                }

                if (!$user) {
                    throw new CustomUserMessageAuthenticationException('Пользователь не найден');
                }

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?JsonResponse
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse([
            'error' => 'Ошибка JWT аутентификации',
            'message' => $exception->getMessage(),
        ], 401);
    }
}
