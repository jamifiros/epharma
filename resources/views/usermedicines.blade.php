@extends('layout')

@section('title', 'User Medicines')

@section('content')
<div class="main-content">
    <h3>User Name: {{ $user->name }}</h3>

    @if (empty($medicines))
        <p>No medicines found for this user.</p>
    @else
        <table class="table table-responsive table-striped table-success">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Morning</th>
                    <th>Afternoon</th>
                    <th>Evening</th>
                    <th>Night</th>
                    <th>Timing</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($medicines as $medicine)
                    <tr>
                        <td>{{ $medicine->medicine_name }}</td>
                        <td>{{ $medicine->morning ? '1' : '0' }}</td>
                        <td>{{ $medicine->afternoon ? '1' : '0' }}</td>
                        <td>{{ $medicine->evening ? '1' : '0' }}</td>
                        <td>{{ $medicine->night ? '1' : '0' }}</td>
                        <td>{{ $medicine->timing }}</td>
                        <td>{{ $medicine->total_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
