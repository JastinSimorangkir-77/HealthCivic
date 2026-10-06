<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'email' => mb_strtolower(trim($request->input('email', ''))),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:25', 'regex:/^[0-9+(). -]{8,25}$/'],
            'password' => ['required', 'confirmed', Password::min(12)->letters()->numbers()],
            'privacy_consent' => ['accepted'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama maksimal 120 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'email.unique' => 'Email ini sudah digunakan. Silakan masuk dengan akun Anda.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.regex' => 'Masukkan nomor telepon yang valid.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'password.min' => 'Kata sandi minimal 12 karakter.',
            'password.letters' => 'Kata sandi harus memuat huruf.',
            'password.numbers' => 'Kata sandi harus memuat angka.',
            'privacy_consent.accepted' => 'Persetujuan pemrosesan data wajib disetujui.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'privacy_consent_at' => now(),
            'password' => $validated['password'],
        ]);

        event(new Registered($user));
        Auth::login($user);
        $request->session()->regenerate();

        return to_route('home');
    }

    public function show(Request $request): View
    {
        return view('portal.account', [
            'user' => $request->user(),
        ]);
    }
}
