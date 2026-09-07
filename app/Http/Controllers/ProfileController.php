<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $mode = (string) $request->query('mode', 'view');
        $readOnly = $mode !== 'edit';
        return view('profile.edit', [
            'user' => $request->user(),
            'readOnly' => $readOnly,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $user = $request->user();
        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ]);
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Cập nhật thông tin cá nhân thành công.');
    }
}

