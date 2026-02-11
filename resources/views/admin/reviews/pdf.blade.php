<!doctype html>
<html lang="{{ $lang ?? app()->getLocale() }}" dir="{{ !empty($isRtl) ? 'rtl' : 'ltr' }}">
  <head>
    <meta charset="utf-8">
    <title>{{ !empty($isRtl) ? 'تصدير المراجعات' : 'Reviews Export' }}</title>
    <style>
      body {
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 12px;
        color: #111;
        direction: {{ !empty($isRtl) ? 'rtl' : 'ltr' }};
      }
      h1 { font-size: 18px; margin: 0 0 6px; }
      .muted { color: #6b7280; }
      table { width: 100%; border-collapse: collapse; margin-top: 12px; }
      th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: {{ !empty($isRtl) ? 'right' : 'left' }}; vertical-align: top; }
      th { background: #f3f4f6; }
      .cell-rtl { direction: rtl; text-align: right; unicode-bidi: plaintext; }
      .cell-ltr { direction: ltr; text-align: left; unicode-bidi: plaintext; }
    </style>
  </head>
  <body>
    <h1>{{ !empty($isRtl) ? 'تصدير المراجعات' : 'Reviews Export' }}</h1>
    <div class="muted">{{ !empty($isRtl) ? 'الفترة' : 'Range' }}: {{ $rangeLabel }}</div>

    <table>
      <thead>
        <tr>
          <th>{{ !empty($isRtl) ? 'المعرف' : 'ID' }}</th>
          <th>{{ !empty($isRtl) ? 'المستخدم' : 'User' }}</th>
          <th>{{ !empty($isRtl) ? 'التقييم' : 'Rating' }}</th>
          <th>{{ !empty($isRtl) ? 'التعليق' : 'Comment' }}</th>
          <th>{{ !empty($isRtl) ? 'مخفي' : 'Hidden' }}</th>
          <th>{{ !empty($isRtl) ? 'مميز' : 'Featured' }}</th>
          <th>{{ !empty($isRtl) ? 'تم الرد' : 'Replied' }}</th>
          <th>{{ !empty($isRtl) ? 'الفرع' : 'Branch' }}</th>
          <th>{{ !empty($isRtl) ? 'المكان' : 'Place' }}</th>
          <th>{{ !empty($isRtl) ? 'تاريخ الإنشاء' : 'Created At' }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($reviews as $r)
          <tr>
            <td>{{ $r->id }}</td>
            <td class="{{ !empty($isRtl) ? 'cell-rtl' : 'cell-ltr' }}">{{ $r->user?->name ?? '-' }}</td>
            <td>{{ $r->overall_rating ?? '-' }}</td>
            <td class="{{ !empty($isRtl) ? 'cell-rtl' : 'cell-ltr' }}">{{ $r->comment ?? '-' }}</td>
            <td>{{ $r->is_hidden ? (!empty($isRtl) ? 'نعم' : 'yes') : (!empty($isRtl) ? 'لا' : 'no') }}</td>
            <td>{{ $r->is_featured ? (!empty($isRtl) ? 'نعم' : 'yes') : (!empty($isRtl) ? 'لا' : 'no') }}</td>
            <td>{{ $r->replied_at ? (!empty($isRtl) ? 'نعم' : 'yes') : (!empty($isRtl) ? 'لا' : 'no') }}</td>
            <td class="{{ !empty($isRtl) ? 'cell-rtl' : 'cell-ltr' }}">
              {{ !empty($isRtl)
                ? ($r->branch?->name_ar ?? $r->branch?->name_en ?? $r->branch?->name ?? '-')
                : ($r->branch?->name_en ?? $r->branch?->name_ar ?? $r->branch?->name ?? '-') }}
            </td>
            <td class="{{ !empty($isRtl) ? 'cell-rtl' : 'cell-ltr' }}">
              {{ !empty($isRtl)
                ? ($r->place?->name_ar
                  ?? $r->place?->title_ar
                  ?? $r->place?->display_name
                  ?? $r->place?->name
                  ?? $r->place?->name_en
                  ?? $r->place?->title_en
                  ?? '-')
                : ($r->place?->display_name
                ?? $r->place?->name
                ?? $r->place?->name_en
                ?? $r->place?->title_en
                ?? $r->place?->name_ar
                ?? $r->place?->title_ar
                ?? '-') }}
            </td>
            <td class="cell-ltr">{{ $r->created_at?->toDateTimeString() ?? '-' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="10">{{ !empty($isRtl) ? 'لا توجد مراجعات' : 'No reviews' }}</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </body>
</html>
