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

                            <div class="card mt-4">
                                <div class="card-header">
                                    <h3 class="card-title">Maintenance Order</h3>
                                </div>
                                <div class="col-sm-12">
                                    <!-- Alert success -->
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

                                    <!-- Validasi form -->
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
                                    <!-- End validasi form -->
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="mb-3 col-sm-12">
                                            <!-- QR Reader -->
                                            <div class="modal-body">
                                                <div class="d-flex justify-content-center">
                                                    <div id="qr-reader" style="width:500px"></div>
                                                    <div id="qr-reader-results"></div>
                                                </div>
                                                <div class="d-flex justify-content-center mt-3">
                                                    <input readonly type="text" id="qr-value" class="form-control" placeholder="Scanned QR Code Value">
                                                </div>
                                            </div>
                                            <div class="modal-footer d-flex justify-content-center">
                                                <button id="showModalBtn" type="button" class="btn btn-primary" disabled>Proceed</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Maintenance Order Modal -->
                                <div class="modal fade" id="maintenanceOrderModal" tabindex="-1" aria-labelledby="maintenanceOrderModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="maintenanceOrderModalLabel">Add Maintenance Order</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form id="maintenanceOrderForm" action="{{ url('/maintenance-orders/store') }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="asset_no" id="modal-asset-no">

                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label for="date" class="form-label">Next Production</label>
                                                            <input type="date" name="date" class="form-control" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="problem" class="form-label">Problem</label>
                                                            <input type="text" name="problem" class="form-control" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="pic" class="form-label">PIC</label>
                                                            <input type="text" name="pic" class="form-control" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="image" class="form-label">Upload Image</label>
                                                            <input type="file" name="img" class="form-control" accept="image/*">
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-success">Submit Maintenance Order</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- /.card-body -->
                            </div>
                            <br>

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
                                                                <div class="col-md-8">
                                                                    <!-- Combined Dropdown -->
                                                                    <label for="machineSelect" class="form-label">Machine</label>
                                                                    <select name="orders[0][machine]" id="machineSelect" class="form-control chosen-select" data-placeholder="Choose a dies..." required>
                                                                        <option value="">Choose a dies...</option>
                                                                        <!-- Options will be populated dynamically via JavaScript -->
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-4">
                                                                    <!-- Date Input -->
                                                                    <label for="date" class="form-label">Next Production</label>
                                                                    <input type="date" name="orders[0][date]" class="form-control" required>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <!-- Problem Input -->
                                                                    <label for="problem" class="form-label">Problem</label>
                                                                    <input type="text" name="orders[0][problem]" class="form-control" required>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <!-- Image Upload -->
                                                                    <label for="image" class="form-label">Upload Image</label>
                                                                    <input type="file" name="orders[0][img]" class="form-control" accept="image/*">
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <!-- Problem Input -->
                                                                    <label for="pic" class="form-label">pic</label>
                                                                    <input type="text" name="orders[0][pic]" class="form-control" required>
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
document.addEventListener("DOMContentLoaded", function () {
    let orderIndex = 1;
    const dies = @json($dies); // Include the necessary data for the dropdown

    // Function to populate options for a Chosen dropdown
    function populateChosenOptions(selectElement) {
        selectElement.empty(); // Clear existing options
        selectElement.append('<option></option>'); // Add an empty placeholder
        dies.forEach((die) => {
            selectElement.append(
                `<option value="${die.asset_no}">${die.code} (${die.process})</option>`
            );
        });
        selectElement.chosen({ width: "100%" }); // Initialize or reinitialize Chosen plugin
    }

    // Initialize Chosen for the first dropdown in the modal
    const initialChosen = $("#machineSelect");
    populateChosenOptions(initialChosen);

    // Reinitialize Chosen when the modal is opened
    $("#addMaintenanceOrderModal").on("shown.bs.modal", function () {
        initialChosen.trigger("chosen:updated");
    });

    // Add new maintenance order entry
    document.getElementById("addMaintenanceOrder").addEventListener("click", function () {
        const container = document.getElementById("maintenanceOrdersContainer");

        // Create a new maintenance order entry
        const newEntry = document.createElement("div");
        newEntry.classList.add("maintenance-order-entry", "border", "rounded", "p-3", "mb-3");
        newEntry.innerHTML = `
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="machineSelect" class="form-label">Machine</label>
                    <select name="orders[${orderIndex}][machine]" class="form-control chosen-select" data-placeholder="Choose a dies..." required>
                        <option value="">Choose a dies...</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="date" class="form-label">Next Production</label>
                    <input type="date" name="orders[${orderIndex}][date]" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="problem" class="form-label">Problem</label>
                    <input type="text" name="orders[${orderIndex}][problem]" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="img" class="form-label">Upload Image</label>
                    <input type="file" name="orders[${orderIndex}][img]" class="form-control" accept="image/*">
                </div>
                <div class="col-md-6">
                    <label for="pic" class="form-label">pic</label>
                    <input type="text" name="orders[${orderIndex}][pic]" class="form-control" required>
                </div>
                <div class="col-md-6 d-flex align-items-center">
                    <button type="button" class="btn btn-danger w-100 remove-entry">Remove</button>
                </div>
            </div>
        `;
        container.appendChild(newEntry);

        // Populate and initialize Chosen for the new dropdown
        const newSelect = $(newEntry).find(".chosen-select");
        populateChosenOptions(newSelect);

        orderIndex++;
    });

    // Remove an entry
    document.getElementById("maintenanceOrdersContainer").addEventListener("click", function (e) {
        if (e.target.classList.contains("remove-entry")) {
            e.target.closest(".maintenance-order-entry").remove();
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
                                                        <th>PIC</th>
                                                        <th>Problem</th>
                                                        <th>Next Production</th>
                                                        <th>Current Qty</th>
                                                        <th>Image</th> <!-- New Image Column -->
                                                        <th>Status</th>
                                                        @if(\Auth::user()->role === 'Super Admin' || \Auth::user()->role === 'IT')
                                                        <th>Action</th> <!-- Conditionally shown for authorized roles -->
                                                    @endif
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

                                                        <td>
                                                            @if ($data->dies->code ?? false)
                                                                <a href="{{ url('/checksheet/scan/' . $data->dies->asset_no) }}">
                                                                    {{ $data->dies->code }}
                                                                </a>
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>

                                                        <td>{{ $data->dies->process ?? 'N/A' }}</td>
                                                        <td>{{ $data->dies->std_stroke ?? 'N/A' }}</td>
                                                        <td>{{$data->pic}}</td>
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
                                                        <td>
                                                            @if ($data->status == 1)
                                                            <a href="#" class="badge bg-success text-decoration-none" data-bs-toggle="modal" data-bs-target="#repairDetailModal{{ $data->id }}">
                                                                <i class="fas fa-check-circle"></i> Repaired
                                                            </a>

                                                            <!-- Repair Detail Modal -->
                                                            <div class="modal fade" id="repairDetailModal{{ $data->id }}" tabindex="-1" aria-labelledby="repairDetailModalLabel{{ $data->id }}" aria-hidden="true">
                                                                <div class="modal-dialog modal-lg">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title" id="repairDetailModalLabel{{ $data->id }}">Repair Details</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div class="row">
                                                                                <div class="col-md-6">
                                                                                    <p><strong>Date:</strong> {{ $data->repair->date }}</p>
                                                                                    <p><strong>PIC:</strong> {{ $data->repair->pic }}</p>
                                                                                    <p><strong>Problem:</strong> {{ $data->repair->problem }}</p>
                                                                                    <p><strong>Action:</strong> {{ $data->repair->action }}</p>
                                                                                    <p><strong>Status:</strong> {{ $data->repair->status }}</p>
                                                                                    <p><strong>Image After Repair:</strong><br>
                                                                                        <img src="{{ asset($data->repair->img_after) }}" alt="After Image" width="50%">
                                                                                    </p>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <p><strong>Start Time:</strong> {{ $data->repair->start_time }}</p>
                                                                                    <p><strong>End Time:</strong> {{ $data->repair->end_time }}</p>
                                                                                    <p><strong>Remarks:</strong> {{ $data->repair->remarks }}</p>
                                                                                    <p><strong>Signature:</strong><br>
                                                                                        <img src="{{ $data->repair->signature }}" alt="Signature" width="100">

                                                                                    </p>
                                                                                    <p><strong>Image Before Repair:</strong><br>
                                                                                        <img src="{{ asset($data->repair->img_before) }}" alt="Before Image" width="50%">
                                                                                    </p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @else
                                                                <a href="{{ route('dies.repair.req', ['id' => encrypt($data->dies->id), 'order_id' => encrypt($data->id)]) }}" class="badge bg-warning text-decoration-none">
                                                                    <i class="fas fa-exclamation-circle"></i> Pending
                                                                </a>
                                                            @endif
                                                        </td>

                                                            @if(\Auth::user()->role === 'Super Admin' || \Auth::user()->role === 'IT')

                                                            <td><form action="{{ route('mtc.order.destroy', $data->id) }}" method="POST" style="display:inline-block;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                                        <i class="fas fa-trash-alt"></i> Delete
                                                                    </button>
                                                                </form>
                                                            </td>
                                                            @endif


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
<script>
$(document).on('click', '.delete-order', function () {
    const orderId = $(this).data('id');
    if (confirm('Are you sure you want to delete this order?')) {
        $.ajax({
            url: `/mtc/order/${orderId}`,
            type: 'DELETE', // Ensure this is DELETE
            data: {
                _token: $('meta[name="csrf-token"]').attr('content') // Include the CSRF token
            },
            success: function (response) {
                if (response.success) {
                    alert(response.message);
                    location.reload(); // Reload the page to update the table
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert('An error occurred while deleting the order.');
            }
        });
    }
});


</script>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script>
 document.addEventListener("DOMContentLoaded", function () {
    const resultContainer = document.getElementById("qr-reader-results");
    const inputField = document.getElementById("qr-value");
    const showModalBtn = document.getElementById("showModalBtn");
    const modalAssetNoInput = document.getElementById("modal-asset-no");

    // Function to handle QR code scan success
    function onScanSuccess(decodedText, decodedResult) {
        console.log(`Decoded text: ${decodedText}`);
        inputField.value = decodedText; // Set scanned value in input
        modalAssetNoInput.value = decodedText; // Set hidden field in modal
        showModalBtn.disabled = false; // Enable the Proceed button
    }

    // Initialize QR code scanner
    const html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render(onScanSuccess);

    // Show modal on Proceed button click
    showModalBtn.addEventListener("click", function () {
        const maintenanceOrderModal = new bootstrap.Modal(document.getElementById("maintenanceOrderModal"));
        maintenanceOrderModal.show();
    });
});

</script>
@endsection
