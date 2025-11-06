<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::latest()->get();

        return response()->json($jobs);
    }

    public function show(Job $job)
    {
        return response()->json($job);
    }
}
