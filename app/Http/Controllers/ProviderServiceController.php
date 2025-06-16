<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Provider Services",
 *     description="API Endpoints for managing provider services"
 * )
 */
class ProviderServiceController extends Controller
{
    /**
     * Attach services to a provider
     *
     * @OA\Post(
     *     path="/api/providers/{providerId}/services",
     *     summary="Attach services to a provider",
     *     tags={"Provider Services"},
     *     @OA\Parameter(
     *         name="providerId",
     *         in="path",
     *         required=true,
     *         description="Provider ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Service IDs to attach",
     *         @OA\JsonContent(
     *             required={"service_ids"},
     *             @OA\Property(
     *                 property="service_ids",
     *                 type="array",
     *                 description="Array of service IDs to attach to the provider",
     *                 @OA\Items(type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Services attached successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Serviços vinculados com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Provider not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Provider] 999")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="service_ids", type="array", @OA\Items(type="string", example="The service ids field is required.")),
     *                 @OA\Property(property="service_ids.0", type="array", @OA\Items(type="string", example="The selected service_ids.0 is invalid."))
     *             )
     *         )
     *     )
     * )
     */
    public function attachService(Request $request, $providerId)
    {
        $provider = Provider::findOrFail($providerId);

        $validated = $request->validate([
            'service_ids' => 'required|array',
            'service_ids.*' => 'exists:services,id',
        ]);

        $provider->services()->syncWithoutDetaching($validated['service_ids']);

        return response()->json([
            'message' => 'Serviços vinculados com sucesso'
        ]);
    }

}
