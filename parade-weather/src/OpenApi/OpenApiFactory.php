<?php

declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Parameter;
use ApiPlatform\OpenApi\OpenApi;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OpenApiFactory implements OpenApiFactoryInterface
{
    public const API_TOKEN_DEFINITION = [
        'api-token',
        'header',
        'Ключ доступа к API (api-token)',
        true,
        false,
        false,
        ['type' => 'string'],
    ];

    public const USER_TOKEN_DEFINITION = [
        'user_token',
        'header',
        'JWT токен пользователя (user_token)',
        true,
        false,
        false,
        ['type' => 'string'],
    ];

    public function __construct(
        private readonly OpenApiFactoryInterface $decorated,
        private readonly TranslatorInterface $translator,
        private readonly array $userTokenRequiredUseEndpoints = [],
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);
        $paths = $openApi->getPaths();

        foreach ($paths->getPaths() as $path => $pathItem) {
            $newPathItem = $pathItem;

            foreach (['get', 'post', 'put', 'patch', 'delete'] as $method) {
                $getMethod = 'get'.ucfirst($method);
                $withMethod = 'with'.ucfirst($method);

                if (!method_exists($pathItem, $getMethod)) {
                    continue;
                }

                $operation = $pathItem->{$getMethod}();

                if (null === $operation) {
                    continue;
                }

                $parameters = $operation->getParameters();

                if (!$this->hasParameter($parameters, 'api-token')) {
                    $parameters[] = new Parameter(...self::API_TOKEN_DEFINITION);
                }

                if (
                    $operation->getOperationId()
                    && \in_array($operation->getOperationId(), $this->userTokenRequiredUseEndpoints, true)
                    && !$this->hasParameter($parameters, 'user_token')
                ) {
                    $parameters[] = new Parameter(...self::USER_TOKEN_DEFINITION);
                }

                $operation = $operation->withParameters($parameters);
                $withMethod = 'with'.ucfirst($method);
                $newPathItem = $newPathItem->{$withMethod}($operation);
            }

            $paths->addPath($path, $newPathItem);
        }

        return $openApi;
    }

    private function hasParameter(array $parameters, string $name): bool
    {
        foreach ($parameters as $param) {
            if ($param->getName() === $name) {
                return true;
            }
        }

        return false;
    }
}
