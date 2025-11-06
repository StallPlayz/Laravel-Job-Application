<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $applications = Application::where('user_id', $request->user()->id)
            ->with('job')
            ->latest()
            ->get();

        return response()->json($applications);
    }

    public function store(Request $request)
    {
        $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'reason' => 'required|string',
        ]);

        // Check if user already applied for this job
        $existingApplication = Application::where('user_id', $request->user()->id)
            ->where('job_id', $request->job_id)
            ->first();

        if ($existingApplication) {
            return response()->json([
                'message' => 'You have already applied for this job',
            ], 422);
        }

        $application = Application::create([
            'user_id' => $request->user()->id,
            'job_id' => $request->job_id,
            'reason' => $request->reason,
        ]);

        $application->load('job');

        return response()->json([
            'message' => 'Application submitted successfully',
            'application' => $application,
        ], 201);
    }

    public function show(Request $request, Application $application)
    {
        // Ensure user can only view their own applications
        if ($application->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        return response()->json($application);
    }

    public function update(Request $request, Application $application)
    {
        // Ensure user can only update their own applications
        if ($application->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $request->validate([
            'reason' => 'required|string',
        ]);

        $application->update([
            'reason' => $request->reason,
        ]);

        $application->load('job');

        return response()->json([
            'message' => 'Application updated successfully',
            'application' => $application,
        ]);
    }

    public function destroy(Request $request, Application $application)
    {
        // Ensure user can only delete their own applications
        if ($application->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $application->delete();

        return response()->json([
            'message' => 'Application deleted successfully',
        ]);
    }
}
