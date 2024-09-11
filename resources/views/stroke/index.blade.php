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
                            Master Stroke
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
                                    <h3 class="card-title">List of Master Stroke</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table id="tableStrokeDies" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Code</th>
                                                        <th>Part No.</th>
                                                        <th>Process</th>
                                                        <th>Standard Stroke</th>
                                                        <th>Classification</th>
                                                        <th>Current Qty</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Data will be loaded via DataTables -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form id="editForm">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Edit Master Stroke</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="edit_id" name="id">
            <div class="mb-3">
              <label for="edit_code" class="form-label">Code</label>
              <input type="text" class="form-control" id="edit_code" name="code" required>
            </div>
            <div class="mb-3">
              <label for="edit_part_no" class="form-label">Part No.</label>
              <input type="text" class="form-control" id="edit_part_no" name="part_no" required>
            </div>
            <div class="mb-3">
              <label for="edit_process" class="form-label">Process</label>
              <input type="text" class="form-control" id="edit_process" name="process" required>
            </div>
            <div class="mb-3">
              <label for="edit_std_stroke" class="form-label">Standard Stroke</label>
              <input type="number" class="form-control" id="edit_std_stroke" name="std_stroke" required>
            </div>
            <div class="mb-3">
              <label for="edit_current_qty" class="form-label">Current Qty</label>
              <input type="number" class="form-control" id="edit_current_qty" name="current_qty" required>
            </div>
            <div class="mb-3">
              <label for="edit_classification" class="form-label">Classification</label>
              <input type="text" class="form-control" id="edit_classification" name="classification" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
        </div>
      </form>
    </div>
  </div>


</main>

<!-- For Datatables -->
<script>
    $('#tableStrokeDies').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('stroke.dies.data') }}", // Ensure this route works
    columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'code', name: 'code' },
        { data: 'part_no', name: 'part_no' },
        { data: 'process', name: 'process' },
        { data: 'std_stroke', name: 'std_stroke' },
        { data: 'classification', name: 'classification' },
        { data: 'current_qty', name: 'current_qty' },
        {
            data: 'id', // Assuming 'id' is the primary key
            name: 'id',
            orderable: false,
            searchable: false,
            render: function(data, type, row) {
                return `<button class="btn btn-primary btn-sm editBtn" data-id="${data}">Edit</button>`;
            }
        },
    ],
    responsive: true,
    lengthChange: false,
    autoWidth: false,
});

// Attach click event to the edit button
$('#tableStrokeDies tbody').on('click', '.editBtn', function() {
    var id = $(this).data('id');
    // Call the function to open the modal and load data
    loadEditForm(id);
});

function loadEditForm(id) {
    $.ajax({
        url: `/master/stroke/${id}/edit`, // Define the correct route to fetch the data
        type: 'GET',
        success: function(response) {
            // Load data into the form
            $('#edit_id').val(response.id);
            $('#edit_code').val(response.code);
            $('#edit_part_no').val(response.part_no);
            $('#edit_process').val(response.process);
            $('#edit_std_stroke').val(response.std_stroke);
            $('#edit_current_qty').val(response.current_qty);
            $('#edit_classification').val(response.classification);
            // Show the modal
            $('#editModal').modal('show');
        },
        error: function(xhr) {
            console.log(xhr.responseText);
        }
    });
}


$('#editForm').submit(function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    var id = $('#edit_id').val();

    $.ajax({
        url: `/master/stroke/${id}`, // Define the correct route for update
        type: 'PUT',
        data: formData,
        success: function(response) {
            $('#editModal').modal('hide'); // Close the modal
            $('#tableStrokeDies').DataTable().ajax.reload(); // Reload the DataTable
            alert(response.message); // Show success message
        },
        error: function(xhr) {
            console.log(xhr.responseText);
        }
    });
});



</script>
@endsection
