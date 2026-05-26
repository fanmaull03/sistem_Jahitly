<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-stone-900">
                Profil Saya
            </h2>
            <p class="mt-1 text-sm text-stone-500">
                Kelola informasi akun dan keamanan Anda.
            </p>
        </div>
    </x-slot>

    <div class="page-enter mx-auto max-w-5xl space-y-6 px-4 pb-16 sm:px-6">
        <div class="bg-white shadow-sm rounded-2xl p-6 mb-6 border border-stone-100">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="bg-white shadow-sm rounded-2xl p-6 mb-6 border border-stone-100">
            @include('profile.partials.update-password-form')
        </div>

        <div class="bg-white shadow-sm rounded-2xl p-6 mb-6 border border-stone-100">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
