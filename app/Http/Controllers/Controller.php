<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="Schedule Service API",
 *     version="1.0.0",
 *     description="API for managing scheduling services, clients, and providers",
 *     @OA\Contact(
 *         email="admin@example.com",
 *         name="API Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 *
 * @OA\Tag(
 *     name="Clients",
 *     description="API Endpoints for Client management"
 * )
 *
 * @OA\Tag(
 *     name="Services",
 *     description="API Endpoints for Service management"
 * )
 *
 * @OA\Tag(
 *     name="Providers",
 *     description="API Endpoints for Provider management"
 * )
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
