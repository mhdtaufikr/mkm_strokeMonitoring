@extends('layouts.master')

@section('content')
<main>
    <!-- Page header -->
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-0">
                {{-- <h2 class="text-white">DIE Maintenance Checklist</h2> --}}
            </div>
        </div>
    </header>

    <!-- Main page content -->
    <div class="container-fluid px-4 mt-n10">
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
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
                        <div class="mb-3 col-sm-12">
                            <form id="checksheetForm" action="{{ url('/checksheet/store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input hidden type="text" name="id" id="" value="{{$id}}">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h3 class="card-title">DIE Maintenance Checklist</h3>
                                        <!-- Trigger Modal on Submit -->
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Submit</button>
                                    </div>

                                    <div class="card-body">
                                        <!-- Checklist Legend -->
                                        <div class="bg-white p-3 border rounded shadow mb-3">
                                            <span>OK: Good</span> | <span>NG: Not Good</span>
                                        </div>

                                        <!-- DIE Component Section -->
                                        @foreach (['DIE COMPONENT' => $dieComponent, 'DIE TOOLS' => $dieTools, 'DIE CASTING & PLATE' => $dieCast] as $category => $items)
                                            <h5 class="mt-4">{{ $category }}</h5>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Description</th>
                                                            <th>Spec</th>
                                                            <th>OK</th>
                                                            <th>NG</th>
                                                            <th>Remarks</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($items as $item)
                                                            <input type="hidden" name="items[{{ $item->name_value }}]" value="{{ $item->name_value }}">
                                                            <tr>
                                                                <td>{{ $item->name_value }}</td>
                                                                <td>{{ $item->code_format }}</td>
                                                                <td><input type="checkbox" name="items[{{ $item->name_value }}][OK]" value="1" class="checkbox"></td>
                                                                <td><input type="checkbox" name="items[{{ $item->name_value }}][NG]" value="1" class="checkbox"></td>
                                                                <td><input type="text" name="items[{{ $item->name_value }}][remarks]" class="form-control"></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <!-- Modal for input details -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Input Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="pic" class="form-label">User</label>
                    <input type="text" class="form-control" id="pic" name="pic" value="{{ old('pic') }}">
                </div>
                <div class="mb-3">
                    <label for="remarks" class="form-label">Remarks</label>
                    <textarea class="form-control" id="remarks" name="remarks">{{ old('remarks') }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="img" class="form-label">Image</label>
                    <input type="file" class="form-control" id="img" name="img">
                </div>
                <div class="mb-3">
                    <label for="signature" class="form-label">Signature</label>
                    <canvas id="signature-pad" class="signature-pad" width="400" height="200" style="border: 1px solid #000;"></canvas>
                    <input type="hidden" id="signature-data" name="signature">
                    <button type="button" class="btn btn-danger" id="clear-signature">Clear</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="oneClickButton">Save changes</button>
            </div>
        </div>
    </div>
</div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>



<!-- JavaScript for datatables, signature pad, and checkbox logic -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/2.3.2/signature_pad.min.js"></script>
<script>
    $(document).ready(function () {
        $("#tableUser").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false
        });
    });

    // Uncheck other checkboxes in the same row when one is selected
    document.addEventListener('DOMContentLoaded', function () {
        const checkboxes = document.querySelectorAll('.checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                if (this.checked) {
                    const row = this.parentElement.parentElement;
                    row.querySelectorAll('.checkbox').forEach(otherCheckbox => {
                        if (otherCheckbox !== this) otherCheckbox.checked = false;
                    });
                }
            });
        });
    });

    // Signature Pad functionality
    document.addEventListener('DOMContentLoaded', function () {
        const signaturePad = new SignaturePad(document.getElementById('signature-pad'));
        const clearButton = document.getElementById('clear-signature');
        const signatureInput = document.getElementById('signature-data');

        clearButton.addEventListener('click', function () {
            signaturePad.clear();
        });

        document.getElementById('oneClickButton').addEventListener('click', function (event) {
            if (signaturePad.isEmpty()) {
                alert("Please provide a signature.");
                event.preventDefault();
            } else {
                const dataUrl = signaturePad.toDataURL('image/png');
                signatureInput.value = dataUrl;
                document.getElementById('checksheetForm').submit(); // Submit the main form
            }
        });
    });
</script>
@endsection
