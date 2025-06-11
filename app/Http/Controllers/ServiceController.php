<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Services",
 *     description="API Endpoints for Service management"
 * )
 */
class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/api/services",
     *     summary="Get all services",
     *     description="Returns a list of all services",
     *     operationId="getServicesList",
     *     tags={"Services"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Haircut"),
     *                 @OA\Property(property="description", type="string", example="Standard haircut service"),
     *                 @OA\Property(property="duration", type="integer", example=30, description="Duration in minutes"),
     *                 @OA\Property(property="price", type="number", format="float", example=50.00),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        //
        return Service::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/api/services",
     *     summary="Create a new service",
     *     description="Creates a new service and returns the created data",
     *     operationId="storeService",
     *     tags={"Services"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Service data",
     *         @OA\JsonContent(
     *             required={"name", "description", "duration", "price"},
     *             @OA\Property(property="name", type="string", example="Haircut", description="Service name"),
     *             @OA\Property(property="description", type="string", example="Standard haircut service", description="Service description"),
     *             @OA\Property(property="duration", type="integer", example=30, description="Duration in minutes"),
     *             @OA\Property(property="price", type="number", format="float", example=50.00, description="Service price")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Service created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Service created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="Haircut"),
     *                 @OA\Property(property="description", type="string", example="Standard haircut service"),
     *                 @OA\Property(property="duration", type="integer", example=30),
     *                 @OA\Property(property="price", type="number", format="float", example=50.00)
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
     *                 @OA\Property(property="description", type="array", @OA\Items(type="string", example="The description field is required.")),
     *                 @OA\Property(property="duration", type="array", @OA\Items(type="string", example="The duration must be at least 1.")),
     *                 @OA\Property(property="price", type="array", @OA\Items(type="string", example="The price field is required."))
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        //
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'price' => 'required|numeric'
        ]);

        Service::create($data);

        return response()->json([
            'message' => 'Service created successfully',
            'data' => $data
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/api/services/{id}",
     *     summary="Get a specific service",
     *     description="Returns a specific service by ID",
     *     operationId="getServiceById",
     *     tags={"Services"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of service to return",
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
     *             @OA\Property(property="name", type="string", example="Haircut"),
     *             @OA\Property(property="description", type="string", example="Standard haircut service"),
     *             @OA\Property(property="duration", type="integer", example=30),
     *             @OA\Property(property="price", type="number", format="float", example=50.00),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service not found"
     *     )
     * )
     */
    public function show(string $id)
    {
        return Service::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/services/{id}",
     *     summary="Update a service",
     *     description="Updates a service and returns the updated data",
     *     operationId="updateService",
     *     tags={"Services"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of service to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Service data",
     *         @OA\JsonContent(
     *             required={"name", "description", "duration", "price"},
     *             @OA\Property(property="name", type="string", example="Haircut", description="Service name"),
     *             @OA\Property(property="description", type="string", example="Standard haircut service", description="Service description"),
     *             @OA\Property(property="duration", type="integer", example=30, description="Duration in minutes"),
     *             @OA\Property(property="price", type="number", format="float", example=50.00, description="Service price")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Service updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="Haircut"),
     *                 @OA\Property(property="description", type="string", example="Standard haircut service"),
     *                 @OA\Property(property="duration", type="integer", example=30),
     *                 @OA\Property(property="price", type="number", format="float", example=50.00)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service not found"
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
     *                 @OA\Property(property="description", type="array", @OA\Items(type="string", example="The description field is required.")),
     *                 @OA\Property(property="duration", type="array", @OA\Items(type="string", example="The duration must be at least 1.")),
     *                 @OA\Property(property="price", type="array", @OA\Items(type="string", example="The price field is required."))
     *             )
     *         )
     *     )
     * )
     *
     * @OA\Patch(
     *     path="/api/services/{id}",
     *     summary="Update a service (partial)",
     *     description="Partially updates a service and returns the updated data. Uses the same endpoint as PUT.",
     *     operationId="patchService",
     *     tags={"Services"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of service to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Service data",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Haircut", description="Service name"),
     *             @OA\Property(property="description", type="string", example="Standard haircut service", description="Service description"),
     *             @OA\Property(property="duration", type="integer", example=30, description="Duration in minutes"),
     *             @OA\Property(property="price", type="number", format="float", example=50.00, description="Service price")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Service updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="Haircut"),
     *                 @OA\Property(property="description", type="string", example="Standard haircut service"),
     *                 @OA\Property(property="duration", type="integer", example=30),
     *                 @OA\Property(property="price", type="number", format="float", example=50.00)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service not found"
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
     *                 @OA\Property(property="description", type="array", @OA\Items(type="string", example="The description field is required.")),
     *                 @OA\Property(property="duration", type="array", @OA\Items(type="string", example="The duration must be at least 1.")),
     *                 @OA\Property(property="price", type="array", @OA\Items(type="string", example="The price field is required."))
     *             )
     *         )
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        //
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'price' => 'required|numeric'
        ]);

        $service = Service::findOrFail($id);
        $service->update($data);

        return response()->json([
            'message' => 'Service updated successfully',
            'data' => $data
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/services/{id}",
     *     summary="Delete a service",
     *     description="Deletes a service and returns a success message",
     *     operationId="deleteService",
     *     tags={"Services"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of service to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Service deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service not found"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        //
        $data = Service::findOrFail($id);
        $data->delete();

        return response()->json([
            'message' => 'Service deleted successfully'
        ], 200);
    }
}
