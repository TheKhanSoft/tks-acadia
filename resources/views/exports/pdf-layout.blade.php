<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $title ?? 'Export Document' }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            /* Ensure good character support */
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .subtitle {
            font-size: 14px;
            margin-bottom: 15px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            vertical-align: top;
            /* Align text to top */
            word-wrap: break-word;
            /* Wrap long text */
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #333;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            color: #777;
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            height: 30px;
        }

        /* Add page numbers */
        .page-number:before {
            content: "Page " counter(page);
        }

        @page {
            margin: 40px 50px;
            /* top, right, bottom, left */
        }
    </style>
</head>

<body>
    <div class="header">
        @if (isset($title) && $title)
            <div class="title">{{ $title }}</div>
        @endif
        @if (isset($subtitle) && $subtitle)
            <div class="subtitle">{{ $subtitle }}</div>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                @foreach ($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>


            @forelse($data as $item)
                <tr>
                    @foreach ($mapKeys as $key)
                        <td>
                            @php
                                $value = data_get($item, $key);
                                // Basic formatting for common types
                                if ($value instanceof \Carbon\Carbon) {
                                    echo $value->format('Y-m-d H:i:s'); // Or 'Y-m-d'
                                } elseif (is_bool($value)) {
                                    echo $value ? 'Yes' : 'No';
                                } elseif (is_array($value) || is_object($value)) {
                                    // Avoid complex objects in PDF, maybe show count or simple summary
                                    echo is_array($value) ? 'Array[' . count($value) . ']' : 'Object';
                                } else {
                                    // Use htmlspecialchars to ensure proper encoding for PDF rendering
                                    echo htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
                                }
                            @endphp
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headings) }}" style="text-align: center;">No data available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated on: {{ now()->format('Y-m-d H:i:s') }} | <span class="page-number"></span>
    </div>

</body>

</html>
