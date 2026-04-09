<x-app-layout>
    <div class="min-h-screen bg-slate-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <!-- Breadcrumbs -->
            <nav class="flex mb-4 text-xs font-bold tracking-widest text-slate-400 uppercase" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition">REGISTRY</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg>
                            <a href="{{ route('admin.organizers') }}" class="ml-1 md:ml-2 hover:text-indigo-600 transition">APPLICATIONS</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center text-indigo-600">
                            <svg class="w-4 h-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"></path></svg>
                            <span class="ml-1 md:ml-2">#ORG-{{ str_pad($organizer->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Header Section -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-10 gap-6">
                <div class="max-w-2xl">
                    <h1 class="text-5xl font-extrabold text-slate-900 mb-4">
                        Application <span class="text-indigo-600">#ORG-{{ str_pad($organizer->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </h1>
                    <p class="text-slate-500 text-lg font-medium leading-relaxed">
                        {{ $organizer->description ?: 'Pending verification for Event Organizer registration. Review documents and verify eligibility.' }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-4">
                    @if($organizer->status === 'pending')
                    <div class="flex gap-4">
                        <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-rejection')" class="inline-flex items-center px-8 py-4 bg-white border border-red-200 text-red-600 rounded-xl font-bold hover:bg-red-50 transition shadow-sm transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Reject
                        </button>
                        <form action="{{ route('admin.organizers.verify', $organizer->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-8 py-4 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-100 transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                Verify & Approve
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Left: Application Information -->
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                        <div class="flex items-center justify-between mb-10">
                            <h3 class="text-xs font-bold tracking-widest text-indigo-500 uppercase">Application Information</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-10 gap-x-8 mb-12">
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Company Name</label>
                                <p class="text-xl font-bold text-slate-800">{{ $organizer->company_name }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Classification</label>
                                <p class="text-xl font-bold text-slate-800">Event Organizer</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Date Initiated</label>
                                <p class="text-xl font-bold text-slate-800">{{ $organizer->created_at->format('F d, Y') }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Submission Stage</label>
                                <div class="flex items-center">
                                    <div class="w-2 h-2 rounded-full bg-indigo-500 mr-2"></div>
                                    <p class="text-xl font-bold text-slate-800">{{ ucfirst($organizer->status) }} Review</p>
                                </div>
                            </div>
                        </div>

                        <!-- Compliance Score Dynamic -->
                        <div class="bg-slate-50 rounded-2xl p-6 flex items-center justify-between border-b-4 {{ $organizer->compliance_score >= 80 ? 'border-indigo-600' : ($organizer->compliance_score >= 50 ? 'border-yellow-500' : 'border-red-500') }}">
                            <div class="flex items-center">
                                <div class="w-12 h-12 {{ $organizer->compliance_score >= 80 ? 'bg-indigo-100' : ($organizer->compliance_score >= 50 ? 'bg-yellow-100' : 'bg-red-100') }} rounded-lg flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 {{ $organizer->compliance_score >= 80 ? 'text-indigo-600' : ($organizer->compliance_score >= 50 ? 'text-yellow-600' : 'text-red-600') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800">Evoria Compliance Score</h4>
                                    <p class="text-sm text-slate-500">Automated diagnostic {{ $organizer->compliance_accuracy }}% accuracy</p>
                                </div>
                            </div>
                            <div class="text-3xl font-black {{ $organizer->compliance_score >= 80 ? 'text-indigo-600' : ($organizer->compliance_score >= 50 ? 'text-yellow-600' : 'text-red-600') }}">{{ $organizer->compliance_score }}%</div>
                        </div>
                    </div>
                </div>

                <!-- Right: Lead Profile -->
                <div class="space-y-8">
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 flex flex-col items-center text-center">
                        <div class="flex items-center justify-between w-full mb-10">
                            <h3 class="text-xs font-bold tracking-widest text-indigo-500 uppercase">Lead Organizer</h3>
                        </div>
                        
                        <div class="relative mb-6">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($organizer->user->name) }}&background=0F172A&color=fff&size=128" alt="Avatar" class="w-32 h-32 rounded-3xl object-cover shadow-xl border-4 border-white">
                            <div class="absolute -bottom-2 -right-2 bg-indigo-600 p-2 rounded-xl text-white shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </div>

                        <h4 class="text-2xl font-bold text-slate-900 mb-1">{{ $organizer->user->name }}</h4>
                        <p class="text-slate-400 font-medium mb-8">Registered Attendee & Candidate</p>

                        <div class="w-full space-y-3 mb-8">
                            <div class="bg-slate-50 rounded-xl p-3 flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">EMAIL</span>
                                <span class="text-xs font-bold text-indigo-600">{{ $organizer->user->email }}</span>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3 flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">APPROVALS</span>
                                <span class="text-xs font-bold text-slate-700">0 Projects</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Row Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Company Entity -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                    <div class="flex items-center justify-between mb-10">
                        <h3 class="text-xs font-bold tracking-widest text-indigo-500 uppercase">Company Entity</h3>
                        <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>

                    <div class="flex items-start space-x-6 mb-8">
                        <div class="w-16 h-16 bg-slate-900 rounded-2xl flex items-center justify-center text-white text-2xl font-black shadow-lg">
                            {{ substr($organizer->company_name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="text-2xl font-bold text-slate-900">{{ $organizer->company_name }}</h4>
                            <p class="text-slate-400 font-medium">Verified Identity ID: #EVO-{{ $organizer->id }}</p>
                        </div>
                    </div>

                    <div class="bg-slate-50 rounded-2xl p-6">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Jurisdiction</label>
                        <div class="flex items-center text-slate-700 font-bold italic">
                            <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Indonesia Event Organizer Association
                        </div>
                    </div>
                </div>

                <!-- Verification Documents -->
                <div id="documents" class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                    <div class="flex items-center justify-between mb-10">
                        <h3 class="text-xs font-bold tracking-widest text-indigo-500 uppercase">Verification Documents</h3>
                        <a href="#" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">Upload Missing</a>
                    </div>

                    <div class="space-y-4">
                        <!-- Portfolio Card -->
                        <div class="flex items-center justify-between p-5 bg-slate-50 rounded-2xl border border-transparent hover:border-indigo-100 transition group">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center mr-4 group-hover:bg-indigo-100 transition">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path></svg>
                                </div>
                                <div>
                                    <h5 class="font-bold text-slate-800">Company_Portfolio.pdf</h5>
                                    @if($organizer->portfolio_path && Storage::disk('public')->exists($organizer->portfolio_path))
                                        <p class="text-xs text-slate-400 font-medium">{{ round(Storage::disk('public')->size($organizer->portfolio_path) / 1024 / 1024, 1) }} MB • {{ $organizer->updated_at->diffForHumans() }}</p>
                                    @endif
                                </div>
                            </div>
                            @if($organizer->portfolio_path && Storage::disk('public')->exists($organizer->portfolio_path))
                            <a href="{{ Storage::url($organizer->portfolio_path) }}" target="_blank" class="p-2 text-slate-400 hover:text-indigo-600 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            </a>
                            @else
                            <span class="text-xs font-bold text-red-400 italic">Missing</span>
                            @endif
                        </div>

                        <!-- Proposal Card -->
                        <div class="flex items-center justify-between p-5 bg-slate-50 rounded-2xl border border-transparent hover:border-indigo-100 transition group">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center mr-4 group-hover:bg-blue-100 transition">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div>
                                    <h5 class="font-bold text-slate-800">Registration_Proposal.pdf</h5>
                                    @if($organizer->proposal_path && Storage::disk('public')->exists($organizer->proposal_path))
                                        <p class="text-xs text-slate-400 font-medium">{{ round(Storage::disk('public')->size($organizer->proposal_path) / 1024 / 1024, 1) }} MB • {{ $organizer->updated_at->diffForHumans() }}</p>
                                    @endif
                                </div>
                            </div>
                            @if($organizer->proposal_path && Storage::disk('public')->exists($organizer->proposal_path))
                            <a href="{{ Storage::url($organizer->proposal_path) }}" target="_blank" class="p-2 text-slate-400 hover:text-indigo-600 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            </a>
                            @else
                            <span class="text-xs font-bold text-red-400 italic">Missing</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom: Process Log -->
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 mb-8">
                <div class="flex items-center justify-between mb-10">
                    <h3 class="text-xs font-bold tracking-widest text-indigo-500 uppercase">Process Log</h3>
                </div>
                <!-- ... (log content) ... -->
                <div class="space-y-10 relative">
                    <div class="absolute left-1.5 top-2 bottom-2 w-0.5 bg-slate-100"></div>
                    <div class="relative flex items-start space-x-6">
                        <div class="w-3.5 h-3.5 rounded-full bg-indigo-600 ring-4 ring-indigo-50 z-10 mt-1.5"></div>
                        <div class="flex-1">
                            <div class="flex justify-between items-center mb-1">
                                <h5 class="font-bold text-slate-800 text-lg">Application Submitted</h5>
                                <span class="text-sm font-bold text-slate-400 tabular-nums">{{ $organizer->created_at->format('H:i:s') }}</span>
                            </div>
                            <p class="text-slate-500 font-medium">Candidate "{{ $organizer->user->name }}" submitted registration for company "{{ $organizer->company_name }}".</p>
                        </div>
                    </div>
                    <div class="relative flex items-start space-x-6">
                        <div class="w-3.5 h-3.5 rounded-full bg-slate-300 z-10 mt-1.5"></div>
                        <div class="flex-1">
                            <div class="flex justify-between items-center mb-1 text-slate-400">
                                <h5 class="font-bold text-lg">Initial Review Initiated</h5>
                                <span class="text-sm font-bold tabular-nums">-- : -- : --</span>
                            </div>
                            <p class="text-slate-400 font-medium italic">Pending admin manual verification of uploaded documents.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rejection Confirmation Modal -->
            <x-modal name="confirm-rejection" focusable>
                <div class="p-8">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto mb-6 bg-red-100 rounded-2xl">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    
                    <h2 class="text-2xl font-bold text-center text-slate-900 mb-2">
                        Reject Application?
                    </h2>
                    
                    <p class="text-center text-slate-500 font-medium mb-10 leading-relaxed">
                        Are you sure you want to reject this application? This action will notify the applicant and they will remain as a regular attendee.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="button" x-on:click="$dispatch('close')" class="flex-1 px-6 py-4 bg-slate-100 text-slate-600 rounded-2xl font-bold hover:bg-slate-200 transition">
                            Cancel, Keep Reviewing
                        </button>
                        
                        <form action="{{ route('admin.organizers.reject', $organizer->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full px-6 py-4 bg-red-600 text-white rounded-2xl font-bold hover:bg-red-700 transition shadow-lg shadow-red-100">
                                Yes, Reject Application
                            </button>
                        </form>
                    </div>
                </div>
            </x-modal>
        </div>
    </div>
</x-app-layout>
