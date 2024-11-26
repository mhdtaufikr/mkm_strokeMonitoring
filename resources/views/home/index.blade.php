@extends('layouts.master')

@section('content')
<style>
    #lblGreetings {
        font-size: 1rem; /* Adjust the base font size as needed */
    }

    @media only screen and (max-width: 600px) {
        #lblGreetings {
            font-size: 1rem; /* Adjust the font size for smaller screens */
        }
    }

    .page-header .page-header-content {
        padding-top: 0rem;
        padding-bottom: 1rem;
    }

    .chartdiv {
        width: 100%;
        height: 500px;
        margin-bottom: 50px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f4f4f4;
    }

    .btn-reset {
        background-color: #ff0000;
        color: white;
        padding: 5px 10px;
        border: none;
        cursor: pointer;
    }

    .btn-reset:hover {
        background-color: #cc0000;
    }
</style>

<!-- Load amCharts library -->
<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

<main>
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-fluid px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-2">
                        <h1 class="page-header-title">
                            <label id="lblGreetings"></label>
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main page content-->
    <section class="content">
        <div class="container-fluid">
            <div class="container-fluid px-4 mt-n10">
                <div class="card">
                    <div class="card-header">
                        <h1 style="color: white">Stroke Monitoring</h1>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <h1>Critical Work Dies</h1>
                                <!-- Critical Chart -->
                                <div id="criticalChart" style="width: 100%; height: 500px;"></div>
                                <script>
                                    // Prepare Critical Chart Data
                                    var criticalData = @json($criticalData).map(function(item) {
                                        return {
                                            stroke_code: item.stroke_code + ' - ' + item.stroke_process,
                                            total_actual_production: item.total_actual_production,
                                            standard_stroke: item.standard_stroke,
                                            reminder_stroke: item.reminder_stroke,
                                            exceed: item.total_actual_production > item.standard_stroke,
                                            reminder_exceed: item.total_actual_production > item.reminder_stroke
                                        };
                                    });

                                    // Create Critical Chart
                                    am5.ready(function() {
                                        var root = am5.Root.new("criticalChart");
                                        root.setThemes([am5themes_Animated.new(root)]);

                                        var chart = root.container.children.push(am5xy.XYChart.new(root, {
                                            panX: false,
                                            panY: false,
                                            wheelX: "panX",
                                            wheelY: "zoomX",
                                            paddingLeft: 0,
                                            layout: root.verticalLayout
                                        }));

                                        var yAxis = chart.yAxes.push(am5xy.CategoryAxis.new(root, {
                                            categoryField: "stroke_code",
                                            renderer: am5xy.AxisRendererY.new(root, { cellStartLocation: 0.1, cellEndLocation: 0.9, minorGridEnabled: true }),
                                            tooltip: am5.Tooltip.new(root, {})
                                        }));
                                        yAxis.data.setAll(criticalData);

                                        var xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, { min: 0, renderer: am5xy.AxisRendererX.new(root, { strokeOpacity: 0.1, minGridDistance: 70 }) }));

                                        var series1 = chart.series.push(am5xy.ColumnSeries.new(root, {
                                            name: "Actual Production",
                                            xAxis: xAxis,
                                            yAxis: yAxis,
                                            valueXField: "total_actual_production",
                                            categoryYField: "stroke_code",
                                            tooltip: am5.Tooltip.new(root, { labelText: "[bold]{name}[/]\n{categoryY}: {valueX}" })
                                        }));
                                        series1.columns.template.setAll({ height: am5.percent(70), fill: am5.color(0x67b7dc) });
                                        series1.columns.template.adapters.add("fill", function(fill, target) {
                                            var dataItem = target.dataItem;
                                            if (dataItem && dataItem.dataContext.exceed) {
                                                return am5.color(0xff0000); // Red if it exceeds standard stroke
                                            } else if (dataItem && dataItem.dataContext.reminder_exceed) {
                                                return am5.color(0xffff00); // Yellow if it exceeds reminder stroke
                                            }
                                            return fill;
                                        });

                                        // Add Standard Stroke Line Series
                                        var series2 = chart.series.push(am5xy.LineSeries.new(root, {
                                            name: "Standard Stroke",
                                            xAxis: xAxis,
                                            yAxis: yAxis,
                                            valueXField: "standard_stroke",
                                            categoryYField: "stroke_code",
                                            stroke: am5.color(0xff0000), // Red color for Standard Stroke line
                                            tooltip: am5.Tooltip.new(root, { labelText: "[bold]{name}[/]\n{categoryY}: {valueX}" })
                                        }));
                                        series2.strokes.template.setAll({ strokeWidth: 2 });
                                        series2.bullets.push(function() {
                                            return am5.Bullet.new(root, {
                                                locationY: 0.5,
                                                sprite: am5.Circle.new(root, {
                                                    radius: 5,
                                                    fill: root.interfaceColors.get("background"),
                                                    stroke: am5.color(0xff0000), // Red color for dots
                                                    strokeWidth: 2
                                                })
                                            });
                                        });

                                        var series3 = chart.series.push(am5xy.LineSeries.new(root, {
                                        name: "Reminder Stroke",
                                        xAxis: xAxis,
                                        yAxis: yAxis,
                                        valueXField: "reminder_stroke",
                                        categoryYField: "stroke_code",
                                        stroke: am5.color(0xffa500), // Orange color for Reminder Stroke line
                                        tooltip: am5.Tooltip.new(root, { labelText: "[bold]{name}[/]\n{categoryY}: {valueX}" })
                                    }));
                                    series3.strokes.template.setAll({ strokeWidth: 2 });
                                    series3.bullets.push(function() {
                                        return am5.Bullet.new(root, {
                                            locationY: 0.5,
                                            sprite: am5.Circle.new(root, {
                                                radius: 5,
                                                fill: root.interfaceColors.get("background"),
                                                stroke: am5.color(0xffa500), // Orange color for dots
                                                strokeWidth: 2
                                            })
                                        });
                                    });


                                        chart.set("cursor", am5xy.XYCursor.new(root, { behavior: "zoomY" }));
                                        series1.data.setAll(criticalData);
                                        series2.data.setAll(criticalData);
                                        series3.data.setAll(criticalData);
                                    });
                                </script>

                            </div>
                            <div class="col-md-5">
                                <h1>Hard Work Dies</h1>
                                <!-- Hard Work Chart -->
                                <div id="hardWorkChart" style="width: 100%; height: 500px;"></div>
                                <script>
                                    // Prepare Hard Work Chart Data
                                    var hardWorkData = @json($hardWorkData).map(function(item) {
                                        return {
                                            stroke_code: item.stroke_code + ' - ' + item.stroke_process,
                                            total_actual_production: item.total_actual_production,
                                            standard_stroke: item.standard_stroke,
                                            reminder_stroke: item.reminder_stroke,
                                            exceed: item.total_actual_production > item.standard_stroke,
                                            reminder_exceed: item.total_actual_production > item.reminder_stroke
                                        };
                                    });

                                    // Create Hard Work Chart
                                    am5.ready(function() {
                                        var root = am5.Root.new("hardWorkChart");
                                        root.setThemes([am5themes_Animated.new(root)]);

                                        var chart = root.container.children.push(am5xy.XYChart.new(root, {
                                            panX: false,
                                            panY: false,
                                            wheelX: "panX",
                                            wheelY: "zoomX",
                                            paddingLeft: 50,  // Increase padding to show Y-axis labels
                                            layout: root.verticalLayout
                                        }));

                                        var yAxis = chart.yAxes.push(am5xy.CategoryAxis.new(root, {
                                            categoryField: "stroke_code",
                                            renderer: am5xy.AxisRendererY.new(root, {
                                                cellStartLocation: 0.1,
                                                cellEndLocation: 0.9,
                                                minorGridEnabled: true,
                                                minGridDistance: 20,  // Adjusts spacing for Y-axis labels
                                                inversed: true         // Inverse order if needed
                                            }),
                                            tooltip: am5.Tooltip.new(root, {})
                                        }));
                                        yAxis.data.setAll(hardWorkData);

                                        var xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {
                                            min: 0,
                                            renderer: am5xy.AxisRendererX.new(root, {
                                                strokeOpacity: 0.1,
                                                minGridDistance: 70
                                            })
                                        }));

                                        var series1 = chart.series.push(am5xy.ColumnSeries.new(root, {
                                            name: "Actual Production",
                                            xAxis: xAxis,
                                            yAxis: yAxis,
                                            valueXField: "total_actual_production",
                                            categoryYField: "stroke_code",
                                            tooltip: am5.Tooltip.new(root, { labelText: "[bold]{name}[/]\n{categoryY}: {valueX}" })
                                        }));
                                        series1.columns.template.setAll({ height: am5.percent(70), fill: am5.color(0x67b7dc) });
                                        series1.columns.template.adapters.add("fill", function(fill, target) {
                                            var dataItem = target.dataItem;
                                            if (dataItem && dataItem.dataContext.exceed) {
                                                return am5.color(0xff0000); // Red if it exceeds standard stroke
                                            } else if (dataItem && dataItem.dataContext.reminder_exceed) {
                                                return am5.color(0xffff00); // Yellow if it exceeds reminder stroke
                                            }
                                            return fill;
                                        });

                                        // Add Standard Stroke Line Series
                                        var series2 = chart.series.push(am5xy.LineSeries.new(root, {
                                            name: "Standard Stroke",
                                            xAxis: xAxis,
                                            yAxis: yAxis,
                                            valueXField: "standard_stroke",
                                            categoryYField: "stroke_code",
                                            stroke: am5.color(0xff0000), // Red color for Standard Stroke line
                                            tooltip: am5.Tooltip.new(root, { labelText: "[bold]{name}[/]\n{categoryY}: {valueX}" })
                                        }));
                                        series2.strokes.template.setAll({ strokeWidth: 2 });
                                        series2.bullets.push(function() {
                                            return am5.Bullet.new(root, {
                                                locationY: 0.5,
                                                sprite: am5.Circle.new(root, {
                                                    radius: 5,
                                                    fill: root.interfaceColors.get("background"),
                                                    stroke: am5.color(0xff0000), // Red color for dots
                                                    strokeWidth: 2
                                                })
                                            });
                                        });

                                        var series3 = chart.series.push(am5xy.LineSeries.new(root, {
                                        name: "Reminder Stroke",
                                        xAxis: xAxis,
                                        yAxis: yAxis,
                                        valueXField: "reminder_stroke",
                                        categoryYField: "stroke_code",
                                        stroke: am5.color(0xffa500), // Orange color for Reminder Stroke line
                                        tooltip: am5.Tooltip.new(root, { labelText: "[bold]{name}[/]\n{categoryY}: {valueX}" })
                                    }));
                                    series3.strokes.template.setAll({ strokeWidth: 2 });
                                    series3.bullets.push(function() {
                                        return am5.Bullet.new(root, {
                                            locationY: 0.5,
                                            sprite: am5.Circle.new(root, {
                                                radius: 5,
                                                fill: root.interfaceColors.get("background"),
                                                stroke: am5.color(0xffa500), // Orange color for dots
                                                strokeWidth: 2
                                            })
                                        });
                                    });


                                        chart.set("cursor", am5xy.XYCursor.new(root, { behavior: "zoomY" }));
                                        series1.data.setAll(hardWorkData);
                                        series2.data.setAll(hardWorkData);
                                        series3.data.setAll(hardWorkData);
                                    });
                                </script>


                            </div>
                            <div class="col-md-4">
                            <!-- Normal Chart -->
                            <h1>Normal Work Dies</h1>
                            <div id="normalChart" class="chartdiv"></div>
                            </div>
                            <div class="col-md-6">
                                <h1>PM Dies</h1>
                                <!-- Table Container -->
                                <table id="tableRepair" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Stroke Code</th>
                                            <th>Stroke Process</th>
                                            <th>Inventory Part No</th>
                                            <th>Inventory Name</th>
                                            <th>Standard Stroke</th>
                                            <th>Total Actual Production</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data as $item)
                                            <tr>
                                                <td>{{ $item->stroke_code }}</td>
                                                <td>{{ $item->stroke_process }}</td>
                                                <td>{{ $item->inventory_part_no }}</td>
                                                <td>{{ $item->inventory_name }}</td>
                                                <td>{{ $item->standard_stroke }}</td>
                                                <td>{{ $item->total_actual_production }}</td>
                                                <td>
                                                    @php
                                                        $difference = $item->standard_stroke - $item->total_actual_production;
                                                    @endphp

                                                    @if($item->total_actual_production > $item->reminder_stroke)
                                                    <a href="{{ route('pm', ['id' => encrypt($item->stroke_id)]) }}" class="btn-reset" role="button">Reset</a>
                                                    @endif

                                                </td>


                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h1>Repair Dies</h1>
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
                                                <td>{{ $data->pic }}</td>
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

<script>
    // Prepare Normal Work Chart Data
var normalData = @json($normalData).map(function(item) {
    return {
        stroke_code: item.stroke_code + ' - ' + item.stroke_process,
        total_actual_production: item.total_actual_production,
        standard_stroke: item.standard_stroke,
        reminder_stroke: item.reminder_stroke,
        exceed: item.total_actual_production > item.standard_stroke,
        reminder_exceed: item.total_actual_production > item.reminder_stroke
    };
});

// Create Normal Work Chart
am5.ready(function() {
    var root = am5.Root.new("normalChart");
    root.setThemes([am5themes_Animated.new(root)]);

    var chart = root.container.children.push(am5xy.XYChart.new(root, {
        panX: false,
        panY: false,
        wheelX: "panX",
        wheelY: "zoomX",
        paddingLeft: 50,
        layout: root.verticalLayout
    }));

    // Y-Axis
    var yAxis = chart.yAxes.push(am5xy.CategoryAxis.new(root, {
        categoryField: "stroke_code",
        renderer: am5xy.AxisRendererY.new(root, {
            cellStartLocation: 0.1,
            cellEndLocation: 0.9,
            minorGridEnabled: true
        }),
        tooltip: am5.Tooltip.new(root, {})
    }));
    yAxis.data.setAll(normalData);

    // X-Axis
    var xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {
        min: 0,
        renderer: am5xy.AxisRendererX.new(root, {
            strokeOpacity: 0.1,
            minGridDistance: 70
        })
    }));

    // Column Series for Actual Production
    var series1 = chart.series.push(am5xy.ColumnSeries.new(root, {
        name: "Actual Production",
        xAxis: xAxis,
        yAxis: yAxis,
        valueXField: "total_actual_production",
        categoryYField: "stroke_code",
        tooltip: am5.Tooltip.new(root, { labelText: "[bold]{name}[/]\n{categoryY}: {valueX}" })
    }));

    series1.columns.template.setAll({ height: am5.percent(70), fill: am5.color(0x67b7dc) });
    series1.columns.template.adapters.add("fill", function(fill, target) {
        var dataItem = target.dataItem;
        if (dataItem && dataItem.dataContext.exceed) {
            return am5.color(0xff0000); // Red if exceeding standard stroke
        } else if (dataItem && dataItem.dataContext.reminder_exceed) {
            return am5.color(0xffff00); // Yellow if exceeding reminder stroke
        }
        return fill;
    });

    // Line Series for Standard Stroke
    var series2 = chart.series.push(am5xy.LineSeries.new(root, {
        name: "Standard Stroke",
        xAxis: xAxis,
        yAxis: yAxis,
        valueXField: "standard_stroke",
        categoryYField: "stroke_code",
        stroke: am5.color(0xff0000),
        tooltip: am5.Tooltip.new(root, { labelText: "[bold]{name}[/]\n{categoryY}: {valueX}" })
    }));

    series2.strokes.template.setAll({ strokeWidth: 2 });
    series2.bullets.push(function() {
        return am5.Bullet.new(root, {
            locationY: 0.5,
            sprite: am5.Circle.new(root, {
                radius: 5,
                fill: root.interfaceColors.get("background"),
                stroke: am5.color(0xff0000),
                strokeWidth: 2
            })
        });
    });

    // Line Series for Reminder Stroke
    var series3 = chart.series.push(am5xy.LineSeries.new(root, {
        name: "Reminder Stroke",
        xAxis: xAxis,
        yAxis: yAxis,
        valueXField: "reminder_stroke",
        categoryYField: "stroke_code",
        stroke: am5.color(0xffa500),
        tooltip: am5.Tooltip.new(root, { labelText: "[bold]{name}[/]\n{categoryY}: {valueX}" })
    }));

    series3.strokes.template.setAll({ strokeWidth: 2 });
    series3.bullets.push(function() {
        return am5.Bullet.new(root, {
            locationY: 0.5,
            sprite: am5.Circle.new(root, {
                radius: 5,
                fill: root.interfaceColors.get("background"),
                stroke: am5.color(0xffa500),
                strokeWidth: 2
            })
        });
    });

    // Cursor
    chart.set("cursor", am5xy.XYCursor.new(root, { behavior: "zoomY" }));

    // Set Data
    series1.data.setAll(normalData);
    series2.data.setAll(normalData);
    series3.data.setAll(normalData);
});

</script>





                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        var greet;

        var myDate = new Date();
        var hrs = myDate.getHours();

        if (hrs < 12)
            greet = 'Good Morning';
        else if (hrs >= 12 && hrs <= 17)
            greet = 'Good Afternoon';
        else
            greet = 'Good Evening';

        document.getElementById('lblGreetings').innerHTML =
            '<b>' + greet + '</b> and welcome to MKM Dies Smart System';
    </script>

<script>
    $(document).ready(function() {
        var table = $("#tableInventory").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
        });
    });
</script>
<script>
    $(document).ready(function() {
        var table = $("#tableRepair").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
        });
    });
</script>
    <script>
        function refreshPage() {
            setTimeout(function() {
                location.reload();
            }, 200000); // 300000 milliseconds = 5 minutes
        }

        // Call the function when the page loads
        refreshPage();
    </script>
</main>
@endsection
