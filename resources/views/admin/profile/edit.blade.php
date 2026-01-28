@extends('admin.layouts.app')

@section('title','Profile')

@section('content')
<div class="bg-white rounded-3xl shadow-soft p-6 max-w-2xl">
  <h2 class="text-lg font-semibold mb-4">Profile</h2>

  @if(session('success'))
    <div class="mb-4 text-sm text-green-700">{{ session('success') }}</div>
  @endif

  <div class="flex gap-6 items-start">
    <div class="w-40">
      <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-100">
        @if($admin->photo_url)
          <img id="preview" src="{{ $admin->photo_url }}" class="w-full h-full object-cover">
        @else
          <div id="preview" class="w-full h-full grid place-items-center text-2xl text-gray-600">{{ strtoupper(substr($admin->name,0,1)) }}</div>
        @endif
      </div>

      <form id="photoForm" method="POST" action="{{ route('admin.profile.photo.update') }}" enctype="multipart/form-data" class="mt-4">
        @csrf
        @method('PATCH')
        <input type="file" name="photo" id="photoInput" accept="image/*" class="hidden">
        <div class="flex gap-2">
          <button type="button" onclick="document.getElementById('photoInput').click()" class="rounded-xl bg-red-800 text-white px-4 py-2">Choose Photo</button>
          <button type="submit" class="rounded-xl bg-gray-200 px-4 py-2">Upload</button>
        </div>
      </form>

      <form method="POST" action="{{ route('admin.profile.photo.remove') }}" class="mt-2">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-sm text-red-600" onclick="return confirm('Remove photo?')">Remove photo</button>
      </form>
    </div>

    <div class="flex-1">
      <div class="text-sm text-gray-600">Name</div>
      <div class="font-medium mb-3">{{ $admin->name }}</div>
      <div class="text-sm text-gray-600">Email</div>
      <div class="font-medium mb-3">{{ $admin->email }}</div>
      <div class="text-sm text-gray-600">Phone</div>
      <div class="font-medium mb-3">{{ $admin->phone }}</div>
    </div>
  </div>
</div>

<script>
  const input = document.getElementById('photoInput');
  const preview = document.getElementById('preview');
  input?.addEventListener('change', (e) => {
    const f = e.target.files[0];
    if (!f) return;
    const url = URL.createObjectURL(f);
    if (preview.tagName === 'IMG') preview.src = url;
    else preview.style.backgroundImage = `url(${url})`;
  });
</script>

@endsection
