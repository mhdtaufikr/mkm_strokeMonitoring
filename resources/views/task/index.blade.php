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
                            Task List
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
                                    <h3 class="card-title">List of Task List</h3>
                                </div>
                                <div class="card-body">
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
                                        <div class="col-sm-12">
                                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addTasklistModal">
                                                <i class="fas fa-plus"></i> Add Tasklist
                                            </button>
                                        </div>
                                         <!-- Modal -->
                                    <div class="modal fade" id="addTasklistModal" tabindex="-1" aria-labelledby="addTasklistModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="addTasklistModalLabel">Add Tasklist</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('tasklist.store') }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="name" class="form-label">Name</label>
                                                            <select class="form-control" id="name" name="name" required>
                                                                <option value="">-- Select PIC --</option>
                                                                @foreach ($names as $name)
                                                                    <option value="{{ $name }}">{{ $name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="job" class="form-label">Job</label>
                                                            <select class="form-control" id="job" name="job" required>
                                                                <option value="">-- Select Job --</option>
                                                                <option value="PM List">PM List</option>
                                                                <option value="MTC Order List">MTC Order List</option>
                                                                <option value="Other">Other</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3" id="description-container">
                                                            <!-- Kontainer untuk input deskripsi -->
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="start_date" class="form-label">Start Time</label>
                                                            <input type="datetime-local" class="form-control" id="start_date" name="start_date" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="end_date" class="form-label">End Time</label>
                                                            <input type="datetime-local" class="form-control" id="end_date" name="end_date" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="status" class="form-label">Status</label>
                                                            <select class="form-control" id="status" name="status" required>
                                                                <option value="Open">Open</option>
                                                                <option value="Close">Close</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Save Task</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                        <script>
                                            $(document).ready(function () {
                                                $('#job').on('change', function () {
                                                    var selectedJob = $(this).val();
                                                    var descriptionContainer = $('#description-container');

                                                    descriptionContainer.empty(); // Kosongkan kontainer deskripsi

                                                    if (selectedJob === 'PM List') {
                                                        // Tampilkan dropdown untuk PM List
                                                        var pmDropdown = `
                                                            <label for="pm_list" class="form-label">Select PM Item</label>
                                                            <select class="form-control" id="pm_list" name="description" required>
                                                                <option value="">-- Select PM Item --</option>
                                                                @foreach ($pmListItems as $item)
                                                                    <option value="{{ $item->stroke_code }} - {{ $item->stroke_process }}">
                                                                        {{ $item->stroke_code }} - {{ $item->stroke_process }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        `;
                                                        descriptionContainer.append(pmDropdown);
                                                    } else if (selectedJob === 'MTC Order List') {
                                                        // Tampilkan dropdown untuk MTC Order List
                                                        var mtcDropdown = `
                                                            <label for="repair_list" class="form-label">Select Repair Item</label>
                                                            <select class="form-control" id="repair_list" name="description" required>
                                                                <option value="">-- Select Repair Item --</option>
                                                                @foreach ($repairItems as $item)
                                                                    <option value="{{ $item->dies->code }} - {{ $item->dies->process }}">
                                                                        {{ $item->dies->code }} - {{ $item->dies->process }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        `;
                                                        descriptionContainer.append(mtcDropdown);
                                                    } else if (selectedJob === 'Other') {
                                                        // Tampilkan textarea untuk input bebas
                                                        var otherInput = `
                                                            <label for="other_description" class="form-label">Description</label>
                                                            <textarea class="form-control" id="other_description" name="description" rows="3" required></textarea>
                                                        `;
                                                        descriptionContainer.append(otherInput);
                                                    }
                                                });
                                            });
                                        </script>
                                        <div class="table-responsive">
                                            <table id="tableInventory" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Name</th>
                                                        <th>Job</th>
                                                        <th>Description</th>
                                                        <th>Start</th>
                                                        <th>End (Estimate)</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $no = 1;
                                                    @endphp
                                                    @foreach ($tasklists as $task)
                                                        <tr>
                                                            <td>{{ $no++ }}</td>
                                                            <td>{{ $task->name }}</td>
                                                            <td>{{ $task->job }}</td>
                                                            <td>{{ $task->description }}</td>
                                                            <td>{{ \Carbon\Carbon::parse($task->start_date)->format('d/m/Y') }}</td>
                                                            <td>{{ \Carbon\Carbon::parse($task->end_date)->format('d/m/Y') }}</td>
                                                            <td>
                                                                @if ($task->status == 'Open')
                                                                    <span class="badge bg-warning text-dark">Open</span> <!-- Yellow badge for 'Open' -->
                                                                @else
                                                                    <span class="badge bg-success">Close</span> <!-- Gray badge for 'Close' -->
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editTasklistModal{{ $task->id }}">
                                                                    Edit
                                                                </button>
                                                                <form action="{{ route('tasklist.destroy', $task->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                                </form>

                                                            </td>
                                                        </tr>
                                                            <!-- Modal Edit -->
                                                        <div class="modal fade" id="editTasklistModal{{ $task->id }}" tabindex="-1" aria-labelledby="editTasklistModalLabel{{ $task->id }}" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="editTasklistModalLabel{{ $task->id }}">Edit Tasklist</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <form action="{{ route('tasklist.update', $task->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <div class="modal-body">
                                                                            <div class="mb-3">
                                                                                <label for="name" class="form-label">Name</label>
                                                                                <input type="text" class="form-control" id="name" name="name" value="{{ $task->name }}" required>
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label for="job" class="form-label">Job</label>
                                                                                <select class="form-control" id="job" name="job" required>
                                                                                    <option value="PM List" {{ $task->job == 'PM List' ? 'selected' : '' }}>PM List</option>
                                                                                    <option value="MTC Order List" {{ $task->job == 'MTC Order List' ? 'selected' : '' }}>MTC Order List</option>
                                                                                    <option value="Other" {{ $task->job == 'Other' ? 'selected' : '' }}>Other</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label for="description" class="form-label">Description</label>
                                                                                <textarea class="form-control" id="description" name="description" rows="3" required>{{ $task->description }}</textarea>
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label for="start_date" class="form-label">Start Time</label>
                                                                                <input type="datetime-local" class="form-control" id="start_date" name="start_date" value="{{ \Carbon\Carbon::parse($task->start_date)->format('Y-m-d\TH:i') }}" required>
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label for="end_date" class="form-label">End Time</label>
                                                                                <input type="datetime-local" class="form-control" id="end_date" name="end_date" value="{{ \Carbon\Carbon::parse($task->end_date)->format('Y-m-d\TH:i') }}" required>
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label for="status" class="form-label">Status</label>
                                                                                <select class="form-control" id="status" name="status" required>
                                                                                    <option value="Open" {{ $task->status == 'Open' ? 'selected' : '' }}>Open</option>
                                                                                    <option value="Close" {{ $task->status == 'Close' ? 'selected' : '' }}>Close</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
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
</main>
<!-- For Datatables -->
<script>
    $(document).ready(function() {
        var table = $("#tableInventory").DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
        });
    });
</script>

@endsection
