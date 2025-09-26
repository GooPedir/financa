<?php

namespace App\OpenApi;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Financas API",
 *     description="API multi-tenant de finanças pessoais e contas conjuntas",
 * )
 * @OA\Server(name="Local", url="/api")
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Token"
 * )
 */
class Swagger {}

