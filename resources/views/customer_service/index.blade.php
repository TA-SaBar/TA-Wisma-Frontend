@extends('layouts.app')

@section('content')
    @include('customer_service.partials.modals')
    @include('customer_service.partials.login')
    <div x-show="isLoggedIn" class="flex-1 flex h-[100dvh] overflow-hidden" x-cloak>
        @include('customer_service.partials.sidebar')
        <!-- CONTENT WRAPPER -->
        <div class="flex-1 flex flex-col h-[100dvh] overflow-hidden bg-slate-50">
            @include('customer_service.partials.header')
            <!-- SCROLLABLE PAGE CONTENT -->
            <main class="flex-1 overflow-y-auto p-4 md:p-8 relative print:p-0 print:m-0">
                @include('customer_service.partials.dashboard')
                @include('customer_service.partials.complaints')
                @include('customer_service.partials.reports')
                @include('customer_service.partials.settings')
            </main>
        </div>
    </div>
@endsection

@push('scripts')
    @include('customer_service.partials.scripts')
@endpush