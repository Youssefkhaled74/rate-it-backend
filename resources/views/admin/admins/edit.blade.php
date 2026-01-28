@extends('admin.layouts.app')

@section('title','Edit Admin')

@section('content')
<div class="max-w-4xl">

  <div class="bg-white rounded-3xl shadow-soft border border-gray-100 overflow-hidden">

    {{-- Top Header --}}
    <div class="px-6 md:px-8 py-6 bg-gradient-to-br from-white to-gray-50 border-b border-gray-100">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div class="flex items-start gap-4">
          {{-- Avatar --}}
          <div class="w-14 h-14 rounded-2xl bg-red-800 text-white grid place-items-center font-bold text-xl shadow-lg shadow-red-800/20">
            {{ strtoupper(mb_substr($admin->name ?? 'A', 0, 1)) }}
          </div>

          <div>
            <div class="flex items-center gap-2 flex-wrap">
              <h2 class="text-xl font-semibold">Edit Admin</h2>

              {{-- Role badge --}}
              <span class="inline-flex items-center px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold">
                {{ $admin->role ?? 'admin' }}
              </span>

              {{-- Status badge --}}
              @if($admin->is_active)
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs font-semibold">
                  <span class="w-2 h-2 rounded-full bg-green-600"></span> Active
                </span>
              @else
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-semibold">
                  <span class="w-2 h-2 rounded-full bg-gray-500"></span> Inactive
                </span>
              @endif
            </div>

            <p class="text-sm text-gray-500 mt-1">
              Update admin details, role, and access status.
            </p>

            <div class="mt-3 text-xs text-gray-500 space-y-1">
              <div><span class="font-semibold text-gray-700">Email:</span> {{ $admin->email }}</div>
              <div><span class="font-semibold text-gray-700">Phone:</span> {{ $admin->phone ?? '-' }}</div>
            </div>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <a href="{{ route('admin.admins.index') }}"
             class="rounded-2xl bg-white border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
            Back
          </a>
        </div>

      </div>
    </div>

    {{-- Errors --}}
    @if ($errors->any())
      <div class="mx-6 md:mx-8 mt-6 rounded-2xl bg-red-50 border border-red-100 text-red-700 text-sm px-4 py-3">
        <div class="font-semibold mb-1">Please fix the following:</div>
        <ul class="list-disc pl-5 space-y-1">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('admin.admins.update', $admin) }}" class="px-6 md:px-8 py-6">
      @csrf
      @method('PUT')

      {{-- Section: Account --}}
      <div class="mb-7">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-sm font-semibold text-gray-900">Account</h3>
            <p class="text-xs text-gray-500 mt-1">Basic information used for admin profile.</p>
          </div>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label class="text-sm font-medium text-gray-700">Full name</label>
            <input name="name" required
              value="{{ old('name', $admin->name) }}"
              class="mt-2 w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm outline-none transition
                     focus:border-red-300 focus:ring-4 focus:ring-red-100">
          </div>

          <div>
            <label class="text-sm font-medium text-gray-700">Email address</label>
            <input name="email" type="email" required
              value="{{ old('email', $admin->email) }}"
              class="mt-2 w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm outline-none transition
                     focus:border-red-300 focus:ring-4 focus:ring-red-100">
          </div>

          <div>
            <label class="text-sm font-medium text-gray-700">Phone (optional)</label>
            <input name="phone"
              value="{{ old('phone', $admin->phone) }}"
              class="mt-2 w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm outline-none transition
                     focus:border-red-300 focus:ring-4 focus:ring-red-100">
          </div>

          <div>
            <label class="text-sm font-medium text-gray-700">Role</label>
            <input name="role"
              value="{{ old('role', $admin->role) }}"
              class="mt-2 w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm outline-none transition
                     focus:border-red-300 focus:ring-4 focus:ring-red-100">
            <p class="mt-2 text-xs text-gray-500">Use a role that matches your authorization rules/policies.</p>
          </div>
        </div>
      </div>

      {{-- Divider --}}
      <div class="h-px bg-gray-100 my-7"></div>

      {{-- Section: Security --}}
      <div class="mb-7">
        <h3 class="text-sm font-semibold text-gray-900">Security</h3>
        <p class="text-xs text-gray-500 mt-1">Update password only if needed.</p>

        <div class="mt-4">
          <label class="text-sm font-medium text-gray-700">New password</label>
          <div class="mt-2 relative">
            <input id="admin_password" name="password" type="password"
              placeholder="Leave blank to keep current password"
              class="w-full rounded-2xl border border-gray-200 bg-gray-50/50 px-4 py-3 pr-12 text-sm outline-none transition
                     focus:border-red-300 focus:ring-4 focus:ring-red-100">

            <button type="button" id="toggle_password"
              class="absolute inset-y-0 right-2 px-3 rounded-xl text-gray-500 hover:text-gray-900 hover:bg-gray-100/70 transition"
              aria-label="Toggle password visibility">
              <svg id="eye_open" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
              <svg id="eye_closed" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M3 3l18 18"></path>
                <path d="M10.6 10.6A3 3 0 0 0 12 15a3 3 0 0 0 2.12-.88"></path>
                <path d="M9.9 5.2A10.5 10.5 0 0 1 12 5c6.5 0 10 7 10 7a18 18 0 0 1-3.2 4.4"></path>
                <path d="M6.2 6.2C3.3 8.3 2 12 2 12s3.5 7 10 7c1.3 0 2.5-.2 3.6-.6"></path>
              </svg>
            </button>
          </div>

          <div class="mt-3 rounded-2xl bg-gray-50 border border-gray-100 px-4 py-3">
            <div class="text-sm font-semibold text-gray-800">Tip</div>
            <div class="text-xs text-gray-500 mt-1">
              Use at least 8 characters. Leave empty to keep the current password.
            </div>
          </div>
        </div>
      </div>

      {{-- Divider --}}
      <div class="h-px bg-gray-100 my-7"></div>

      {{-- Section: Access --}}
      <div class="mb-2">
        <h3 class="text-sm font-semibold text-gray-900">Access</h3>
        <p class="text-xs text-gray-500 mt-1">Control whether this admin can access the dashboard.</p>

        <label class="mt-4 inline-flex items-center gap-3 cursor-pointer select-none">
          <input type="checkbox" name="is_active" value="1" {{ old('is_active', $admin->is_active) ? 'checked' : '' }}
                 class="sr-only peer">
          <span class="w-12 h-6 rounded-full bg-gray-200 peer-checked:bg-green-500 transition relative">
            <span class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-white transition peer-checked:translate-x-6"></span>
          </span>
          <span class="text-sm text-gray-700">
            <span class="font-semibold">{{ $admin->is_active ? 'Active' : 'Inactive' }}</span>
            <span class="text-gray-500">— allow this admin to access dashboard.</span>
          </span>
        </label>
      </div>

      {{-- Sticky Actions --}}
      <div class="mt-8 -mx-6 md:-mx-8 px-6 md:px-8 py-4 border-t border-gray-100 bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60 sticky bottom-0">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
          <button
            class="rounded-2xl bg-red-800 text-white px-6 py-3 text-sm font-semibold
                   hover:bg-red-900 transition shadow-lg shadow-red-800/20">
            Save Changes
          </button>

          <a href="{{ route('admin.admins.index') }}"
             class="rounded-2xl bg-white border border-gray-200 text-gray-800 px-6 py-3 text-sm font-semibold
                    hover:bg-gray-50 transition text-center">
            Cancel
          </a>

          <div class="sm:ml-auto text-xs text-gray-400 flex items-center gap-2">
            <span class="inline-block w-2 h-2 rounded-full bg-gray-300"></span>
            Changes are saved immediately
          </div>
        </div>
      </div>
    </form>

  </div>

  {{-- Inline JS show/hide password --}}
  <script>
    (function () {
      const input = document.getElementById('admin_password');
      const btn = document.getElementById('toggle_password');
      const eyeOpen = document.getElementById('eye_open');
      const eyeClosed = document.getElementById('eye_closed');
      if (!input || !btn) return;

      btn.addEventListener('click', function () {
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        eyeOpen.classList.toggle('hidden', isHidden);
        eyeClosed.classList.toggle('hidden', !isHidden);
      });
    })();
  </script>

</div>
@endsection
