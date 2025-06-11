<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Service::all();
    }

    /**
     * Store a newly created resource in storage.
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
     */
    public function show(string $id)
    {
        return Service::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
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
