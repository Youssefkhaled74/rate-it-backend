<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Reviews Export</title>
    <style>
      body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
      h1 { font-size: 18px; margin: 0 0 6px; }
      .muted { color: #6b7280; }
      table { width: 100%; border-collapse: collapse; margin-top: 12px; }
      th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; }
      th { background: #f3f4f6; }
    </style>
  </head>
  <body>
    <h1>Reviews Export</h1>
    <div class="muted">Range: {{ $rangeLabel }}</div>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>User</th>
          <th>Rating</th>
          <th>Comment</th>
          <th>Hidden</th>
          <th>Featured</th>
          <th>Replied</th>
          <th>Branch</th>
          <th>Place</th>
          <th>Created At</th>
        </tr>
      </thead>
      <tbody>
        @forelse($reviews as $r)
          <tr>
            <td>{{ $r->id }}</td>
            <td>{{ $r->user?->name ?? '-' }}</td>
            <td>{{ $r->overall_rating ?? '-' }}</td>
            <td>{{ $r->comment ?? '-' }}</td>
            <td>{{ $r->is_hidden ? 'yes' : 'no' }}</td>
            <td>{{ $r->is_featured ? 'yes' : 'no' }}</td>
            <td>{{ $r->replied_at ? 'yes' : 'no' }}</td>
            <td>{{ $r->branch?->name ?? '-' }}</td>
            <td>
              {{ $r->place?->display_name
                ?? $r->place?->name
                ?? $r->place?->name_en
                ?? $r->place?->title_en
                ?? $r->place?->name_ar
                ?? $r->place?->title_ar
                ?? '-' }}
            </td>
            <td>{{ $r->created_at?->toDateTimeString() ?? '-' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="10">No reviews</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </body>
</html>
