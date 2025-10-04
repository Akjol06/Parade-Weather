<?php

namespace App\Security;

use App\Repository\ClientRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiAuthenticator extends AbstractAuthenticator
{
    public const AUTH_TOKEN_KEY = 'api-token';

    public function __construct(
        private ClientRepository $clientRepository,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        if (!$request->headers->has(self::AUTH_TOKEN_KEY)) {
            return false;
        }

        return true;
    }

    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->headers->get(self::AUTH_TOKEN_KEY);
        if (null === $apiToken) {
            throw new CustomUserMessageAuthenticationException('API token not provided in the "api-token" header.');
        }

        $passport = new SelfValidatingPassport(
            new UserBadge(
                $apiToken,
                fn ($userIdentifier) => $this->clientRepository->findOneBy(['xAuthToken' => $userIdentifier]),
            )
        );

        try {
            $passport->getUser();

            return $passport;
        } catch (\Throwable) {
            throw new CustomUserMessageAuthenticationException('Provided API token is invalid.');
        }
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?JsonResponse
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse([
            'error' => 'Authentication error',
            'message' => $exception->getMessage(),
        ], 401);
    }
}
