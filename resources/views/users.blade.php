@extends('layout') <!-- Extend the master layout -->

@section('title', 'Users') <!-- Override the title section -->

@section('content')
<div class="main-content">
            <h2 class="text-success mb-4">Users</h2>
            @if($users->isEmpty())
                 <p>No users found.</p>
               @else
                <div class="table-responsive">
                <table class="table table-responsive table-striped table-success">
                <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                            <a href="{{ route('admin.userdetails.show', $user->id) }}" class="btn btn-success">View Details</a>                            </td>
                        </tr>
                    @endforeach                    
                    </tbody>
                </table>
            </div>
            @endif
    </div>
    @endsection