@extends('layouts.app')

@section('content')
    @include('guest.partials.modals')
    @include('guest.partials.login')
    <div x-show="isLoggedIn" class="flex-1 flex h-[100dvh] overflow-hidden" x-cloak>
        @include('guest.partials.sidebar')
        <!-- CONTENT WRAPPER -->
        <div class="flex-1 flex flex-col h-[100dvh] overflow-hidden bg-[#F4F7FC]">
            @include('guest.partials.header')
            <!-- SCROLLABLE PAGE CONTENT -->
            <main class="flex-1 overflow-y-auto p-4 md:p-8 relative">
                @include('guest.partials.dashboard')
                @include('guest.partials.facilities')
                @include('guest.partials.booking_wizard')
                @include('guest.partials.history')
                @include('guest.partials.profile')
                @include('guest.partials.help')
            </main>
        </div>
    </div>
@endsection

@push('scripts')
    @include('guest.partials.scripts')
@endpush