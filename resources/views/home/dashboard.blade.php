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
        height: 1000px;
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

                        <div class="row">
                            <!-- Card: Top 10 Most Repaired Dies -->
                            <div class="col-xl-6 col-md-6">
                                <div class="card">
                                    <div class="card-header" style="color: white;">
                                        Top 10 Most Repaired Dies
                                    </div>
                                    <div class="card-body">
                                        <div id="repairsChartDiv" style="width: 100%; height: 400px;"></div>
                                    </div>
                                </div>
                            </div>
                            <script>
                                document.addEventListener("DOMContentLoaded", function () {
                                    // Get the current month and year
                                    const now = new Date();
                                    const month = now.toLocaleString('default', { month: 'long' }); // Get the full month name
                                    const year = now.getFullYear();

                                    // Add the current month and year dynamically above the chart
                                    const chartContainer = document.getElementById("repairsChartDiv").parentElement;
                                    const header = document.createElement("h5");
                                    header.style.textAlign = "center";
                                    header.style.marginBottom = "10px";
                                    header.innerText = `Top 10 Most Repaired Dies - ${month} ${year}`;
                                    chartContainer.insertBefore(header, chartContainer.firstChild);

                                    // Data from the backend
                                    const repairsData = @json($topRepairs);

                                    // Prepare data with unique identifiers
                                    const chartData = repairsData.map(item => ({
                                        code: item.code, // Use code as X-axis
                                        total_repairs: parseInt(item.total_repairs) // Ensure repairs count is numeric
                                    }));

                                    // Chart: Top 10 Most Repaired Dies
                                    am5.ready(function () {
                                        let root = am5.Root.new("repairsChartDiv");

                                        root.setThemes([am5themes_Animated.new(root)]);

                                        // Create the main chart
                                        let chart = root.container.children.push(
                                            am5xy.XYChart.new(root, {
                                                panX: true,
                                                panY: true,
                                                wheelX: "panX",
                                                wheelY: "zoomX",
                                                pinchZoomX: true,
                                                layout: root.verticalLayout // Arrange elements vertically
                                            })
                                        );

                                        // Add X-axis (Code)
                                        let xAxis = chart.xAxes.push(
                                            am5xy.CategoryAxis.new(root, {
                                                categoryField: "code", // Use code for the X-axis
                                                renderer: am5xy.AxisRendererX.new(root, {
                                                    minGridDistance: 30,
                                                    labels: {
                                                        rotation: -45 // Rotate labels for better readability
                                                    }
                                                }),
                                                tooltip: am5.Tooltip.new(root, {
                                                    labelText: "{categoryX}" // Tooltip showing the code
                                                })
                                            })
                                        );

                                        // Add X-axis caption
                                        xAxis.children.moveValue(
                                            am5.Label.new(root, {
                                                text: "Code", // X-axis caption
                                                x: am5.p50,
                                                centerX: am5.p50,
                                                fontWeight: "bold"
                                            }),
                                            0
                                        );

                                        xAxis.data.setAll(chartData);

                                        // Add Y-axis (Problem Count)
                                        let yAxis = chart.yAxes.push(
                                            am5xy.ValueAxis.new(root, {
                                                renderer: am5xy.AxisRendererY.new(root, {})
                                            })
                                        );

                                        // Add Y-axis caption
                                        yAxis.children.moveValue(
                                            am5.Label.new(root, {
                                                text: "Problem Count", // Y-axis caption
                                                rotation: -90,
                                                y: am5.p50,
                                                centerX: am5.p50,
                                                fontWeight: "bold"
                                            }),
                                            0
                                        );

                                        // Create the column series
                                        let series = chart.series.push(
                                            am5xy.ColumnSeries.new(root, {
                                                name: "Repairs",
                                                xAxis: xAxis,
                                                yAxis: yAxis,
                                                valueYField: "total_repairs",
                                                categoryXField: "code", // Use code as the category
                                                tooltip: am5.Tooltip.new(root, {
                                                    labelText: "{categoryX}: {valueY} repairs"
                                                })
                                            })
                                        );

                                        series.columns.template.setAll({
                                            tooltipText: "[bold]{categoryX}[/]\nRepairs: {valueY}",
                                            width: am5.percent(80),
                                            strokeOpacity: 0
                                        });

                                        series.data.setAll(chartData);

                                        // Add cursor for interactivity
                                        chart.set("cursor", am5xy.XYCursor.new(root, {
                                            behavior: "zoomX"
                                        }));

                                        // Animate on load
                                        chart.appear(1000, 100);
                                        series.appear();
                                    });
                                });
                            </script>



                            <div class="col-xl-6 col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 style="color: white;">MTTR / Dies</h3>
                                    </div>
                                    <div class="card-body">
                                        <div id="mttrChartDiv" style="width: 100%; height: 400px;"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                // Get the current month and year
                                const now = new Date();
                                const month = now.toLocaleString('default', { month: 'long' }); // Get the full month name
                                const year = now.getFullYear();

                                // Add the current month and year dynamically above the chart
                                const chartContainer = document.getElementById("mttrChartDiv").parentElement;
                                const header = document.createElement("h5");
                                header.style.textAlign = "center";
                                header.style.marginBottom = "10px";
                                header.innerText = `MTTR / Dies - ${month} ${year}`;
                                chartContainer.insertBefore(header, chartContainer.firstChild);

                                // Data from the backend
                                const mttrData = @json($topMTTR);

                                // Prepare data for the chart
                                const chartData = mttrData.map((item) => ({
                                    code: item.code, // Use the 'code' field for the X-axis
                                    mttr_value: parseFloat(item.mttr_value), // Ensure MTTR value is numeric
                                    problem_count: parseInt(item.problem_count), // Problem count for tooltips
                                    total_repair_time: parseFloat(item.total_repair_time), // Repair time for tooltips
                                }));

                                // Chart: MTTR / Dies
                                am5.ready(function () {
                                    // Create the root element
                                    let root = am5.Root.new("mttrChartDiv");

                                    root.setThemes([am5themes_Animated.new(root)]);

                                    // Create the main chart
                                    let chart = root.container.children.push(
                                        am5xy.XYChart.new(root, {
                                            panX: true,
                                            panY: true,
                                            wheelX: "panX",
                                            wheelY: "zoomX",
                                            pinchZoomX: true,
                                            layout: root.verticalLayout,
                                        })
                                    );

                                    // Add X-axis (Code)
                                    let xAxis = chart.xAxes.push(
                                        am5xy.CategoryAxis.new(root, {
                                            categoryField: "code", // Use the 'code' field for categories
                                            renderer: am5xy.AxisRendererX.new(root, {
                                                minGridDistance: 30,
                                                labels: {
                                                    rotation: -45, // Rotate labels for better readability
                                                },
                                            }),
                                            tooltip: am5.Tooltip.new(root, {
                                                labelText: "{categoryX}", // Tooltip showing the code
                                            }),
                                        })
                                    );

                                    // Add X-axis label
                                    xAxis.children.moveValue(
                                        am5.Label.new(root, {
                                            text: "Dies Code", // X-axis caption
                                            x: am5.p50,
                                            centerX: am5.p50,
                                            fontWeight: "bold",
                                        }),
                                        0
                                    );

                                    xAxis.data.setAll(chartData);

                                    // Add Y-axis (MTTR Values)
                                    let yAxis = chart.yAxes.push(
                                        am5xy.ValueAxis.new(root, {
                                            renderer: am5xy.AxisRendererY.new(root, {}),
                                        })
                                    );

                                    // Add Y-axis label
                                    yAxis.children.moveValue(
                                        am5.Label.new(root, {
                                            text: "MTTR (Hours)", // Y-axis caption
                                            rotation: -90,
                                            y: am5.p50,
                                            centerX: am5.p50,
                                            fontWeight: "bold",
                                        }),
                                        0
                                    );

                                    // Create the column series
                                    let series = chart.series.push(
                                        am5xy.ColumnSeries.new(root, {
                                            name: "MTTR",
                                            xAxis: xAxis,
                                            yAxis: yAxis,
                                            valueYField: "mttr_value",
                                            categoryXField: "code",
                                            tooltip: am5.Tooltip.new(root, {
                                                labelText:
                                                    "Code: {categoryX}\nMTTR: {valueY} hours\nProblems: {problem_count}\nTotal Repair Time: {total_repair_time} hours",
                                            }),
                                        })
                                    );

                                    series.columns.template.setAll({
                                        tooltipText:
                                            "[bold]{categoryX}[/]\nMTTR: {valueY} hours\nProblems: {problem_count}\nRepair Time: {total_repair_time} hours",
                                        width: am5.percent(80),
                                        strokeOpacity: 0,
                                    });

                                    series.data.setAll(chartData);

                                    // Add cursor for interactivity
                                    chart.set(
                                        "cursor",
                                        am5xy.XYCursor.new(root, {
                                            behavior: "zoomX",
                                        })
                                    );

                                    // Animate on load
                                    chart.appear(1000, 100);
                                    series.appear();
                                });
                            });
                        </script>


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
