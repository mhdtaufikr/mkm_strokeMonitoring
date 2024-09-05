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

    #chartdiv {
        width: 100%;
        height: 500px;
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
                        <!-- Chart Container -->
                        <div id="chartdiv"></div>

                        <!-- Table Container -->
                        <table class="table table-bordered table-striped">
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
                                            @if($item->total_actual_production > $item->standard_stroke)
                                            <form method="POST" action="{{ route('reset.qty', ['strokeId' => $item->stroke_id]) }}">
                                                @csrf
                                                <button type="submit" class="btn-reset">Reset</button>
                                            </form>
                                        @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <script>
        var myDate = new Date();
        var hrs = myDate.getHours();

        var greet;

        if (hrs < 12)
            greet = 'Good Morning';
        else if (hrs >= 12 && hrs <= 17)
            greet = 'Good Afternoon';
        else if (hrs >= 17 && hrs <= 24)
            greet = 'Good Evening';

        document.getElementById('lblGreetings').innerHTML =
            '<b>' + greet + '</b> and welcome to MKM Dies Smart System';

        // Prepare chart data
        var chartData = @json($data).map(function(item) {
            return {
                stroke_code: item.stroke_code + ' - ' + item.stroke_process,
                total_actual_production: item.total_actual_production,
                standard_stroke: item.standard_stroke,
                exceed: item.total_actual_production > item.standard_stroke // Determine if actual production exceeds the standard
            };
        });

        am5.ready(function() {

            // Create root element
            var root = am5.Root.new("chartdiv");

            // Set themes
            root.setThemes([
                am5themes_Animated.new(root)
            ]);

            // Create chart
            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: false,
                panY: false,
                wheelX: "panX",
                wheelY: "zoomX",
                paddingLeft: 0,
                layout: root.verticalLayout
            }));

            // Create Y-axis (Category Axis)
            var yRenderer = am5xy.AxisRendererY.new(root, {
                cellStartLocation: 0.1,
                cellEndLocation: 0.9,
                minorGridEnabled: true
            });
            yRenderer.grid.template.set("location", 1);

            var yAxis = chart.yAxes.push(
                am5xy.CategoryAxis.new(root, {
                    categoryField: "stroke_code", // Now stroke_code includes both code and process
                    renderer: yRenderer,
                    tooltip: am5.Tooltip.new(root, {})
                })
            );
            yAxis.data.setAll(chartData);

            // Create X-axis (Value Axis)
            var xAxis = chart.xAxes.push(
                am5xy.ValueAxis.new(root, {
                    min: 0,
                    renderer: am5xy.AxisRendererX.new(root, {
                        strokeOpacity: 0.1,
                        minGridDistance: 70
                    })
                })
            );

            // Create Column Series for Actual Production
            var series1 = chart.series.push(am5xy.ColumnSeries.new(root, {
                name: "Actual Production",
                xAxis: xAxis,
                yAxis: yAxis,
                valueXField: "total_actual_production",
                categoryYField: "stroke_code", // Now stroke_code includes both code and process
                sequencedInterpolation: true,
                tooltip: am5.Tooltip.new(root, {
                    pointerOrientation: "horizontal",
                    labelText: "[bold]{name}[/]\n{categoryY}: {valueX}"
                })
            }));

            series1.columns.template.setAll({
                height: am5.percent(70),
                fill: am5.color(0x67b7dc) // Default color
            });

            // Change color to red if it exceeds the standard stroke
            series1.columns.template.adapters.add("fill", function(fill, target) {
                var dataItem = target.dataItem;
                if (dataItem && dataItem.dataContext.exceed) {
                    return am5.color(0xff0000); // Red color for exceeding standard
                }
                return fill;
            });

            // Create Line Series for Standard Stroke
            var series2 = chart.series.push(am5xy.LineSeries.new(root, {
                name: "Standard Stroke",
                xAxis: xAxis,
                yAxis: yAxis,
                valueXField: "standard_stroke",
                categoryYField: "stroke_code", // Now stroke_code includes both code and process
                sequencedInterpolation: true,
                tooltip: am5.Tooltip.new(root, {
                    pointerOrientation: "horizontal",
                    labelText: "[bold]{name}[/]\n{categoryY}: {valueX}"
                })
            }));

            series2.strokes.template.setAll({
                strokeWidth: 2,
            });

            series2.bullets.push(function () {
                return am5.Bullet.new(root, {
                    locationY: 0.5,
                    sprite: am5.Circle.new(root, {
                        radius: 5,
                        stroke: series2.get("stroke"),
                        strokeWidth: 2,
                        fill: root.interfaceColors.get("background")
                    })
                });
            });

            // Add legend
            var legend = chart.children.push(am5.Legend.new(root, {
                centerX: am5.p50,
                x: am5.p50
            }));
            legend.data.setAll(chart.series.values);

            // Add cursor
            var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                behavior: "zoomY"
            }));
            cursor.lineX.set("visible", false);

            series1.data.setAll(chartData);
            series2.data.setAll(chartData);

            // Animate on load
            series1.appear();
            series2.appear();
            chart.appear(1000, 100);

        }); // end am5.ready()
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
