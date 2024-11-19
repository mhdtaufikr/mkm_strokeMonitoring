@extends('layouts.master')

@section('content')
<main>
    <!-- Page header -->
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-0">
                {{-- <h2 class="text-white">Die Repair Checksheet</h2> --}}
            </div>
        </div>
    </header>

    <!-- Main content -->
    <div class="container-fluid px-4 mt-n10">
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="mb-3 col-sm-12">
                            <form action="{{ url('dies/repair/store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id_dies" value="{{ $id }}">
                                <input type="hidden" name="id_order" value="{{ $id_req }}">
                                <div class="card">
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
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h3 class="card-title">Die Repair Checksheet {{$data->code}} - {{$data->process}}</h3>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>

                                    <div class="card-body">
                                        <!-- Repair Information split into two columns -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="pic" class="form-label">User (PIC)</label>
                                                            <input type="text" class="form-control" id="pic" name="pic" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="date" class="form-label">Date</label>
                                                            <input type="date" class="form-control" id="date" name="date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="problem" class="form-label">Problem Description</label>
                                                            <textarea class="form-control" id="problem" name="problem" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="action" class="form-label">Action Taken</label>
                                                            <textarea class="form-control" id="action" name="action" required></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label for="start_time" class="form-label">Start Time</label>
                                                            <input type="time" class="form-control" id="start_time" name="start_time" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label for="end_time" class="form-label">End Time</label>
                                                            <input type="time" class="form-control" id="end_time" name="end_time" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-3">
                                                            <label for="balance" class="form-label">Balance (Hours)</label>
                                                            <input type="text" class="form-control" id="balance" name="balance" disabled>
                                                        </div>
                                                    </div>
                                                </div>

                                                <script>
                                                    document.addEventListener("DOMContentLoaded", function () {
                                                        const startTimeInput = document.getElementById("start_time");
                                                        const endTimeInput = document.getElementById("end_time");
                                                        const balanceInput = document.getElementById("balance");

                                                        function calculateBalance() {
                                                            const startTime = startTimeInput.value;
                                                            const endTime = endTimeInput.value;

                                                            if (startTime && endTime) {
                                                                const [startHour, startMinute] = startTime.split(":").map(Number);
                                                                const [endHour, endMinute] = endTime.split(":").map(Number);

                                                                // Convert time to minutes
                                                                const startTotalMinutes = startHour * 60 + startMinute;
                                                                const endTotalMinutes = endHour * 60 + endMinute;

                                                                // Calculate difference in minutes
                                                                let diffMinutes;
                                                                if (endTotalMinutes >= startTotalMinutes) {
                                                                    diffMinutes = endTotalMinutes - startTotalMinutes;
                                                                } else {
                                                                    // Handle midnight case
                                                                    diffMinutes = 1440 - startTotalMinutes + endTotalMinutes;
                                                                }

                                                                // Convert minutes to hours and round to 2 decimal places
                                                                const diffHours = (diffMinutes / 60).toFixed(2);

                                                                balanceInput.value = diffHours;
                                                            }
                                                        }

                                                        startTimeInput.addEventListener("change", calculateBalance);
                                                        endTimeInput.addEventListener("change", calculateBalance);
                                                    });
                                                </script>

                                            </div>

                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="remarks" class="form-label">Remarks</label>
                                                            <input type="text" class="form-control" id="remarks" name="remarks">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="status" class="form-label">Status</label>
                                                            <select class="form-control" id="status" name="status" required>
                                                                <option value="OK">OK</option>
                                                                <option value="NG">NG</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="img_before" class="form-label">Image Before Repair</label>
                                                            <input type="file" class="form-control" id="img_before" name="img_before" accept="image/*">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="img_after" class="form-label">Image After Repair</label>
                                                            <input type="file" class="form-control" id="img_after" name="img_after" accept="image/*">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="signature" class="form-label">Signature</label>
                                                            <canvas id="signature-pad" class="signature-pad" width="380" height="100" style="border: 1px solid #000;"></canvas>
                                                            <input type="hidden" id="signature-data" name="signature">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 d-flex align-items-center">
                                                        <button type="button" class="btn btn-danger mt-3" id="clear-signature">Clear</button>
                                                    </div>
                                                </div>

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

<!-- Signature Pad JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/2.3.2/signature_pad.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const signaturePad = new SignaturePad(document.getElementById('signature-pad'));
        const clearButton = document.getElementById('clear-signature');
        const signatureInput = document.getElementById('signature-data');

        clearButton.addEventListener('click', function () {
            signaturePad.clear();
        });

        document.querySelector('form').addEventListener('submit', function (event) {
            if (signaturePad.isEmpty()) {
                alert("Please provide a signature.");
                event.preventDefault();
            } else {
                signatureInput.value = signaturePad.toDataURL('image/png');
            }
        });
    });
</script>
@endsection
