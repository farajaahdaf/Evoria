<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OrganizerProfile;
use App\Models\User;
use App\Services\PortfolioVerificationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Display the organizer registration view.
     */
    public function createOrganizer(): View
    {
        return view('auth.register-organizer');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    /**
     * Handle organizer registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storeOrganizer(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'portfolio' => ['nullable', 'file', 'mimes:docx,pdf,jpeg,png,jpg', 'max:5120'],
        ]);

        $portfolioPath = null;
        if ($request->hasFile('portfolio')) {
            $portfolioPath = $request->file('portfolio')->store('organizers/portfolios', 'public');
        }

        $user = DB::transaction(function () use ($request, $portfolioPath) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'organizer',
            ]);

            $profile = OrganizerProfile::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name,
                'description' => $request->description,
                'portfolio_path' => $portfolioPath,
                'status' => 'pending',
            ]);

            if ($portfolioPath) {
                app(PortfolioVerificationService::class)->analyze($profile->load('user'));
            }

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect()
            ->route('organizer.pending')
            ->with('success', 'Pendaftaran Event Organizer berhasil. Akun Anda sedang menunggu verifikasi admin.');
    }
}
