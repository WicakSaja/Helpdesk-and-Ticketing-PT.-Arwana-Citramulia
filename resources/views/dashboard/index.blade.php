@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Header -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-semibold">
                        Ticketing System PT Arwana
                    </h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">
                        @auth_check
                            {{ session('user.name') }}
                        @endauth_check
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="inline" id="logoutForm">
                        @csrf
                        <button type="submit" class="text-blue-600 hover:text-blue-800">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @auth_check
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">
                        Welcome, {{ session('user.name') }}!
                    </h2>

                    <!-- User Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="text-lg font-semibold">{{ session('user.email') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Phone</p>
                            <p class="text-lg font-semibold">{{ session('user.phone') }}</p>
                        </div>
                    </div>

                    <!-- Roles & Permissions -->
                    <div class="border-t pt-4">
                        <h3 class="text-lg font-semibold mb-2">Roles</h3>
                        <div class="flex gap-2 mb-4">
                            @foreach (session('roles', []) as $role)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $role }}
                                </span>
                            @endforeach
                        </div>

                        <h3 class="text-lg font-semibold mb-2">Permissions</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach (session('permissions', []) as $permission)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                    {{ $permission }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Role-Based Content -->
                    <div class="border-t pt-4 mt-4">
                        <h3 class="text-lg font-semibold mb-4">Role-Based Features</h3>

                        @role('requester')
                            <div class="bg-blue-50 p-4 rounded mb-4">
                                <h4 class="font-semibold text-blue-900">Requester Dashboard</h4>
                                <p class="text-blue-800">You can create and view your tickets</p>
                                <a href="#" class="mt-2 inline-block text-blue-600 hover:text-blue-800">
                                    View My Tickets →
                                </a>
                            </div>
                        @endrole

                        @role('technician')
                            <div class="bg-purple-50 p-4 rounded mb-4">
                                <h4 class="font-semibold text-purple-900">Technician Dashboard</h4>
                                <p class="text-purple-800">You can view and resolve assigned tickets</p>
                                <a href="#" class="mt-2 inline-block text-purple-600 hover:text-purple-800">
                                    View Assigned Tickets →
                                </a>
                            </div>
                        @endrole

                        @role('helpdesk')
                            <div class="bg-green-50 p-4 rounded mb-4">
                                <h4 class="font-semibold text-green-900">Helpdesk Dashboard</h4>
                                <p class="text-green-800">You can manage all tickets and assign them</p>
                                <a href="#" class="mt-2 inline-block text-green-600 hover:text-green-800">
                                    View All Tickets →
                                </a>
                            </div>
                        @endrole

                        @role('supervisor')
                            <div class="bg-yellow-50 p-4 rounded mb-4">
                                <h4 class="font-semibold text-yellow-900">Supervisor Dashboard</h4>
                                <p class="text-yellow-800">You can manage tickets and approve resolutions</p>
                                <a href="#" class="mt-2 inline-block text-yellow-600 hover:text-yellow-800">
                                    View Dashboard →
                                </a>
                            </div>
                        @endrole

                        @role('master-admin')
                            <div class="bg-red-50 p-4 rounded mb-4">
                                <h4 class="font-semibold text-red-900">Admin Dashboard</h4>
                                <p class="text-red-800">You have full system access</p>
                                <a href="#" class="mt-2 inline-block text-red-600 hover:text-red-800">
                                    Admin Panel →
                                </a>
                            </div>
                        @endrole
                    </div>

                    <!-- Session Data (Debug) -->
                    <div class="border-t pt-4 mt-4">
                        <details class="text-sm">
                            <summary class="cursor-pointer font-semibold text-gray-700">
                                Debug: Session Data
                            </summary>
                            <pre class="mt-2 bg-gray-100 p-3 rounded text-xs overflow-auto">{{ json_encode(session()->all(), JSON_PRETTY_PRINT) }}</pre>
                        </details>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                <p class="text-yellow-800">
                    Please <a href="{{ route('login') }}" class="font-semibold hover:underline">login</a> to continue
                </p>
            </div>
        @endauth_check
    </div>
</div>

{{-- Auth Scripts --}}
<script>
    const API_URL = "{{ env('API_BASE_URL', 'http://localhost:8000') }}";
</script>
<script src="{{ asset('js/auth-token-manager.js') }}"></script>
<script src="{{ asset('js/logout-handler.js') }}"></script>

@endsection
