@extends('layouts.app')

@section('content')
    @include('receptionist.partials.modals')
    @include('receptionist.partials.login')
    <div x-show="isLoggedIn" class="flex-1 flex h-[100dvh] overflow-hidden" x-cloak>
        @include('receptionist.partials.sidebar')
        <!-- CONTENT WRAPPER -->
        <div class="flex-1 flex flex-col h-[100dvh] overflow-hidden bg-slate-50">
            @include('receptionist.partials.header')
            <!-- SCROLLABLE PAGE CONTENT -->
            <main class="flex-1 overflow-y-auto p-4 md:p-8 relative print:p-0 print:m-0">
                @include('receptionist.partials.dashboard')
                @include('receptionist.partials.check')
                @include('receptionist.partials.settings')
            </main>
        </div>
    </div>
@endsection

@push('scripts')
    @include('receptionist.partials.scripts')
@endpush