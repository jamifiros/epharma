@extends('layout') <!-- Extend the master layout -->

@section('title', 'Stocks') <!-- Override the title section -->

@section('content')
<div class="main-content">
    <h3>Stocks</h3>

    <!-- Button to Open Modal -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addStockModal">
        + Add New Stock
    </button>

    @if ($stocks->isEmpty())
        <p>No stocks available.</p>
    @else
        <table class="table table-responsive table-striped table-success">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>MRP</th>
                    <th>Batch No</th>
                    <th>Expiry Date</th>
                    <th>Quantity</th>
                    <th>Payout</th>
                    <th>Balance</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stocks as $stock)
                            <tr data-id="{{ $stock->id }}">
                                <td>{{ $stock->medicine_name }}</td>
                                <td>{{ $stock->MRP }}</td>
                                <td class="det" data-name="batch_no" data-type="text">{{ $stock->stockDetails->batch_no ?? 'N/A' }}</td>
                                <td class="det" data-name="expiry_date" data-type="date">
                                    @php
                                        $expiryDate = $stock->stockDetails->expiry_date ?? null;
                                    @endphp

                                    @if($expiryDate)
                                        @if(\Carbon\Carbon::parse($expiryDate)->isPast())
                                            <span style="color: red;">Expired ({{ \Carbon\Carbon::parse($expiryDate)->format('d-m-Y') }})</span>
                                        @else
                                            {{ \Carbon\Carbon::parse($expiryDate)->format('d-m-Y') }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>

                                <td class="det" data-name="quantity" data-type="number">
                                    @php
                                        $quantity = $stock->stockDetails->quantity ?? null;
                                    @endphp

                                    @if($quantity !== null)
                                        @if($quantity < 10)
                                            <span style="color: red; font-weight: bold;">{{ $quantity }} (Low)</span>
                                        @elseif($quantity >= 10 && $quantity <= 20)
                                            <span style="color: orange; font-weight: bold;">{{ $quantity }} (Medium)</span>
                                        @else
                                            <span style="color: green; font-weight: bold;">{{ $quantity }} (Sufficient)</span>
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>

                                <td class="det" data-name="payout" data-type="number">{{ $stock->stockDetails->payout ?? 'N/A' }}</td>
                                <td class="det" data-name="balance" data-type="number">{{ $stock->stockDetails->balance ?? 'N/A' }}</td>
                                <td>
                                    <button class="btn btn-primary edit-btn">Edit</button>
                                </td>
                                <td>
                                    <a href="{{ route('admin.stocks.delete', $stock->id) }}" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this stock?');">Delete</a>
                                </td>
                            </tr>
                @endforeach
            </tbody>
        </table>

    @endif
</div>

<!-- Add Stock Modal -->
<div class="modal fade" id="addStockModal" tabindex="-1" aria-labelledby="addStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.stocks.create') }}" method="POST"
                style="border: 1px solid #ddd; padding:20px;">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addStockModalLabel">Add New Stocks</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="medicineFormsContainer" style="height: 400px; overflow-y: auto; padding: 10px;">
                        <!-- Initial Medicine Form -->
                        <div class="medicine-form card p-3 mb-3">
                            <div class="mb-3">
                                <label for="medicineName" class="form-label">Medicine Name</label>
                                <input type="text" class="form-control" name="medicine_name[]"
                                    placeholder="Enter Medicine Name" required>
                            </div>
                            <div class="mb-3">
                                <label for="MRP" class="form-label">MRP</label>
                                <input type="number" class="form-control" name="MRP[]" placeholder="Enter MRP" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="addMedicineBtn" class="btn btn-primary mb-2">+ Add More Medicine</button>
                    <button type="submit" class="btn btn-success w-100">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script for Adding More Forms -->
<script>
    document.getElementById('addMedicineBtn').addEventListener('click', function (e) {
        e.preventDefault(); // Prevent form submission on button click
        const medicineFormsContainer = document.getElementById('medicineFormsContainer');

        // Clone the first medicine form
        const firstMedicineForm = medicineFormsContainer.querySelector('.medicine-form');
        const newMedicineForm = firstMedicineForm.cloneNode(true);

        // Clear the inputs in the cloned form
        newMedicineForm.querySelectorAll('input').forEach(input => input.value = '');

        // Append the cloned form to the container
        medicineFormsContainer.appendChild(newMedicineForm);

        // Scroll to the newly created form
        newMedicineForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    document.addEventListener('DOMContentLoaded', function () {
    // Add event listener to all "Edit" buttons
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr'); // Get the parent row of the button
            const editMode = row.getAttribute('data-edit-mode') === 'true'; // Check if edit mode is active

            if (!editMode) {
                // Switch to edit mode
                row.setAttribute('data-edit-mode', 'true');
                this.textContent = 'Save'; // Change button text to Save

                // Replace the text of each 'td.det' with an input field
                row.querySelectorAll('.det').forEach(cell => {
                    let value = cell.textContent.trim() === 'N/A' ? '' : cell.textContent.trim(); // Default value or empty
                    const name = cell.dataset.name;
                    const type = cell.dataset.type;

                    // Handle numeric fields
                    if (type === 'number') {
                        value = value.replace(/[^\d.-]/g, ''); // Remove non-numeric characters
                    }

                    // Handle date fields
                    if (type === 'date') {
                        // Convert displayed date (e.g., d-m-Y) to ISO format (YYYY-MM-DD)
                        const parts = value.split('-'); // Assuming format is d-m-Y
                        if (parts.length === 3) {
                            const [day, month, year] = parts;
                            value = `${year}-${month}-${day}`; // Reformat to ISO
                        }
                    }

                    // Insert an input field with the appropriate value
                    cell.innerHTML = `
                        <input type="${type}" 
                               class="form-control" 
                               name="${name}" 
                               value="${value}" 
                               placeholder="Enter ${name}">`;
                });
            } else {
                // Switch to view mode and submit the form
                row.setAttribute('data-edit-mode', 'false');
                this.textContent = 'Edit'; // Change button text back to Edit

                // Collect data and make an AJAX request to save the changes
                const stockId = row.dataset.id;
                const formData = {};

                row.querySelectorAll('input').forEach(input => {
                    formData[input.name] = input.value || null; // Use null if the field is empty
                });

                fetch(`/stocks/${stockId}/details/update`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(formData),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Replace input fields with their updated values
                            row.querySelectorAll('.det').forEach(cell => {
                                const name = cell.dataset.name;
                                cell.textContent = formData[name] || 'N/A'; // Display updated value or 'N/A'
                            });
                        } else {
                            alert('Failed to update stock details.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });
    });
});


</script>
@endsection