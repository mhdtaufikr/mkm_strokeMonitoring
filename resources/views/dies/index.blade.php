@extends('layouts.master')

@section('content')

<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-fluid px-4 mt-n10">
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">


                            <!-- /.card -->

                            <div class="card mt-4">
                                <div class="card-header">
                                    <h3 class="card-title">Checksheet</h3>
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
                                            <form action="{{ url('/checksheet/scan') }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="form-group mb-4">
                                                        <select name="mechine" id="machineSelect" class="form-control chosen-select" data-placeholder="Choose a dies...">
                                                            <!-- Options will be populated dynamically using PHP -->
                                                        </select>
                                                    </div>
                                                    <div class="d-flex justify-content-center">
                                                        <div id="qr-reader" style="width:500px"></div>
                                                        <div id="qr-reader-results"></div>
                                                    </div>
                                                    <div class="d-flex justify-content-center mt-3">
                                                        <input readonly type="text" name="no_mechine" id="qr-value" class="form-control" placeholder="Scanned QR Code Value">
                                                    </div>
                                                </div>
                                                <div class="modal-footer d-flex justify-content-center">
                                                    <button id="submitBtn" type="submit" class="btn btn-primary">Submit</button>
                                                </div>

                                            </form>

                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <br>
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Summary Report</h3>
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
                                            <table id="tablepm" class="table table-bordered table-striped mt-3">
                                                <thead>
                                                    <tr>
                                                        <th>Dies</th>
                                                        <th>Date</th>
                                                        <th>PIC</th>
                                                        <th>Remarks</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($pm as $pmRecord)
                                                        <tr>
                                                            <td>{{ $pmRecord->MstStrokeDies->code }} - {{ $pmRecord->MstStrokeDies->process }}</td>
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
                                            <table id="tableRepair" class="table table-bordered table-striped mt-3">
                                                <thead>
                                                    <tr>
                                                        <th>Dies</th>
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
                                                            @if ($repairRecord->MstStrokeDies)
                                                                <td>{{ $repairRecord->MstStrokeDies->code }} - {{ $repairRecord->MstStrokeDies->process }}</td>
                                                            @else
                                                                <td>Data not available</td>
                                                            @endif
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
                            <!-- /.card -->

                            {{-- <div class="card mt-4">
                                <div class="card-header">
                                    <h3 class="card-title">List Checksheet</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="mb-3 col-sm-12">
                                            <div class="table-responsive">
                                                <table id="tableUser" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Dies</th>
                                                            <th>Date</th>
                                                            <th>PIC</th>
                                                            <th>Action</th>
                                                            <th>PDF</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                        $no = 1;
                                                        @endphp
                                                        @foreach ($item as $data)
                                                        <tr>
                                                            <td>{{ $no++ }}</td>
                                                            <td>{{$data->MstStrokeDies->code}} - {{$data->MstStrokeDies->process}} ({{$data->MstStrokeDies->part_name}})</td>
                                                            <td>{{ date('d/m/Y', strtotime($data->date)) }}</td>
                                                            <td>{{$data->pic}}</td>
                                                            <td>
                                                                <a href="{{ url('/apar/detail/'.encrypt($data->id)) }}" class="btn btn-primary btn-sm" title="Detail">Detail
                                                                </a>
                                                            </td>
                                                            <td>
                                                                <a href="{{url('apar/generate-pdf/'.encrypt($data->id))}}" class="btn btn-success btn-sm" title="Generate PDF">
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
                                </div>
                            </div> --}}
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
    </div>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script>
    function docReady(fn) {
        if (document.readyState === "complete" || document.readyState === "interactive") {
            setTimeout(fn, 1);
        } else {
            document.addEventListener("DOMContentLoaded", fn);
        }
    }

    docReady(function () {
        var resultContainer = document.getElementById('qr-reader-results');
        var inputField = document.getElementById('qr-value');
        var lastResult, countResults = 0;

        function onScanSuccess(decodedText, decodedResult) {
            if (decodedText !== lastResult) {
                console.log(`Decoded text: ${decodedText}`);
                lastResult = decodedText;
                inputField.value = decodedText;
            }
        }

        var html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
    });
</script>

<script>
  $(document).ready(function() {
    var table = $("#tableUser").DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false,
      // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    });
  });
</script>

<script>
    // Populate options dynamically from PHP variable with both no_apar and location
    var machines = <?php echo json_encode($dies); ?>;

    // Function to populate select options
    function populateOptions() {
        var select = $('#machineSelect');
        select.empty();
        select.append('<option></option>'); // Add an empty option
        machines.forEach(function(machine) {

            // Format each option as "type - no_apar - location - groupLabel"
            select.append('<option value="' + machine.asset_no + '">' + machine.code + ' ( ' + machine.process + ' ) ' + '</option>');
        });
        // Initialize Chosen plugin
        select.chosen();
    }

    // Call the function to populate options on page load
    $(document).ready(function() {
        populateOptions();
    });
</script>

@endsection
