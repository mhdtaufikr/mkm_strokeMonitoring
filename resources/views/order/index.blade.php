@extends('layouts.master')

@section('content')
<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="tool"></i></div>
                            Maintenance Order
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4 mt-n10">
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row ">
                        <div class="col-sm-6">
                            <h1></h1>
                        </div>
                    </div>
                </div>
            </section>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">List of Maintenance Order</h3>
                                </div>
                                <div class="card-body">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMaintenanceOrderModal">
                                        Add Maintenance Order
                                    </button>
                                  <!-- Add Maintenance Order Modal -->
                                <div class="modal fade" id="addMaintenanceOrderModal" tabindex="-1" aria-labelledby="addMaintenanceOrderModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addMaintenanceOrderModalLabel">Add Maintenance Order</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form id="maintenanceOrderForm" action="{{ route('mtc_orders.store') }}" method="POST" enctype="multipart/form-data">
                                                    @csrf

                                                    <!-- Container for multiple maintenance orders -->
                                                    <div id="maintenanceOrdersContainer">
                                                        <div class="maintenance-order-entry border rounded p-3 mb-3">
                                                            <div class="row g-3">
                                                                <div class="col-md-6">
                                                                    <!-- Part Name Dropdown -->
                                                                    <label for="partName" class="form-label">Part Name</label>
                                                                    <select name="orders[0][part_name]" class="form-select part-name" required>
                                                                        <option value="">Select Part Name</option>
                                                                        @foreach($distinctPartNames as $partName)
                                                                            <option value="{{ $partName }}">{{ $partName }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <!-- Code and Process -->
                                                                    <label for="codeProcess" class="form-label">Code - Process</label>
                                                                    <select name="orders[0][code_process]" class="form-select code-process" required>
                                                                        <option value="">Select Code - Process</option>
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <!-- Problem Input -->
                                                                    <label for="problem" class="form-label">Problem</label>
                                                                    <input type="text" name="orders[0][problem]" class="form-control" required>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <!-- Date Input -->
                                                                    <label for="date" class="form-label">Date</label>
                                                                    <input type="date" name="orders[0][date]" class="form-control" required>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <!-- Image Upload -->
                                                                    <label for="image" class="form-label">Upload Image</label>
                                                                    <input type="file" name="orders[0][img]" class="form-control" accept="image/*">
                                                                </div>

                                                                <div class="col-md-6 d-flex align-items-center">
                                                                    <!-- Remove Button -->
                                                                    <button type="button" class="btn btn-danger w-100 remove-entry">Remove</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Button to Add More Maintenance Orders -->
                                                    <div class="d-flex justify-content-between mt-3">
                                                        <button type="button" class="btn btn-primary" id="addMaintenanceOrder">Add Another Maintenance Order</button>
                                                        <button type="submit" class="btn btn-success">Submit Maintenance Orders</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        let orderIndex = 1;

                                        // Function to add a new maintenance order entry
                                        document.getElementById("addMaintenanceOrder").addEventListener("click", function() {
                                            const container = document.getElementById("maintenanceOrdersContainer");

                                            // Create a new entry div with proper layout
                                            const newEntry = document.createElement("div");
                                            newEntry.classList.add("maintenance-order-entry", "border", "rounded", "p-3", "mb-3");
                                            newEntry.innerHTML = `
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Part Name</label>
                                                        <select name="orders[${orderIndex}][part_name]" class="form-select part-name" required>
                                                            <option value="">Select Part Name</option>
                                                            @foreach($distinctPartNames as $partName)
                                                                <option value="{{ $partName }}">{{ $partName }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Code - Process</label>
                                                        <select name="orders[${orderIndex}][code_process]" class="form-select code-process" required>
                                                            <option value="">Select Code - Process</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Problem</label>
                                                        <input type="text" name="orders[${orderIndex}][problem]" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Date</label>
                                                        <input type="date" name="orders[${orderIndex}][date]" class="form-control" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Upload Image</label>
                                                        <input type="file" name="orders[${orderIndex}][img]" class="form-control" accept="image/*">
                                                    </div>
                                                    <div class="col-md-6 d-flex align-items-center">
                                                        <button type="button" class="btn btn-danger w-100 remove-entry">Remove</button>
                                                    </div>
                                                </div>
                                            `;
                                            container.appendChild(newEntry);
                                            orderIndex++;
                                        });

                                        // Function to remove an entry
                                        document.getElementById("maintenanceOrdersContainer").addEventListener("click", function(e) {
                                            if (e.target.classList.contains("remove-entry")) {
                                                e.target.closest(".maintenance-order-entry").remove();
                                            }
                                        });

                                        // Fetch Code and Process dynamically based on Part Name selection
                                        document.addEventListener("change", function(event) {
                                            if (event.target.classList.contains("part-name")) {
                                                const partName = event.target.value;
                                                const codeProcessSelect = event.target.closest(".maintenance-order-entry").querySelector(".code-process");

                                                // Clear existing options
                                                codeProcessSelect.innerHTML = '<option value="">Select Code - Process</option>';

                                                if (partName) {
                                                    fetch(`/get-code-process?part_name=${encodeURIComponent(partName)}`)
                                                        .then(response => response.json())
                                                        .then(data => {
                                                            data.forEach(item => {
                                                                const option = document.createElement("option");
                                                                option.value = item.id; // Use `id` or any unique identifier
                                                                option.textContent = `${item.code} - ${item.process}`;
                                                                codeProcessSelect.appendChild(option);
                                                            });
                                                        })
                                                        .catch(error => console.error("Error fetching code and process:", error));
                                                }
                                            }
                                        });
                                    });
                                    </script>


                                    <div class="row">
                                        <div class="mb-3 col-sm-12">
                                            @if (session('status'))
                                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                    <strong>{{ session('status') }}</strong>
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>
                                            @endif
                                            @if (session('failed'))
                                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                    <strong>{{ session('failed') }}</strong>
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>
                                            @endif
                                            @if (count($errors) > 0)
                                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                    <ul>
                                                        <li><strong>Data Process Failed !</strong></li>
                                                        @foreach ($errors->all() as $error)
                                                            <li><strong>{{ $error }}</strong></li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="table-responsive">
                                            <table id="tableInventory" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Part Name</th>
                                                        <th>Code</th>
                                                        <th>Process</th>
                                                        <th>Std Stroke</th>
                                                        <th>Problem</th>
                                                        <th>Date</th>
                                                        <th>Current Qty</th>
                                                        <th>Image</th> <!-- New Image Column -->
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $no = 1;
                                                    @endphp
                                                    @foreach ($items as $data)
                                                    <tr>
                                                        <td>{{ $no++ }}</td>
                                                        <td>{{ $data->dies->part_name ?? 'N/A' }}</td>
                                                        <td>{{ $data->dies->code ?? 'N/A' }}</td>
                                                        <td>{{ $data->dies->process ?? 'N/A' }}</td>
                                                        <td>{{ $data->dies->std_stroke ?? 'N/A' }}</td>
                                                        <td>{{ $data->problem }}</td>
                                                        <td>{{ $data->date }}</td>
                                                        <td>{{ $data->dies->current_qty ?? 'N/A' }}</td>
                                                        <td>
                                                            @if ($data->img)
                                                                <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-image="{{ asset($data->img) }}">
                                                                    <img src="{{ asset($data->img) }}" alt="Image" class="img-thumbnail" style="width: 50px; height: auto;">
                                                                </a>
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Full Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Image" class="img-fluid">
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const imageModal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');

        imageModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget; // Button that triggered the modal
            const imageUrl = button.getAttribute('data-image'); // Extract image URL from data-* attribute
            modalImage.src = imageUrl; // Update the modal image src
        });

        imageModal.addEventListener('hidden.bs.modal', function() {
            modalImage.src = ""; // Clear the image when the modal is closed
        });
    });
    </script>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>
<!-- For Datatables -->
<script>
    $(document).ready(function() {
        var table = $("#tableInventory").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
        });
    });
</script>
@endsection
