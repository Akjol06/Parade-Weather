<?php

namespace App\Controller\Api\Auth;

use App\DTO\Input\User\RegisterInput;
use App\Service\Auth\RegistrationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class RegisterController extends AbstractController
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function __invoke(Request $request, RegistrationService $registrationService): JsonResponse
    {
        $json = $request->getContent();
        $data = $this->serializer->deserialize($json, RegisterInput::class, 'json', ['groups' => ['user:write']]);

        try {
            $user = $registrationService->register($data);

            return new JsonResponse([
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'createdAt' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $user->getUpdatedAt()->format('Y-m-d H:i:s'),
            ], 201);
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}
