<?php

namespace App\Http\Controllers\User;

use App\Helpers\Transformers\LeaveTransformer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HRM\LeaveType;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::all();

        $leaveTypes->transform(fn($leaveType) => LeaveTransformer::leaveType($leaveType));

        return response()->json([
            'data' => $leaveTypes,
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $leaveType = LeaveType::create($request->all());

        return response()->json([
            'message' => 'Leave type created successfully',
            'data' => LeaveTransformer::leaveType($leaveType),
        ], 201);
    }

    public function show($id)
    {
        $leaveType = LeaveType::findOrFail($id);
        return response()->json([
            'data' => LeaveTransformer::leaveType($leaveType),
        ], 200);
    }

    public function update(LeaveType $leaveType, Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $leaveType->update($request->all());
        
        return response()->json([
            'message' => 'Leave type updated successfully',
            'data' => LeaveTransformer::leaveType($leaveType),
        ], 200);
    }
}
