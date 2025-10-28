<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- ✅ Profile Information Form --}}
            <div class="p-6 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-semibold mb-4">Profile Information</h3>
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>Save Changes</x-primary-button>
                    </div>
                </form>
            </div>

            {{-- ✅ Password Update Form --}}
            <div class="p-6 bg-white shadow sm:rounded-lg">
                <h3 class="text-lg font-semibold mb-4">Change Password</h3>
                @include('profile.partials.update-password-form')
            </div>

            {{-- ✅ Account Delete Form --}}
            <div class="p-6 bg-white shadow sm:rounded-lg">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
