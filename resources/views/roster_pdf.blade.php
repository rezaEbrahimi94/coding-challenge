<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nurse Roster from {{ $startDate }} to {{ $endDate }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
            vertical-align: top;
            white-space: normal;
        }

        th {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
        }
    </style>


</head>
<body>
<h1>Nurse Roster</h1>
<h2>Period: {{ $startDate }} to {{ $endDate }}</h2>
<table>
    <thead>
    <tr>
        <th>Date</th>
        <th>Morning </th>
        <th>Evening </th>
        <th>Night </th>
    </tr>
    </thead>
    <tbody>
    @foreach ($rosters->groupBy(function ($roster) {
        return \Carbon\Carbon::parse($roster->date)->toDateString();
    }) as $date => $shifts)
        <tr>
            <td>{{ $date }}</td>
            @foreach (['morning', 'evening', 'night'] as $type)
                <td>
                    @php
                        $shift = $shifts->firstWhere('type', $type);
                    @endphp
                    {{ $shift ? $shift->nurses->pluck('name')->join(', ') : 'No shift' }}
                </td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
