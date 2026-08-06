@extends('layouts.app')

@section('content')
    @include('admin.partials.modals')
    @include('admin.partials.login')
    <div x-show="isLoggedIn" class="flex-1 flex h-[100dvh] overflow-hidden" x-cloak>
        @include('admin.partials.sidebar')
        <!-- CONTENT WRAPPER -->
        <div class="flex-1 flex flex-col h-[100dvh] overflow-hidden bg-slate-50">
            @include('admin.partials.header')
            <!-- SCROLLABLE PAGE CONTENT -->
            <main class="flex-1 overflow-y-auto p-4 md:p-8 relative print:p-0 print:m-0">
                @include('admin.partials.dashboard')
                @include('admin.partials.facilities')
                @include('admin.partials.guests')
                @include('admin.partials.reports')
                @include('admin.partials.settings')
            </main>
        </div>
    </div>
@endsection

@push('scripts')
    @include('admin.partials.scripts')
@endpush
