<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Providers",
 *     description="API Endpoints for Provider management"
 * )
 */
class ProviderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/providers",
     *     summary="Get all providers",
     *     description="Returns a list of all providers",
     *     operationId="getProvidersList",
     *     tags={"Providers"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                 @OA\Property(property="phone", type="string", example="123-456-7890"),
     *                 @OA\Property(property="document", type="string", example="12345678901"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Provider::all();
    }

    /**
     * @OA\Post(
     *     path="/api/providers",
     *     summary="Create a new provider",
     *     description="Creates a new provider and returns the created data",
     *     operationId="storeProvider",
     *     tags={"Providers"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Provider data",
     *         @OA\JsonContent(
     *             required={"name", "email", "phone", "document"},
     *             @OA\Property(property="name", type="string", example="John Doe", description="Provider name"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com", description="Provider email"),
     *             @OA\Property(property="phone", type="string", example="123-456-7890", description="Provider phone number"),
     *             @OA\Property(property="document", type="string", example="12345678901", description="Provider document (11 characters)"),
     *             @OA\Property(property="specialization", type="string", example="Specialization", description="Provider specialization"),
     *             @OA\Property(property="bio", type="string", example="Bio description", description="Provider bio")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Provider created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Provider created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                 @OA\Property(property="phone", type="string", example="123-456-7890"),
     *                 @OA\Property(property="document", type="string", example="12345678901"),
     *                 @OA\Property(property="specialization", type="string", example="Specialization"),
     *                 @OA\Property(property="bio", type="string", example="Bio description")
     *             )
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
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required.")),
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email must be a valid email address.")),
     *                 @OA\Property(property="phone", type="array", @OA\Items(type="string", example="The phone field is required.")),
     *                 @OA\Property(property="document", type="array", @OA\Items(type="string", example="The document must be 11 characters.")),
     *                 @OA\Property(property="specialization", type="array", @OA\Items(type="string", example="The specialization field is required.")),
     *                 @OA\Property(property="bio", type="array", @OA\Items(type="string", example="The bio field is required."))
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:providers',
            'phone' => 'required|string|max:255',
            'document' => 'required|string|size:11|unique:providers',
            'specialization' => 'nullable|string|max:255',
            'bio' => 'nullable|string'
        ]);

        $data['user_id'] = $request->user()->id;

        $provider = Provider::create($data);

        return response()->json([
            'message' => 'Provider created successfully',
            'data' => $provider
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/providers/{id}",
     *     summary="Get a specific provider",
     *     description="Returns a specific provider by ID",
     *     operationId="getProviderById",
     *     tags={"Providers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of provider to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="phone", type="string", example="123-456-7890"),
     *             @OA\Property(property="document", type="string", example="12345678901"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Provider not found"
     *     )
     * )
     */
    public function show(string $id)
    {
        //
        return Provider::findOrFail($id);
    }

    /**
     * @OA\Put(
     *     path="/api/providers/{id}",
     *     summary="Update a provider",
     *     description="Updates a provider and returns the updated data",
     *     operationId="updateProvider",
     *     tags={"Providers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of provider to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Provider data",
     *         @OA\JsonContent(
     *             required={"name", "email", "phone", "document"},
     *             @OA\Property(property="name", type="string", example="John Doe", description="Provider name"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com", description="Provider email"),
     *             @OA\Property(property="phone", type="string", example="123-456-7890", description="Provider phone number"),
     *             @OA\Property(property="document", type="string", example="12345678901", description="Provider document (11 characters)"),
     *             @OA\Property(property="specialization", type="string", example="Specialization", description="Provider specialization"),
     *             @OA\Property(property="bio", type="string", example="Bio description", description="Provider bio")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Provider updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Provider updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                 @OA\Property(property="phone", type="string", example="123-456-7890"),
     *                 @OA\Property(property="document", type="string", example="12345678901"),
     *                 @OA\Property(property="specialization", type="string", example="Specialization"),
     *                 @OA\Property(property="bio", type="string", example="Bio description"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Provider not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required.")),
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email must be a valid email address.")),
     *                 @OA\Property(property="phone", type="array", @OA\Items(type="string", example="The phone field is required.")),
     *                 @OA\Property(property="document", type="array", @OA\Items(type="string", example="The document must be 11 characters.")),
     *                 @OA\Property(property="specialization", type="array", @OA\Items(type="string", example="The specialization field is required.")),
     *                 @OA\Property(property="bio", type="array", @OA\Items(type="string", example="The bio field is required."))
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Patch(
     *     path="/api/providers/{id}",
     *     summary="Update a provider (partial)",
     *     description="Partially updates a provider and returns the updated data. Uses the same endpoint as PUT.",
     *     operationId="patchProvider",
     *     tags={"Providers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of provider to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Provider data",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe", description="Provider name"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com", description="Provider email"),
     *             @OA\Property(property="phone", type="string", example="123-456-7890", description="Provider phone number"),
     *             @OA\Property(property="document", type="string", example="12345678901", description="Provider document (11 characters)"),
     *             @OA\Property(property="specialization", type="string", example="Specialization", description="Provider specialization"),
     *             @OA\Property(property="bio", type="string", example="Bio description", description="Provider bio")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Provider updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Provider updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                 @OA\Property(property="phone", type="string", example="123-456-7890"),
     *                 @OA\Property(property="document", type="string", example="12345678901"),
     *                 @OA\Property(property="specialization", type="string", example="Specialization"),
     *                 @OA\Property(property="bio", type="string", example="Bio description"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Provider not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required.")),
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email must be a valid email address.")),
     *                 @OA\Property(property="phone", type="array", @OA\Items(type="string", example="The phone field is required.")),
     *                 @OA\Property(property="document", type="array", @OA\Items(type="string", example="The document must be 11 characters.")),
     *                 @OA\Property(property="specialization", type="array", @OA\Items(type="string", example="The specialization field is required.")),
     *                 @OA\Property(property="bio", type="array", @OA\Items(type="string", example="The bio field is required."))
     *             )
     *         )
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $provider = Provider::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:providers,email,' . $provider->id,
            'phone'    => 'required|string|max:255',
            'document' => 'required|string|size:11|unique:providers,document,' . $provider->id,
            'specialization' => 'nullable|string|max:255',
            'bio' => 'nullable|string'
        ]);

        $provider->update($validated);

        return response()->json([
            'message' => 'Provider updated successfully',
            'data' => $provider
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/providers/{id}",
     *     summary="Delete a provider",
     *     description="Deletes a provider and returns a success message",
     *     operationId="deleteProvider",
     *     tags={"Providers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of provider to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Provider deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Provider deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Provider not found"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $provider = Provider::findOrFail($id);

        $provider->delete();

        return response()->json([
            'message' => 'Provider deleted successfully'
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/provider/profile",
     *     summary="Obter perfil do prestador autenticado",
     *     tags={"Provider"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Perfil do provider retornado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Provider profile data"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="phone", type="string", example="(11) 99999-8888"),
     *                 @OA\Property(property="document", type="string", example="12345678900")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Perfil do provider não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Provider profile not found")
     *         )
     *     )
     * )
     */
    public function profile()
    {
        $provider = auth()->user()->provider;

        if (!$provider) {
            return response()->json(['message' => 'Provider profile not found'], 404);
        }

        return response()->json([
            'message' => 'Provider profile data',
            'data' => $provider
        ]);
    }
}
