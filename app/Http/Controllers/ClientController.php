<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Clients",
 *     description="API Endpoints for Client management"
 * )
 */
class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/api/clients",
     *     summary="Get all clients",
     *     description="Returns a list of all clients",
     *     operationId="getClientsList",
     *     tags={"Clients"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Jane Smith"),
     *                 @OA\Property(property="email", type="string", format="email", example="jane@example.com"),
     *                 @OA\Property(property="phone", type="string", example="123-456-7890"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Client::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/api/clients",
     *     summary="Create a new client",
     *     description="Creates a new client and returns the created data",
     *     operationId="storeClient",
     *     tags={"Clients"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Client data",
     *         @OA\JsonContent(
     *             required={"name", "email", "phone"},
     *             @OA\Property(property="name", type="string", example="Jane Smith", description="Client name"),
     *             @OA\Property(property="email", type="string", format="email", example="jane@example.com", description="Client email"),
     *             @OA\Property(property="phone", type="string", example="123-456-7890", description="Client phone number")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Client created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Client created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="Jane Smith"),
     *                 @OA\Property(property="email", type="string", format="email", example="jane@example.com"),
     *                 @OA\Property(property="phone", type="string", example="123-456-7890")
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
     *                 @OA\Property(property="phone", type="array", @OA\Items(type="string", example="The phone field is required."))
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients',
            'phone' => 'required|string|max:255'
        ]);

        Client::create($data);

        return response()->json([
            'message' => 'Client created successfully',
            'data' => $data
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/api/clients/{id}",
     *     summary="Get a specific client",
     *     description="Returns a specific client by ID",
     *     operationId="getClientById",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of client to return",
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
     *             @OA\Property(property="name", type="string", example="Jane Smith"),
     *             @OA\Property(property="email", type="string", format="email", example="jane@example.com"),
     *             @OA\Property(property="phone", type="string", example="123-456-7890"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found"
     *     )
     * )
     */
    public function show(string $id)
    {
       return Client::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/clients/{id}",
     *     summary="Update a client",
     *     description="Updates a client and returns the updated data",
     *     operationId="updateClient",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of client to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Client data",
     *         @OA\JsonContent(
     *             required={"name", "email", "phone"},
     *             @OA\Property(property="name", type="string", example="Jane Smith", description="Client name"),
     *             @OA\Property(property="email", type="string", format="email", example="jane@example.com", description="Client email"),
     *             @OA\Property(property="phone", type="string", example="123-456-7890", description="Client phone number")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Client updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Jane Smith"),
     *                 @OA\Property(property="email", type="string", format="email", example="jane@example.com"),
     *                 @OA\Property(property="phone", type="string", example="123-456-7890"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found"
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
     *                 @OA\Property(property="phone", type="array", @OA\Items(type="string", example="The phone field is required."))
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Patch(
     *     path="/api/clients/{id}",
     *     summary="Update a client (partial)",
     *     description="Partially updates a client and returns the updated data. Uses the same endpoint as PUT.",
     *     operationId="patchClient",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of client to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Client data",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Jane Smith", description="Client name"),
     *             @OA\Property(property="email", type="string", format="email", example="jane@example.com", description="Client email"),
     *             @OA\Property(property="phone", type="string", example="123-456-7890", description="Client phone number")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Client updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Jane Smith"),
     *                 @OA\Property(property="email", type="string", format="email", example="jane@example.com"),
     *                 @OA\Property(property="phone", type="string", example="123-456-7890"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found"
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
     *                 @OA\Property(property="phone", type="array", @OA\Items(type="string", example="The phone field is required."))
     *             )
     *         )
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $client = Client::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients,email,' . $client->id,
            'phone' => 'required|string|max:255',
        ]);

        $client->update($data);

        return response()->json([
            'message' => 'Client updated successfully',
            'data' => $client
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/clients/{id}",
     *     summary="Delete a client",
     *     description="Deletes a client and returns a success message",
     *     operationId="deleteClient",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of client to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Client deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return response()->json([
            'message' => 'Client deleted successfully'
        ], 200);
    }
}
