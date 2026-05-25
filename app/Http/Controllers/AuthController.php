<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\User;

#[\Illuminate\Routing\Controllers\Middleware('web')]
class AuthController extends Controller implements HasMiddleware
{
    /**
     * Daftarkan middleware.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('web'),
        ];
    }

    /**
     * Menampilkan view halaman login.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Menampilkan view halaman register.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Memproses registrasi pengguna baru.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    /**
     * Memproses otentikasi login.
     * 
     * Penjelasan Keamanan Session Laravel 13:
     * Saat Auth::attempt() berhasil, Laravel secara otomatis meregenerasi ID Session
     * ($request->session()->regenerate()) untuk mencegah serangan Session Fixation.
     * Ini menjamin bahwa sesi user yang login benar-benar baru dan aman (clean state).
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Mencegah Session Fixation attack
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    /**
     * Logout dan hapus session.
     * 
     * Penjelasan Keamanan Session Laravel 13:
     * Saat logout, kita melakukan invalidasi sesi (invalidate) dan men-generate ulang token CSRF
     * (regenerateToken). Hal ini mencegah token lama disalahgunakan oleh pihak ketiga
     * atau mencegah serangan Cross-Site Request Forgery (CSRF).
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
 
        $request->session()->invalidate();
        $request->session()->regenerateToken();
 
        return redirect('/login');
    }

    /**
     * Memperbarui profil pengguna.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        if ($request->hasFile('profile_photo')) {
            // Hapus foto lama jika ada
            if ($user->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo);
            }
            
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $data['profile_photo'] = $path;
        }

        $user->update($data);

        return back()->with('success', 'Profile updated successfully!');
    }
}
