<?php

namespace App\Http\Controllers;

use App\Models\OrganizerProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrganizerApplicationController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->role === 'organizer') {
            $status = optional($user->organizerProfile)->status;

            if ($status === 'verified') {
                return redirect()->route('organizer.dashboard');
            }

            return redirect()->route('organizer.pending');
        }

        abort_unless($user->role === 'attendee', 403);

        return view('organizer.apply');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->role === 'attendee', 403);

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'portfolio' => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:5120'],
            'proposal' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $portfolioPath = null;
        if ($request->hasFile('portfolio')) {
            $portfolioPath = $request->file('portfolio')->store('organizers/portfolios', 'public');
        }

        $proposalPath = null;
        if ($request->hasFile('proposal')) {
            $proposalPath = $request->file('proposal')->store('organizers/proposals', 'public');
        }

        DB::transaction(function () use ($user, $validated, $portfolioPath, $proposalPath) {
            OrganizerProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => $validated['company_name'],
                    'description' => $validated['description'] ?? null,
                    'portfolio_path' => $portfolioPath,
                    'proposal_path' => $proposalPath,
                    'status' => 'pending',
                ]
            );

            $user->update(['role' => 'organizer']);
        });

        return redirect()
            ->route('organizer.pending')
            ->with('success', 'Pengajuan Event Organizer berhasil dikirim. Akun Anda sedang menunggu verifikasi admin.');
    }
}
