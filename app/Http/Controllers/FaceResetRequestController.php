<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FaceResetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaceResetRequestController extends Controller
{
    public function create()
    {
        $user = Auth::user();

        // Check for existing requests
        $existingRequest = FaceResetRequest::where('user_id', $user->id)
            ->latest()
            ->first();

        return view('face_reset_requests.create', compact('existingRequest'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();

        // Check if user already has pending request
        $existingPending = FaceResetRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingPending) {
            return redirect()->back()->with('warning', 'Anda sudah memiliki permintaan reset yang belum diproses. Tunggu admin untuk mereviunya.');
        }

        FaceResetRequest::create([
            'user_id' => $request->user_id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('attendance.index')->with('success', 'Permintaan reset wajah telah dikirim. Admin akan meninjau dalam waktu singkat.');
    }
}
