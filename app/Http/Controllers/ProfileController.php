<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $bidangOptions = config('bidang.options');

        return view('profile.edit', [
            'user' => $request->user(),
            'bidangOptions' => $bidangOptions,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * ✅ TAMBAHAN: Mahasiswa update Bidang/Divisi sendiri
     */
    public function updateBidang(Request $request): RedirectResponse
    {
        $request->validate([
            'bidang' => ['required', 'string', 'max:255'],
        ]);

        $request->user()->update([
            'bidang' => $request->bidang,
        ]);

        return Redirect::back()->with('success', 'Bidang/Divisi berhasil disimpan.');
    }

    /**
     * ✅ TAMBAHAN: Halaman Bidang/Divisi (halaman sendiri)
     */
    public function showBidang(): View
{
    $bidangOptions = \App\Models\AboutSetting::bidangOptions();

    return view('mahasiswaa.bidang-divisi', compact('bidangOptions'));
}

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
