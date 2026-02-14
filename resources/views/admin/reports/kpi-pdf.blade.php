<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>KPI Report</title>
    <style>
      body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
      h1 { font-size: 18px; margin: 0 0 6px; }
      h2 { font-size: 14px; margin: 16px 0 8px; }
      table { width: 100%; border-collapse: collapse; margin-top: 8px; }
      th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; }
      th { background: #f3f4f6; }
      .muted { color: #6b7280; }
      .grid { display: table; width: 100%; border-spacing: 8px; }
      .card { display: table-cell; border: 1px solid #e5e7eb; padding: 8px; }
      .k { font-size: 11px; color: #6b7280; }
      .v { font-size: 16px; font-weight: 700; margin-top: 4px; }
    </style>
  </head>
  <body>
    <h1>KPI Report</h1>
    <div class="muted">From: {{ $fromDate->toDateString() }} | To: {{ $toDate->toDateString() }}</div>

    <h2>Overview</h2>
    <div class="grid">
      <div class="card"><div class="k">Total Reviews</div><div class="v">{{ $overview['total_reviews'] ?? 0 }}</div></div>
      <div class="card"><div class="k">Avg Overall Rating</div><div class="v">{{ number_format((float) ($overview['avg_overall_rating'] ?? 0), 2) }}</div></div>
      <div class="card"><div class="k">Avg Review Score</div><div class="v">{{ number_format((float) ($overview['avg_review_score'] ?? 0), 2) }}</div></div>
      <div class="card"><div class="k">Reply Rate %</div><div class="v">{{ number_format((float) ($overview['reply_rate_percent'] ?? 0), 1) }}%</div></div>
    </div>

    <h2>Subscriptions</h2>
    <table>
      <thead><tr><th>KPI</th><th>Value</th></tr></thead>
      <tbody>
        <tr><td>Total</td><td>{{ $subscriptions['total'] ?? 0 }}</td></tr>
        <tr><td>New In Range</td><td>{{ $subscriptions['new_in_range'] ?? 0 }}</td></tr>
        <tr><td>Active</td><td>{{ $subscriptions['active'] ?? 0 }}</td></tr>
        <tr><td>Free</td><td>{{ $subscriptions['free'] ?? 0 }}</td></tr>
        <tr><td>Expired</td><td>{{ $subscriptions['expired'] ?? 0 }}</td></tr>
        <tr><td>Revenue</td><td>{{ number_format(($subscriptions['revenue_cents'] ?? 0) / 100, 2) }}</td></tr>
      </tbody>
    </table>

    <h2>Points & QR</h2>
    <table>
      <thead><tr><th>KPI</th><th>Value</th></tr></thead>
      <tbody>
        <tr><td>Points Issued</td><td>{{ $points['issued'] ?? 0 }}</td></tr>
        <tr><td>Points Redeemed</td><td>{{ $points['redeemed'] ?? 0 }}</td></tr>
        <tr><td>Points Net</td><td>{{ $points['net'] ?? 0 }}</td></tr>
        <tr><td>Total QR Scans</td><td>{{ $qr['total_scans'] ?? 0 }}</td></tr>
        <tr><td>Unique Scan Users</td><td>{{ $qr['unique_users'] ?? 0 }}</td></tr>
        <tr><td>Unique Scan Branches</td><td>{{ $qr['unique_branches'] ?? 0 }}</td></tr>
      </tbody>
    </table>

    <h2>Top Branches</h2>
    <table>
      <thead><tr><th>Branch</th><th>Avg Rating</th><th>Reviews</th></tr></thead>
      <tbody>
        @forelse($topBranches as $row)
          @php $branch = $branchesById[$row->branch_id] ?? null; @endphp
          <tr>
            <td>{{ $branch?->name_en ?? $branch?->name_ar ?? $branch?->name ?? ('#'.$row->branch_id) }}</td>
            <td>{{ number_format((float) $row->avg_rating, 2) }}</td>
            <td>{{ $row->reviews_count }}</td>
          </tr>
        @empty
          <tr><td colspan="3">No data</td></tr>
        @endforelse
      </tbody>
    </table>

    <h2>Top Brands</h2>
    <table>
      <thead><tr><th>Brand</th><th>Avg Rating</th><th>Reviews</th></tr></thead>
      <tbody>
        @forelse($topBrands as $row)
          <tr>
            <td>{{ $row->name_en ?? $row->name_ar ?? ('#'.$row->brand_id) }}</td>
            <td>{{ number_format((float) $row->avg_rating, 2) }}</td>
            <td>{{ $row->reviews_count }}</td>
          </tr>
        @empty
          <tr><td colspan="3">No data</td></tr>
        @endforelse
      </tbody>
    </table>
  </body>
</html>
