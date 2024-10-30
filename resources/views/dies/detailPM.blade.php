@extends('layouts.master')

@section('content')
<main>
    <!-- Page header -->
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-0">
                <h2 class="text-white">Maintenance Details for Asset: {{ $data->code }} - {{ $data->process }}</h2>
            </div>
        </div>
    </header>

    <!-- Main page content -->
    <div class="container-fluid px-4 mt-n10">
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">
                            <h3>Maintenance Header Information</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr><th>PIC</th><td>{{ $pmHead->pic }}</td></tr>
                                <tr><th>Date</th><td>{{ $pmHead->date }}</td></tr>
                                <tr><th>Remarks</th><td>{{ $pmHead->remarks }}</td></tr>
                                <tr>
                                    <th>Signature</th>
                                    <td><img src="{{ $pmHead->signature }}" alt="Signature" width="100"></td>
                                </tr>
                                <tr>
                                    <th>Image</th>
                                    <td>
                                        @if($pmHead->img)
                                            <img src="{{ asset($pmHead->img) }}" alt="Image" width="100">
                                        @else
                                            <span>No image available</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <!-- Grouped Maintenance Details -->
                            <h4 class="mt-4">Maintenance Details</h4>
                            @foreach($pmDetail as $category => $details)
                                <h5 class="mt-3">{{ $category }}</h5>
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
                                        @foreach($details as $detail)
                                            <tr>
                                                <td>{{ $detail->item_check }}</td>
                                                <td>{{ $detail->dropdown->code_format ?? 'N/A' }}</td> <!-- Show spec -->
                                                <td>{{ $detail->OK ? 'V' : '' }}</td>
                                                <td>{{ $detail->NG ? 'V' : '' }}</td>
                                                <td>{{ $detail->remarks }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>
@endsection
