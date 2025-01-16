@extends('layout') <!-- Extend the master layout -->

@section('title', 'Prescriptions-history') <!-- Override the title section -->

@section('content')
<div class="main-content">
    <h2>All Medicines</h2>
    
    @if ($medicines->isEmpty())
        <p>No medicines found.</p>
    @else
        <table class="table table-responsive table-striped table-success">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Medicine Name</th>
                    <th>Morning</th>
                    <th>Afternoon</th>
                    <th>Evening</th>
                    <th>Night</th>
                    <th>Timing</th>
                    <th>Total Count</th>
                    <th>Prescription ID</th>
                    <th>User Name</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($medicines as $medicine)
                    <tr>
                        <td>{{ $medicine->id }}</td>
                        <td>{{ $medicine->medicine_name }}</td>
                        <td>{{ $medicine->morning ? 'Yes' : 'No' }}</td>
                        <td>{{ $medicine->afternoon ? 'Yes' : 'No' }}</td>
                        <td>{{ $medicine->evening ? 'Yes' : 'No' }}</td>
                        <td>{{ $medicine->night ? 'Yes' : 'No' }}</td>
                        <td>{{ $medicine->timing }}</td>
                        <td>{{ $medicine->total_count }}</td>
                        <td>{{ $medicine->prescription_id }}</td>
                        <td>{{ $medicine->prescription->user->name ?? 'Unknown' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
