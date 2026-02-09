<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Dashboard Report</title>
    <style>
      body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
      h1 { font-size: 18px; margin: 0 0 6px; }
      h2 { font-size: 14px; margin: 18px 0 6px; }
      table { width: 100%; border-collapse: collapse; }
      th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; }
      th { background: #f3f4f6; }
      .muted { color: #6b7280; }
      .kpi { margin: 6px 0; }
      .kpi span { display: inline-block; min-width: 160px; }
    </style>
  </head>
  <body>
    <h1>Dashboard Report</h1>
    <div class="muted">Period: {{ $period }} | {{ $start->toDateTimeString() }} → {{ $end->toDateTimeString() }}</div>

    <h2>KPIs</h2>
    <div class="kpi"><span>Total Reviews</span> {{ $total_reviews }}</div>
    <div class="kpi"><span>Average Rating</span> {{ $average_rating }}</div>
    <div class="kpi"><span>New Users</span> {{ $new_users }}</div>
    <div class="kpi"><span>Pending Reply</span> {{ $pending_reply }}</div>
    <div class="kpi"><span>Total Users</span> {{ $total_users }}</div>
    <div class="kpi"><span>Total Brands</span> {{ $total_brands }}</div>

    <h2>Recent Reviews</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>User</th>
          <th>Rating</th>
          <th>Comment</th>
          <th>Branch</th>
          <th>Place</th>
          <th>Created At</th>
        </tr>
      </thead>
      <tbody>
        @forelse($recent_reviews as $r)
          <tr>
            <td>{{ $r->id }}</td>
            <td>{{ $r->user?->name ?? '-' }}</td>
            <td>{{ $r->overall_rating ?? '-' }}</td>
            <td>{{ $r->comment ?? '-' }}</td>
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
            <td colspan="7">No reviews</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </body>
</html>
