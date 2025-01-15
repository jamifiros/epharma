@extends('layout') <!-- Extend the master layout -->

@section('title', 'Prescriptions') <!-- Override the title section -->

@section('content')
<div class="main-content">
<h3>Patient Name: {{ $prescription->user->name }}</h3>

    @if ($prescription->medicines->isEmpty())
        <p>No medicines added for this prescription.</p>
    @else
        <table class="table table-responsive table-striped table-success">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>intake</th>
                    <th>Timing</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prescription->medicines as $medicine)
                    <tr>
                        <td>{{ $medicine->medicine_name }}</td>
                        <td>1-0-0-1</td>
                        <td>{{ $medicine->timing }}</td>
                        <td>{{ $medicine->count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    @endsection