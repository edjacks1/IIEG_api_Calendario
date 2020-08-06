<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
 * @OA\SecurityScheme(
 *     type="apiKey",
 *     in="header",
 *     securityScheme="authorization",
 *     name="authorization"
 * )
 */
/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="1.0.0",
 *         title="My Api",
 *         description="Lumen Swagger Api",
 *         @OA\License(name="my api")
 *     ),
 *     @OA\Server(
 *         description="Api Documentations my api",
 *         url="/api/v1",
 *     ),
 * )
 */
    //
}
