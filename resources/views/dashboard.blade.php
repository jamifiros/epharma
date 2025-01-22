@extends('layout') <!-- Extend the master layout -->

@section('title', 'Dashboard') <!-- Override the title section -->

@section('content')
<div class="main-content">
    <!-- Dashboard Content -->
    <div class="container mt-4">
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-users"></i>
                            Users
                        </h5>
                        <p class="card-text">Total users: {{ $userCount }}</p>
                        <a href="{{ route('admin.users') }}" class="btn btn-green">Go to Users</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-regular fa-file-powerpoint"></i>
                            Prescriptions
                        </h5>
                        <p class="card-text">Total prescriptions: {{ $prescriptionCount }}</p>
                        <a href="{{ route('admin.prescriptions.history') }}" class="btn btn-green">View
                            Prescriptions</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex mt-4 justify-content-between align-items-center align-content-center gap-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="tbl-container p-2" style="height: 400px; overflow-y: auto;">
                        <!-- Profit Table Section -->
                        <h3>Profit Table of Medicines</h3>
                        @if(count($profitData) > 0)
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Medicine Name</th>
                                        <th>MRP (₹)</th>
                                        <th>Sailed Quantity</th>
                                        <th>Payout (₹)</th>
                                        <th>Profit (₹)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($profitData as $data)
                                        <tr>
                                            <td>{{ $data['name'] }}</td>
                                            <td>{{ $data['MRP'] }}</td>
                                            <td>{{ $data['sailed_quantity'] }}</td>
                                            <td>{{ $data['payout'] }}</td>
                                            <td>{{ $data['profit'] < 0 ? 0 : $data['profit'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p>No profit data available.</p>
                        @endif

                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="graph-content p-2">
                        <h3>Profit Graph of Medicines</h3>
                        @if(count($profitData) > 0)
                            <canvas id="profitChart" width="400" height="260" style="overflow-x:auto"></canvas>
                        @else
                            <p>No profit data available.</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // const profitData = @json($profitData);

    // if (profitData.length > 0) {
    //     const labels = profitData.map(item => item.name);
    //     const profits = profitData.map(item => item.profit);

    //     const ctx = document.getElementById('profitChart').getContext('2d');
    //     new Chart(ctx, {
    //         type: 'bar',
    //         data: {
    //             labels: labels,
    //             datasets: [{
    //                 label: 'Profit (₹)',
    //                 data: profits,
    //                 backgroundColor: 'rgba(75, 192, 192, 0.2)',
    //                 borderColor: 'rgba(75, 192, 192, 1)',
    //                 borderWidth: 1
    //             }]
    //         },
    //         options: {
    //             responsive: true,
    //             scales: {
    //                 y: {
    //                     beginAtZero: true,
    //                     title: {
    //                         display: true,
    //                         text: 'Profit (₹)'
    //                     }
    //                 },
    //                 x: {
    //                     title: {
    //                         display: true,
    //                         text: 'Medicine Name'
    //                     }
    //                 }
    //             }
    //         }
    //     });
    // }

    const profitData = @json($profitData);

    if (profitData.length > 0) {
        const labels = profitData.map(item => item.name);
        const profits = profitData.map(item => item.profit);

        const ctx = document.getElementById('profitChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',  // Change to line chart
            data: {
                labels: labels,
                datasets: [{
                    label: 'Profit (₹)',
                    data: profits,
                    backgroundColor: 'rgba(122, 75, 192, 0.2)',  // Line chart's area color
                    borderColor: 'rgb(57, 9, 169)',  // Line color
                    borderWidth: 2,  // Line thickness
                    fill: true,  // Fill the area under the line
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Profit (₹)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Medicine Name'
                        }
                    }
                }
            }
        });
    }

</script>

@endsection