<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0" />
    <title>Data Chart</title>
    <!-- Adding Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet" />
    <!-- Adding the standalone version of Lightweight charts -->
    <script type="text/javascript" src="https://unpkg.com/lightweight-charts/dist/lightweight-charts.standalone.production.js"></script>
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <style>
        body {
            padding: 0;
            margin: 0;
            /* Add a background color to match the chart */
            background-color: #222;
        }
        /* Styles for attribution message */
        
        .lw-attribution {
            position: absolute;
            left: 0px;
            top: 0px;
            z-index: 3;
            /* place above the charts */
            padding: 10px 0px 0px 12px;
            font-family: "Roboto", sans-serif;
            font-size: 0.8em;
        }
        
        .lw-attribution a {
            cursor: pointer;
            color: rgb(54, 217, 122);
            opacity: 0.8;
        }
        
        .lw-attribution a:hover {
            color: rgb(54, 217, 122);
            opacity: 1;
        }
        
        .date-select {
            position: absolute;
            z-index: 9;
        }
        
        .loader {
            position: fixed;
            z-index: 9;
            background: #000000;
            width: 100%;
            height: 100vh;
            text-align: center;
            padding-top: 20vh;
        }
    </style>
</head>

<body>


    <div class="date-select">
        <input type="date" class="start_date">
        <input type="date" class="end_date">
        <select name="crypto" id="crypto">
            <option value="NDX.INDX" selcted>NASDAQ</option>
            <option value="SP500NTR.INDX">S&P500</option>
            <option value="US2Y.INDX">US02Y</option>
            <option value="BCOMCO.INDX">UKOIL</option>
            <option value="BCOMGC.INDX">GOLD</option>
        </select>
        <input class="load_chart" type="submit">
    </div>


    <div class="loader">
        <img src="./assets/img/loader.webp" alt="">
    </div>

    <div id="container" style="position: absolute; width: 100%; height: 100%">

    </div>
    <script type="text/javascript">
        setInterval(() => {
            if ($('#container').is(':empty')) {
                $(".loader").show();
            } else {
                $(".loader").fadeOut();
            }
        }, 500);

        $(document).ready(function() {

                    // By Default Load Last Days Data
                    <?php date_default_timezone_set('UTC'); ?>
                    
                    // I have found that last data date is 3 days old from today. 
                    // e.g. It is 13th February but they provided 10th February's data as last data
                    var start_date = "<?php 
                        $start_date = date('Y-m-d',strtotime("-3 days")); //get utc date
                        $start_date = $start_date . " 00:00:00"; //set time to 12 AM
                        echo $start_date;
                    ?>";

                    // Today's date
                    var end_date = "<?php echo date('Y-m-d H:i:s'); ?>"; //today current UTC date and time

                    var crypto = $('#crypto').find(":selected").val();

                    $("#container").empty();

                    // Get Api Data from https://eodhistoricaldata.com/
                    getApiData(start_date, end_date, 'NDX.INDX'); //by default load NASDAQ data


            $(".load_chart").click(function() {
        

                if (($('.start_date').val() != '') && ($('.end_date').val() != '') && ($('#crypto').find(":selected").val() != '')) {

                    // If user select the dates, time & the ticker

                    var start_date = $('.start_date').val();
                    start_date += ' 00:00:00';  
                    start_date = start_date.replace('T',' ');

                    var end_date = $('.end_date').val();
                    end_date += ' 00:00:00'; 
                    end_date = end_date.replace('T',' ');

                    var crypto = $('#crypto').find(":selected").val();

                    $("#container").empty()

                    // Get Api Data from https://eodhistoricaldata.com/
                    getApiData(start_date, end_date, crypto);
                    
                }else{

                    // If user does not select the dates & time but the ticker only

                    var crypto = $('#crypto').find(":selected").val();

                    // By Default Load Last Days Data
                    <?php date_default_timezone_set('UTC'); ?>
                    
                    // I have found that last data date is 3 days old from today. 
                    // e.g. It is 13th February but they provided 10th February's data as last data
                    if((crypto == 'US2Y.INDX')||(crypto == 'BCOMCO.INDX')||crypto == 'BCOMGC.INDX'){
                        // For EOD data get last 10 days data 
                        var start_date = "<?php 
                            $start_date = date('Y-m-d',strtotime("-1140 days")); //get utc date
                            $start_date = $start_date . " 00:00:00"; //set time to 12 AM
                            echo $start_date;
                        ?>";
                    }else{
                        // For intra day data
                        var start_date = "<?php 
                            $start_date = date('Y-m-d',strtotime("-600 days")); //get utc date
                            $start_date = $start_date . " 00:00:00"; //set time to 12 AM
                            echo $start_date;
                        ?>";
                    }

                    // Today's date
                    var end_date = "<?php echo date('Y-m-d H:i:s'); ?>"; //today current UTC date and time

                    $("#container").empty();

                    // Get Api Data from https://eodhistoricaldata.com/
                    getApiData(start_date, end_date, crypto);
                }
            });
        });



        //Get Api Data from https://eodhistoricaldata.com/
        function getApiData(startDate, endDate, crypto){

            var api_url = 'http://eod.com/Stock%20Market%20Data%20Visualisation';

            // API get data from eodhistoricaldata.com and store CSV file in server
            axios.get(api_url+'/api/api_get_eodhistoricaldata_data.php', {
                    params: {
                        startDate: startDate,
                        endDate: endDate,
                        crypto: crypto
                    }
                })
                .then(function(response) {
                    // handle success
                    // console.log(response.request.responseURL);
                    loadChart(startDate, endDate, crypto); //get chart data and show the chart
                })
                .catch(function(error) {
                    // handle error
                    console.log(error);
                })
                .then(function() {

                });
            // END - API get data from eodhistoricaldata.com and store CSV file in server 
        }






        // Load data in chart
        function loadChart(startDate, endDate, crypto) {


            // Get data from CSV file as JSON which is saved in server
            var apiResponseDataSet;

            var api_url = 'http://eod.com/Stock%20Market%20Data%20Visualisation';

            axios.get(api_url+'/api/api_get_chart_data.php', {
                    params: {
                        startDate: startDate,
                        endDate: endDate,
                        crypto: crypto
                    }
                })
                .then(function(response) {
                    // handle success
                    // console.log(response.request.responseURL);
                    setData(response.data); //set response data
                    showChart(); //show the candlestick chart
                })
                .catch(function(error) {
                    // handle error
                    console.log(error);
                })
                .then(function() {

                });

            // END - Load data from CSV file which is saved in server  


            function setData(data) {
                apiResponseDataSet = data;
            }

            // Function to generate a sample set of Candlestick datapoints

            function showChart() { //invoke it after api call

                function generateCandlestickData() {
                    return apiResponseDataSet;
                }

                // Show/hide time in chart
                if((crypto == 'US2Y.INDX')||(crypto == 'BCOMCO.INDX')||crypto == 'BCOMGC.INDX'){
                    // For EOD chart
                    var showTimeInChart = false;
                }else{
                    // For intra day chart
                    var showTimeInChart = true;
                }
                    
                // Create the Lightweight Chart within the container element
                const chart = LightweightCharts.createChart(
                    document.getElementById('container'), {
                        layout: {
                            background: {
                                color: "#222"
                            },
                            textColor: "#C3BCDB",
                        },
                        grid: {
                            vertLines: {
                                color: "#444"
                            },
                            horzLines: {
                                color: "#444"
                            },
                        },
                        timeScale: {
                            borderColor: "rgba(197, 203, 206, 0.8)",
                            timeVisible: showTimeInChart,
                            secondsVisible: false,
                        },
                    }
                );
                

                // Setting the border color for the vertical axis
                chart.priceScale().applyOptions({
                    borderColor: "#71649C",
                });

                // Setting the border color for the horizontal axis
                chart.timeScale().applyOptions({
                    borderColor: "#71649C",
                });

                // Adjust the starting bar width (essentially the horizontal zoom)
                chart.timeScale().applyOptions({
                    barSpacing: 10,
                });

                // Changing the font
                chart.applyOptions({
                    layout: {
                        fontFamily: "'Roboto', sans-serif",
                    },
                });

                // Get the current users primary locale
                const currentLocale = window.navigator.languages[0];
                // Create a number format using Intl.NumberFormat
                const myPriceFormatter = Intl.NumberFormat(currentLocale, {
                    style: "currency",
                    currency: "USD", // Currency for data points
                }).format;

                // Apply the custom priceFormatter to the chart
                chart.applyOptions({
                    localization: {
                        priceFormatter: myPriceFormatter,
                        dateFormat: 'dd MMMM yyyy',
                    },
                });

                // Customizing the Crosshair
                chart.applyOptions({
                    crosshair: {
                        // Change mode from default 'magnet' to 'normal'.
                        // Allows the crosshair to move freely without snapping to datapoints
                        mode: LightweightCharts.CrosshairMode.Normal,

                        // Vertical crosshair line (showing Date in Label)
                        vertLine: {
                            width: 8,
                            color: "#C3BCDB44",
                            style: LightweightCharts.LineStyle.Solid,
                            labelBackgroundColor: "#9B7DFF",
                        },

                        // Horizontal crosshair line (showing Price in Label)
                        horzLine: {
                            color: "#9B7DFF",
                            labelBackgroundColor: "#9B7DFF",
                        },
                    },
                });             

                // Generate sample data to use within a candlestick series
                const candleStickData = generateCandlestickData().map((datapoint) => {
                    // map function is changing the color for the individual
                    // candlestick points that close above 205
                    if (datapoint.close < 205) return datapoint;
                    // we are adding 'color' and 'wickColor' properties to the datapoint.
                    // Using spread syntax: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Operators/Spread_syntax#spread_in_object_literals
                    return {...datapoint,
                        // color: "orange",
                        // wickColor: "orange"
                    };
                });

                // Convert the candlestick data for use with a line series
                const lineData = candleStickData.map((datapoint) => ({
                    time: datapoint.time,
                    value: (datapoint.close + datapoint.open) / 2,
                }));

                // Add an area series to the chart,
                // Adding this before we add the candlestick chart
                // so that it will appear beneath the candlesticks
                const areaSeries = chart.addAreaSeries({
                    lastValueVisible: false, // hide the last value marker for this series
                    crosshairMarkerVisible: false, // hide the crosshair marker for this series
                    lineColor: "transparent", // hide the line
                    topColor: "rgba(56, 33, 110,0.6)",
                    bottomColor: "rgba(56, 33, 110, 0.1)",
                });
                // Set the data for the Area Series
                areaSeries.setData(lineData);
                

                // Create the Main Series (Candlesticks)
                const mainSeries = chart.addCandlestickSeries();
                // Set the data for the Main Series
                mainSeries.setData(candleStickData);

                // Changing the Candlestick colors
                mainSeries.applyOptions({
                    wickUpColor: "rgb(54, 217, 122)",
                    upColor: "rgb(54, 217, 122)",
                    wickDownColor: "rgb(225, 50, 85)",
                    downColor: "rgb(225, 50, 85)",
                    borderVisible: false,
                });

                // Adjust the options for the priceScale of the mainSeries
                mainSeries.priceScale().applyOptions({
                    autoScale: false, // disables auto scaling based on visible content
                    scaleMargins: {
                        top: 0.1,
                        bottom: 0.2,
                    },
                });


                // chart.addLineSeries({
                //     priceFormat: {
                //         minMove: 0.25,
                //     }
                // });                 
                
                chart.addBarSeries({
                    priceFormat: {
                        minMove: 0.25,
                    }
                }); 

                mainSeries.createPriceLine({
                    price: 12510.00,
                    color: 'rgba(229, 37, 69, 1)',
                    lineWidth: 2,
                    lineStyle: LightweightCharts.LineStyle.Dotted,
                    title: 'sell order',
                    draggable: true,
                });

                mainSeries.createPriceLine({
                    price: 12410.00,
                    color: 'rgba(229, 37, 69, 1)',
                    lineWidth: 2,
                    lineStyle: LightweightCharts.LineStyle.Dashed,
                    title: 'buy order',
                    draggable: true,
                });


                

                // Adding a window resize event handler to resize the chart when
                // the window size changes.
                // Note: for more advanced examples (when the chart doesn't fill the entire window)
                // you may need to use ResizeObserver -> https://developer.mozilla.org/en-US/docs/Web/API/ResizeObserver
                window.addEventListener("resize", () => {
                    chart.resize(window.innerWidth, window.innerHeight);
                });

            };


        }
    </script>
</body>

</html>