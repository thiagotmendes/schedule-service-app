<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Appointments",
 *     description="API Endpoints for managing appointments"
 * )
 */
class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @OA\Get(
     *     path="/api/appointments",
     *     summary="Get all appointments",
     *     tags={"Appointments"},
     *     @OA\Response(
     *         response=200,
     *         description="List of all appointments",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="client_id", type="integer", example=1),
     *                 @OA\Property(property="provider_id", type="integer", example=1),
     *                 @OA\Property(property="service_id", type="integer", example=1),
     *                 @OA\Property(property="scheduled_at", type="string", format="date-time", example="2023-06-15T14:30:00Z"),
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
        return Appointment::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @OA\Post(
     *     path="/api/appointments",
     *     summary="Create a new appointment",
     *     tags={"Appointments"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Appointment data",
     *         @OA\JsonContent(
     *             required={"client_id", "provider_id", "service_id", "scheduled_at"},
     *             @OA\Property(property="client_id", type="integer", example=1, description="Client ID"),
     *             @OA\Property(property="provider_id", type="integer", example=1, description="Provider ID"),
     *             @OA\Property(property="service_id", type="integer", example=1, description="Service ID"),
     *             @OA\Property(property="scheduled_at", type="string", format="date-time", example="2023-06-15T14:30:00Z", description="Appointment date and time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Appointment created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Appointment created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="client_id", type="integer", example=1),
     *                 @OA\Property(property="provider_id", type="integer", example=1),
     *                 @OA\Property(property="service_id", type="integer", example=1),
     *                 @OA\Property(property="scheduled_at", type="string", format="date-time", example="2023-06-15T14:30:00Z")
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
     *                 @OA\Property(property="client_id", type="array", @OA\Items(type="string", example="The client id field is required.")),
     *                 @OA\Property(property="provider_id", type="array", @OA\Items(type="string", example="The provider id field is required.")),
     *                 @OA\Property(property="service_id", type="array", @OA\Items(type="string", example="The service id field is required.")),
     *                 @OA\Property(property="scheduled_at", type="array", @OA\Items(type="string", example="The scheduled at must be a date after now."))
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'provider_id' => 'required|exists:providers,id',
            'service_id' => 'required|exists:services,id',
            'scheduled_at' => 'required|date|after:now',
            'status' => 'nullable|string|in:pending,confirmed,cancelled,completed',
            'notes' => 'nullable|string'
        ]);

        $appointment = Appointment::create($data);

        return response()->json([
            'message' => 'Appointment created successfully',
            'data' => $appointment
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @OA\Get(
     *     path="/api/appointments/{id}",
     *     summary="Get a specific appointment by ID",
     *     tags={"Appointments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Appointment ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appointment details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="client_id", type="integer", example=1),
     *             @OA\Property(property="provider_id", type="integer", example=1),
     *             @OA\Property(property="service_id", type="integer", example=1),
     *             @OA\Property(property="scheduled_at", type="string", format="date-time", example="2023-06-15T14:30:00Z"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Appointment] 999")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        //
        return Appointment::findOrFail($id);
    }

    /**
     * @OA\Patch(
     *     path="/api/appointments/{id}",
     *     summary="Atualiza parcialmente um agendamento",
     *     description="Atualiza um ou mais campos de um agendamento existente",
     *     operationId="patchAppointment",
     *     tags={"Appointments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do agendamento",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="client_id", type="integer", example=1, description="Client ID"),
     *             @OA\Property(property="provider_id", type="integer", example=1, description="Provider ID"),
     *             @OA\Property(property="service_id", type="integer", example=1, description="Service ID"),
     *             @OA\Property(property="scheduled_at", type="string", format="date-time", example="2025-06-15T14:30:00Z", description="Appointment date and time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Atualização parcial bem-sucedida",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Appointment updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Agendamento não encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     *
     * @OA\Put(
     *     path="/api/appointments/{id}",
     *     summary="Atualiza completamente um agendamento",
     *     description="Substitui todos os campos obrigatórios de um agendamento",
     *     operationId="putAppointment",
     *     tags={"Appointments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do agendamento",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"client_id", "provider_id", "service_id", "scheduled_at"},
     *             @OA\Property(property="client_id", type="integer", example=1, description="Client ID"),
     *             @OA\Property(property="provider_id", type="integer", example=1, description="Provider ID"),
     *             @OA\Property(property="service_id", type="integer", example=1, description="Service ID"),
     *             @OA\Property(property="scheduled_at", type="string", format="date-time", example="2025-06-15T14:30:00Z", description="Appointment date and time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Agendamento atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Appointment updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Agendamento não encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $appointment = Appointment::findOrFail($id);

        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'provider_id' => 'required|exists:providers,id',
            'service_id' => 'required|exists:services,id',
            'scheduled_at' => 'required|date|after:now',
            'status' => 'nullable|string|in:pending,confirmed,cancelled,completed',
            'notes' => 'nullable|string'
        ]);

        $appointment->update($data);

        return response()->json([
            'message' => 'Appointment updated successfully',
            'data' => $appointment
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @OA\Delete(
     *     path="/api/appointments/{id}",
     *     summary="Delete an appointment",
     *     tags={"Appointments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Appointment ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appointment deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Appointment deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Appointment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Appointment] 999")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        //

        $appointment = Appointment::findOrFail($id);

        $appointment->delete();

        return response()->json([
            'message' => 'Appointment deleted successfully'
        ], 200);
    }
}
