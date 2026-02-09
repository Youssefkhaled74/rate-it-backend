@extends('admin.layouts.app')

@section('page_title', __('admin.notifications'))
@section('title', __('admin.send_notification'))

@section('content')
  <div class="bg-white border border-gray-100 rounded-[24px] p-6 shadow-soft">
    <h2 class="text-xl font-semibold text-gray-900">{{ __('admin.send_notification') }}</h2>
    <p class="text-sm text-gray-500 mt-1">{{ __('admin.send_notification_hint') }}</p>

    @if(session('success'))
      <div class="mt-4 text-sm text-green-700 bg-green-50 border border-green-100 rounded-xl px-4 py-3">
        {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div class="mt-4 text-sm text-red-700 bg-red-50 border border-red-100 rounded-xl px-4 py-3">
        <div class="font-semibold mb-1">{{ __('admin.fix_errors') }}</div>
        <ul class="list-disc pl-5">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('admin.notifications.send.post') }}" class="mt-6 space-y-5">
      @csrf

      <div>
        <label class="text-sm font-medium text-gray-700">{{ __('admin.audience') }}</label>
        <div class="mt-2 flex items-center gap-4">
          <label class="flex items-center gap-2 text-sm">
            <input type="radio" name="audience" value="single" {{ old('audience','single') === 'single' ? 'checked' : '' }}>
            {{ __('admin.single_user') }}
          </label>
          <label class="flex items-center gap-2 text-sm">
            <input type="radio" name="audience" value="multiple" {{ old('audience') === 'multiple' ? 'checked' : '' }}>
            {{ __('admin.multiple_users') }}
          </label>
        </div>
      </div>

      <div id="single-user-field" class="space-y-2">
        <label class="text-sm font-medium text-gray-700">{{ __('admin.user_id') }}</label>
        <input name="user_id" type="number" min="1" value="{{ old('user_id') }}" class="w-full rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
      </div>

      <div id="multiple-users-field" class="space-y-2 hidden">
        <label class="text-sm font-medium text-gray-700">{{ __('admin.user_ids') }}</label>
        <textarea name="user_ids" rows="4" class="w-full rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200" placeholder="1, 2, 3">{{ old('user_ids') }}</textarea>
        <div class="text-xs text-gray-500">{{ __('admin.user_ids_hint') }}</div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="text-sm font-medium text-gray-700">{{ __('admin.language') }}</label>
          <select name="lang" class="w-full rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
            <option value="en" {{ old('lang','en') === 'en' ? 'selected' : '' }}>EN</option>
            <option value="ar" {{ old('lang') === 'ar' ? 'selected' : '' }}>AR</option>
            <option value="auto" {{ old('lang') === 'auto' ? 'selected' : '' }}>AUTO</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm font-medium text-gray-700">{{ __('admin.title_en') }}</label>
          <input name="title_en" type="text" value="{{ old('title_en') }}" class="w-full rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
        </div>
        <div>
          <label class="text-sm font-medium text-gray-700">{{ __('admin.title_ar') }}</label>
          <input name="title_ar" type="text" value="{{ old('title_ar') }}" class="w-full rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm font-medium text-gray-700">{{ __('admin.body_en') }}</label>
          <textarea name="body_en" rows="4" class="w-full rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">{{ old('body_en') }}</textarea>
        </div>
        <div>
          <label class="text-sm font-medium text-gray-700">{{ __('admin.body_ar') }}</label>
          <textarea name="body_ar" rows="4" class="w-full rounded-2xl border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-200">{{ old('body_ar') }}</textarea>
        </div>
      </div>

      <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="use_queue" value="1" {{ old('use_queue') ? 'checked' : '' }}>
        {{ __('admin.use_queue_hint') }}
      </label>

      <div class="pt-2">
        <button type="submit" class="rounded-full bg-red-800 text-white px-6 py-3 text-sm font-semibold hover:bg-red-900 transition">
          {{ __('admin.send_notification') }}
        </button>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
  <script>
    (function(){
      const radios = document.querySelectorAll('input[name=\"audience\"]');
      const single = document.getElementById('single-user-field');
      const multiple = document.getElementById('multiple-users-field');
      function sync(){
        const val = document.querySelector('input[name=\"audience\"]:checked')?.value || 'single';
        if (val === 'multiple') {
          single.classList.add('hidden');
          multiple.classList.remove('hidden');
        } else {
          multiple.classList.add('hidden');
          single.classList.remove('hidden');
        }
      }
      radios.forEach(r => r.addEventListener('change', sync));
      sync();
    })();
  </script>
@endpush
