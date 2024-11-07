@extends('layouts.master')

@section('content')
<main>
    <!-- Page header -->
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-5">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4 d-flex justify-content-between">

            </div>
        </div>
    </header>

    <!-- Main page content-->
    <div class="container-fluid px-4 mt-n10">
        <div class="content-wrapper">
            <section class="content-header"></section>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between mt-4">
                                <!-- Buttons for Preventive Maintenance, Repair, and BOM Die -->
                                <a href="{{ url('dies/pm/' . encrypt($data->id)) }}" class="btn btn-primary">Preventive Maintenance</a>
                                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#bomModal">
                                    BOM Die
                                </button>
                                <a href="{{ url('dies/repair/' . encrypt($data->id)) }}" class="btn btn-secondary">Repair</a>
                            </div>
                            <!-- BOM Die Modal -->
                          <!-- Modal for Adding BOM Die Entries -->
<!-- BOM Die Modal -->
<div class="modal fade" id="bomModal" tabindex="-1" aria-labelledby="bomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bomModalLabel">BOM Die Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($bom->isEmpty())
                    <p>No BOM Die data available.</p>
                @else
                    <table id="tableBomDie" class="table table-bordered mt-3 mb-3">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Size</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bom as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->size }}</td>
                                    <td>{{ $item->qty }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif

                <!-- Form to Add New BOM Data -->
                <form id="bomForm" action="{{ route('bom.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_dies" value="{{ $data->id }}">

                    <div id="bomItems">
                        <div class="row mb-2">
                            <div class="col">
                                <input type="text" name="items[0][name]" class="form-control" placeholder="Name" required>
                            </div>
                            <div class="col">
                                <input type="text" name="items[0][size]" class="form-control" placeholder="Size">
                            </div>
                            <div class="col">
                                <input type="number" name="items[0][qty]" class="form-control" placeholder="Quantity" required>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-danger remove-item">Remove</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" id="addBomItem">Add Item</button>
                    <button type="submit" class="btn btn-success">Save BOM Items</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let itemCount = 1;

        document.getElementById("addBomItem").addEventListener("click", function() {
            const newItem = document.createElement("div");
            newItem.classList.add("row", "mb-2");
            newItem.innerHTML = `
                <div class="col">
                    <input type="text" name="items[${itemCount}][name]" class="form-control" placeholder="Name" required>
                </div>
                <div class="col">
                    <input type="text" name="items[${itemCount}][size]" class="form-control" placeholder="Size">
                </div>
                <div class="col">
                    <input type="number" name="items[${itemCount}][qty]" class="form-control" placeholder="Quantity" required>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-danger remove-item">Remove</button>
                </div>
            `;
            document.getElementById("bomItems").appendChild(newItem);
            itemCount++;
        });

        document.getElementById("bomItems").addEventListener("click", function(e) {
            if (e.target.classList.contains("remove-item")) {
                e.target.closest(".row").remove();
            }
        });
    });
    </script>




                            <!-- Die Details Card -->
                            <div class="card mt-4 mb-4">
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
                                <div class="card-header">
                                    <h3 class="card-title">Detail Dies</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Left Column -->
                                        <div class="col-md-6">
                                            <table class="table table-bordered">
                                                <tr><th>Asset No</th><td>{{ $data['asset_no'] }}</td></tr>
                                                <tr><th>Part Name</th><td>{{ $data['part_name'] }}</td></tr>
                                                <tr><th>Code</th><td>{{ $data['code'] }}</td></tr>
                                                <tr><th>Part No</th><td>{{ $data['part_no'] }}</td></tr>
                                                <tr><th>Process</th><td>{{ $data['process'] }}</td></tr>
                                                <tr><th>Standard Stroke</th><td>{{ $data['std_stroke'] }}</td></tr>
                                                <tr><th>Current Quantity</th><td>{{ $data['current_qty'] }}</td></tr>
                                                <tr>
                                                    <th>
                                                        <!-- Button to trigger image modal -->
                                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#imageModal">
                                                            Manage Images
                                                        </button>
                                                    </th>
                                                    <td>
                                                        <!-- Bootstrap Carousel for showing images -->
                                                        @if(!empty($data->img) && is_array($images = json_decode($data->img, true)))
                                                        <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
                                                            <div class="carousel-inner">
                                                                @foreach($images as $index => $image)
                                                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                                        <img src="{{ asset(str_replace('\\', '/', $image)) }}" class="d-block w-100" alt="Image {{ $index + 1 }}" style="height: 200px; object-fit: cover;">
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <!-- Controls for next and previous slides -->
                                                            <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                                <span class="visually-hidden">Previous</span>
                                                            </button>
                                                            <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                                <span class="visually-hidden">Next</span>
                                                            </button>
                                                        </div>
                                                        @else
                                                        <p>No images available</p>
                                                        @endif

                                                    </td>
                                                </tr>

                                            </table>
                                        </div>
                                        <!-- Right Column -->
                                        <div class="col-md-6">
                                            <table class="table table-bordered">
                                                <tr><th>Cutoff Date</th><td>{{ $data['cutoff_date'] }}</td></tr>
                                                <tr><th>Classification</th><td>{{ $data['classification'] }}</td></tr>
                                                <tr><th>Status</th><td>{{ $data['status'] }}</td></tr>
                                                <tr><th>Height</th><td>{{ $data['height'] }}</td></tr>
                                                <tr><th>Width</th><td>{{ $data['width'] }}</td></tr>
                                                <tr><th>Length</th><td>{{ $data['length'] }}</td></tr>
                                                <tr><th>Weight</th><td>{{ $data['weight'] }}</td></tr>
                                                <tr><th>Remarks</th><td>{{ $data['remarks'] }}</td></tr>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- Edit Button to Trigger Modal -->
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal">
                                        <i class="fas fa-edit"></i> Edit Data
                                    </button>
                                    <!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Asset Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('asset.update', $data['id']) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="asset_no" class="form-label">Asset No</label>
                                <input type="text" class="form-control" id="asset_no" name="asset_no" value="{{ $data['asset_no'] }}" >
                            </div>
                            <div class="mb-3">
                                <label for="part_name" class="form-label">Part Name</label>
                                <input type="text" class="form-control" id="part_name" name="part_name" value="{{ $data['part_name'] }}" >
                            </div>
                            <div class="mb-3">
                                <label for="code" class="form-label">Code</label>
                                <input type="text" class="form-control" id="code" name="code" value="{{ $data['code'] }}" >
                            </div>
                            <div class="mb-3">
                                <label for="part_no" class="form-label">Part No</label>
                                <input type="text" class="form-control" id="part_no" name="part_no" value="{{ $data['part_no'] }}" >
                            </div>
                            <div class="mb-3">
                                <label for="process" class="form-label">Process</label>
                                <input type="text" class="form-control" id="process" name="process" value="{{ $data['process'] }}" >
                            </div>
                            <div class="mb-3">
                                <label for="std_stroke" class="form-label">Standard Stroke</label>
                                <input type="number" class="form-control" id="std_stroke" name="std_stroke" value="{{ $data['std_stroke'] }}" >
                            </div>
                            <div class="mb-3">
                                <label for="current_qty" class="form-label">Current Quantity</label>
                                <input type="number" class="form-control" id="current_qty" name="current_qty" value="{{ $data['current_qty'] }}" >
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cutoff_date" class="form-label">Cutoff Date</label>
                                <input type="date" class="form-control" id="cutoff_date" name="cutoff_date" value="{{ $data['cutoff_date'] }}" >
                            </div>
                            <div class="mb-3">
                                <label for="classification" class="form-label">Classification</label>
                                <input type="text" class="form-control" id="classification" name="classification" value="{{ $data['classification'] }}" >
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <input type="text" class="form-control" id="status" name="status" value="{{ $data['status'] }}" >
                            </div>
                            <div class="mb-3">
                                <label for="height" class="form-label">Height</label>
                                <input type="number" class="form-control" id="height" name="height" value="{{ $data['height'] }}" >
                            </div>
                            <div class="mb-3">
                                <label for="width" class="form-label">Width</label>
                                <input type="number" class="form-control" id="width" name="width" value="{{ $data['width'] }}" >
                            </div>
                            <div class="mb-3">
                                <label for="length" class="form-label">Length</label>
                                <input type="number" class="form-control" id="length" name="length" value="{{ $data['length'] }}" >
                            </div>
                            <div class="mb-3">
                                <label for="weight" class="form-label">Weight</label>
                                <input type="number" class="form-control" id="weight" name="weight" value="{{ $data['weight'] }}" >
                            </div>
                            <div class="mb-3">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="3">{{ $data['remarks'] }}</textarea>
                            </div>
                        </div>
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
                                </div>
                            </div>

                            <!-- Modal for Image Management -->
                            <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="imageModalLabel">Manage Images</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Form to add new images -->
                                            <div class="mb-3">
                                                <form action="{{ url('/dies/add/image') }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $data->id }}">
                                                    <h5>Add New Images</h5>
                                                    <div class="input-group">
                                                        <input type="file" class="form-control" name="new_images[]" multiple>
                                                        <button class="btn btn-primary" type="submit">Upload</button>
                                                    </div>
                                                </form>
                                            </div>

                                            <div class="row">
                                                @foreach(($data->images ?? []) as $key => $imagePath)
                                                    <div class="col-md-4 mb-3">
                                                        <div class="card">
                                                            <img src="{{ asset($imagePath) }}" class="card-img-top" alt="Image {{ $key + 1 }}" style="height: 200px; width: auto;">
                                                            <div class="card-body">
                                                                <!-- Form to delete an image -->
                                                                <form action="{{ url('/dies/delete/image') }}" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="img_path" value="{{ $imagePath }}">
                                                                    <input type="hidden" name="id" value="{{ $data->id }}">
                                                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{$data->code}} - {{$data->process}}</h3>
                                </div>
                                <div class="card-body">
                                    <!-- Tabs navigation -->
                                    <ul class="nav nav-tabs" id="pmRepairTabs" role="tablist">
                                        <li style="background-color: rgba(90, 19, 10, 0.8);" class="nav-item" role="presentation">
                                            <a class="nav-link active" id="pm-tab" data-bs-toggle="tab" href="#pm" role="tab" aria-controls="pm" aria-selected="true">Preventive Maintenance</a>
                                        </li>
                                        <li style="background-color: rgba(90, 19, 10, 0.8);" class="nav-item" role="presentation">
                                            <a class="nav-link" id="repair-tab" data-bs-toggle="tab" href="#repair" role="tab" aria-controls="repair" aria-selected="false">Repairs</a>
                                        </li>
                                    </ul>

                                    <!-- Tab content -->
                                    <div class="tab-content" id="pmRepairTabsContent">
                                        <!-- PM Form Heads Tab -->
                                        <div class="tab-pane fade show active" id="pm" role="tabpanel" aria-labelledby="pm-tab">
                                            <table id="tablepm" class="table table-bordered mt-3">
                                                <thead>
                                                    <tr>

                                                        <th>Date</th>
                                                        <th>PIC</th>
                                                        <th>Remarks</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($pm as $pmRecord)
                                                        <tr>

                                                            <td>{{ $pmRecord->date }}</td>
                                                            <td>{{ $pmRecord->pic }}</td>
                                                            <td>{{ $pmRecord->remarks }}</td>
                                                            <td>
                                                                <a href="{{ url('/pm/detail/'.encrypt($pmRecord->id)) }}" class="btn btn-primary btn-sm" title="Detail">Detail
                                                                </a>
                                                                <a href="{{url('pm/generate-pdf/'.encrypt($pmRecord->id))}}" class="btn btn-success btn-sm" title="Generate PDF">
                                                                    <i class="fas fa-file-pdf"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Repair Tab -->
                                        <div class="tab-pane fade" id="repair" role="tabpanel" aria-labelledby="repair-tab">
                                            <table id="tableRepair" class="table table-bordered mt-3">
                                                <thead>
                                                    <tr>

                                                        <th>Date</th>
                                                        <th>PIC</th>
                                                        <th>Problem</th>
                                                        <th>Action</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($repair as $repairRecord)
                                                        <tr>

                                                            <td>{{ $repairRecord->date }}</td>
                                                            <td>{{ $repairRecord->pic }}</td>
                                                            <td>{{ $repairRecord->problem }}</td>
                                                            <td>{{ $repairRecord->action }}</td>
                                                            <td>{{ $repairRecord->status }}</td>
                                                            <td>
                                                               <!-- Modal Trigger Button -->
                                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#repairDetailModal"
                                                                    data-date="{{ $repairRecord->date }}"
                                                                    data-pic="{{ $repairRecord->pic }}"
                                                                    data-problem="{{ $repairRecord->problem }}"
                                                                    data-action="{{ $repairRecord->action }}"
                                                                    data-status="{{ $repairRecord->status }}"
                                                                    data-start-time="{{ $repairRecord->start_time }}"
                                                                    data-end-time="{{ $repairRecord->end_time }}"
                                                                    data-remarks="{{ $repairRecord->remarks }}"
                                                                    data-signature="{{ $repairRecord->signature }}"
                                                                    data-img-before="{{ asset($repairRecord->img_before) }}"
                                                                    data-img-after="{{ asset($repairRecord->img_after) }}">
                                                                Detail
                                                            </button>
                                                                <a href="{{url('repair/generate-pdf/'.encrypt($repairRecord->id))}}" class="btn btn-success btn-sm" title="Generate PDF">
                                                                    <i class="fas fa-file-pdf"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
<!-- Repair Detail Modal -->
<div class="modal fade" id="repairDetailModal" tabindex="-1" aria-labelledby="repairDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="repairDetailModalLabel">Repair Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Date:</strong> <span id="modalDate"></span></p>
                        <p><strong>PIC:</strong> <span id="modalPIC"></span></p>
                        <p><strong>Problem:</strong> <span id="modalProblem"></span></p>
                        <p><strong>Action:</strong> <span id="modalAction"></span></p>
                        <p><strong>Status:</strong> <span id="modalStatus"></span></p>
                        <p><strong>Image After Repair:</strong><br><img id="modalImgAfter" src="" alt="After Image" width="50%"></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Start Time:</strong> <span id="modalStartTime"></span></p>
                        <p><strong>End Time:</strong> <span id="modalEndTime"></span></p>
                        <p><strong>Remarks:</strong> <span id="modalRemarks"></span></p>
                        <p><strong>Signature:</strong><br><img id="modalSignature" src="" alt="Signature" width="100"></p>
                        <p><strong>Image Before Repair:</strong><br><img id="modalImgBefore" src="" alt="Before Image" width="50%"></p>

                    </div>
                </div>
            </div>
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
<script>
    var repairDetailModal = document.getElementById('repairDetailModal');
    repairDetailModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;

        // Retrieve the data attributes from the button
        document.getElementById('modalDate').textContent = formatDate(button.getAttribute('data-date'));

        function formatDate(dateString) {
            if (dateString) {
                const [year, month, day] = dateString.split('-'); // Assuming date is in "Y-m-d" format
                return `${day}/${month}/${year}`;
            }
            return '';
        }

        document.getElementById('modalPIC').textContent = button.getAttribute('data-pic');
        document.getElementById('modalProblem').textContent = button.getAttribute('data-problem');
        document.getElementById('modalAction').textContent = button.getAttribute('data-action');
        document.getElementById('modalStatus').textContent = button.getAttribute('data-status');
        document.getElementById('modalStartTime').textContent = formatTime(button.getAttribute('data-start-time'));
        document.getElementById('modalEndTime').textContent = formatTime(button.getAttribute('data-end-time'));

        function formatTime(dateTime) {
            if (dateTime) {
                const timePart = dateTime.split(' ')[1]; // Extract "HH:MM:SS" part
                return timePart ? timePart.slice(0, 5) : ''; // Get only "HH:MM"
            }
            return '';
        }

        document.getElementById('modalRemarks').textContent = button.getAttribute('data-remarks');
        document.getElementById('modalSignature').src = button.getAttribute('data-signature');
        document.getElementById('modalImgBefore').src = button.getAttribute('data-img-before');
        document.getElementById('modalImgAfter').src = button.getAttribute('data-img-after');
    });
</script>

<script>
    $(document).ready(function() {
      var table = $("#tablepm").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      });
    });
  </script>
  <script>
    $(document).ready(function() {
      var table = $("#tableRepair").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      });
    });
  </script>
    <script>
        $(document).ready(function() {
          var table = $("#tableBomDie").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
          });
        });
      </script>
@endsection
