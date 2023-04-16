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
        
        .select-options {
            display: inline-flex;
            position: absolute;
            z-index: 9;
            margin-top:10px;
            margin-left:10px;
            color:white;
            font-family: Roboto;
            font-size:12px;
        }

        .loader {
            position: fixed;
            z-index: 99999;
            background: #000000;
            width: 100%;
            height: 100vh;
            text-align: center;
            padding-top: 20vh;
        }
        .ohlc {
            position: fixed;
            width: auto;
            top: 40px;
            z-index: 99999;
            height: 20px;
            color: hsl(240deg 8% 93%);
            font-family: 'Roboto';
            font-size: 12px;
            display: inline-flex;
            visibility:hidden;
        }
        .o{
            padding-left:10px;
            padding-right:10px;
            padding-top:3px;
        }
        .h {
            padding-left:10px;
            padding-right:10px;
            padding-top:3px;
        }
        .l {
            padding-left:10px;
            padding-right:10px;
            padding-top:3px;
        } 
        .c {
            padding-left:10px;
            padding-right:10px;
            padding-top:3px;
        } 
        .v {
            padding-left:10px;
            padding-right:10px;
            padding-top:3px;
        }
        .tickname, .timescale{
            padding-left:10px;
            font-weight:700;
            font-size:14px;
        }
        .duration{
            padding-left:10px;
            padding-right:10px;
            font-weight:700;
            font-size:14px;
        }
        .tickname:after, .duration:after{
            content:"•";
            margin-left:10px;
        }
        #duration, .start_date, .end_date, #crypto, #price_scale{
            margin-right:10px;
        }
        .start_date, .end_date{
            margin-left:10px;
        }
        .input_lebel{
            padding-top:3px;
        }
    </style>
</head>

<body>
 

    <div class="select-options">
        <select name="crypto" id="crypto">
        </select>
        <select name="duration" id="duration">
            <option value="1d" selected>1 day</option>
            <option value="1m">1 Month</option>
            <option value="1w">1 Week</option>
            <option value="3m">3 Months</option>
            <option value="6m">6 Months</option>
            <option value="12m">1 Year</option>
        </select>
        <select name="price_scale" id="price_scale">
            <option value="Normal">Normal</option>
            <option value="Logarithmic" selected>Logarithmic</option>
            <option value="Percentage">Percentage</option>
        </select>
    </div>

    <div class="ohlc">
        <div class="tickname"></div>
        <div class="duration"></div>
        <div class="o"><strong>O:</strong> <span></span></div>
        <div class="h"><strong>H:</strong> <span></span></div>
        <div class="l"><strong>L:</strong> <span></span></div>
        <div class="c"><strong>C:</strong> <span></span></div>
        <div class="v"><strong>V:</strong> <span></span></div>
    </div>

    <div class="loader">
        <img src="./assets/img/loader.webp" alt="">
    </div>

    <div id="container" style="position: absolute; width: 100%; height: 100%"></div>

<script type="text/javascript">

var api_url = "http://eod.com/Stock-Market-Data-Visualisation";


// Load tickers

var tickers;

axios
  .get(api_url + "/api/tickers.php")
  .then(function (response) {
    // handle success
    tickers = response.data;
    // Set ticker
    var options = $('#crypto',)
    for (var i = 0; i < tickers.length; i++) {
      $('<option>', {
        value: tickers[i].ticker_code,
        text: tickers[i].ticker,
      }).appendTo(options);
    }
    $("#crypto option:first").attr('selected','selected');
  })
  .catch(function (error) {
    // handle error
    console.log(error);
  })
  .then(function () {});

// END - Load tickers

// Loading Icon
setInterval(() => {
  if ($("#container").is(":empty")) {
    $(".loader").show();
  } else {
    $(".loader").fadeOut();
  }
}, 500);
// END - Loading Icon

$(document).ready(function () {

  setTimeout(() => {
    // Show the crypto value on indicator
    $(".tickname").text($("#crypto").find(":selected").text());
    $(".duration").text($("#duration").find(":selected").text());

    var crypto = $("#crypto").find(":selected").val();
    var duration = $("#duration").find(":selected").val();

    $("#container").empty();

    // Load Data from csv file and show chart
    loadChart(crypto, duration);
  }, 500);

  $("#duration").change(function () {
    // Show the crypto value on indicator
    $(".tickname").text($("#crypto").find(":selected").text());
    $(".duration").text($("#duration").find(":selected").text());
  });

  $("#crypto").change(function () {
    // Clear all setInterval function
    var id = window.setInterval(function () {}, 0);
    while (id--) {
      window.clearInterval(id);
    }
    // END - Clear all setInterval function

    // Loading Icon
    setInterval(() => {
      if ($("#container").is(":empty")) {
        $(".loader").show();
      } else {
        $(".loader").fadeOut();
      }
    }, 500);
    // END - Loading Icon

    // Show the crypto value on indicator
    $(".tickname").text($("#crypto").find(":selected").text());
    $(".duration").text($("#duration").find(":selected").text());

    var crypto = $("#crypto").find(":selected").val();
    var duration = $("#duration").find(":selected").val();

    $("#container").empty();

    // Load Data from csv file and show chart
    loadChart(crypto, duration);
  });

  $("#duration").change(function () {
    // Clear all setInterval function
    var id = window.setInterval(function () {}, 0);
    while (id--) {
      window.clearInterval(id);
    }
    // END - Clear all setInterval function

    // Loading Icon
    setInterval(() => {
      if ($("#container").is(":empty")) {
        $(".loader").show();
      } else {
        $(".loader").fadeOut();
      }
    }, 500);
    // END - Loading Icon


    // Show the crypto value on indicator
    $(".tickname").text($("#crypto").find(":selected").text());
    $(".duration").text($("#duration").find(":selected").text());

    var crypto = $("#crypto").find(":selected").val();
    var duration = $("#duration").find(":selected").val();

    $("#container").empty();

    // Load Data from csv file and show chart
    loadChart(crypto, duration);

  });
});

// Load data in chart
function loadChart(crypto, duration) {
  // Get data from CSV file as JSON which is saved in server
  var apiResponseDataSet;

  axios
    .get(api_url + "/api/api_get_chart_data.php", {
      params: {
        crypto: crypto,
        duration: duration,
      },
    })
    .then(function (response) {
      // handle success
      console.log(response.request.responseURL);
      setData(response.data); //set response dataz
      showChart(); //show the candlestick chart
    })
    .catch(function (error) {
      // handle error
      console.log(error);
    })
    .then(function () {});

  // END - Load data from CSV file which is saved in server

  function setData(data) {
    apiResponseDataSet = data;
  }

  // Function to generate a sample set of Candlestick datapoints

  function showChart() {
    //invoke it after api call

    function generateCandlestickData() {
      return apiResponseDataSet;
    }

    //Show/hide time in chart
    // var showTimeInChart = false;
    var showTimeInChart = true;

    // Create the Lightweight Chart within the container element
    const chart = LightweightCharts.createChart(
      document.getElementById("container"),
      {
        layout: {
          background: {
            color: "#222",
          },
          textColor: "#C3BCDB",
        },
        grid: {
          vertLines: {
            color: "#444",
          },
          horzLines: {
            color: "#444",
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
        dateFormat: "dd MMMM yyyy",
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

        vertLine: {
          color: "#9B7DFF",
          labelBackgroundColor: "#9B7DFF",
        },

        // Horizontal crosshair line (showing Price in Label)
        horzLine: {
          color: "#9B7DFF",
          labelBackgroundColor: "#9B7DFF",
        },
      },
    });

    const chartData = generateCandlestickData().map((datapoint) => ({
      time: datapoint.time,
      open: datapoint.open,
      high: datapoint.high,
      low: datapoint.low,
      close: datapoint.close,
    }));

    const volumeData = generateCandlestickData().map((datapoint) => ({
      time: datapoint.time,
      value: datapoint.volume,
      color: datapoint.color,
    }));

    // Generate sample data to use within a candlestick series
    const candleStickData = chartData.map((datapoint) => {
      // map function is changing the color for the individual
      // candlestick points that close above 205
      // if (datapoint.close < 205) return datapoint;
      // we are adding 'color' and 'wickColor' properties to the datapoint.
      // Using spread syntax: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Operators/Spread_syntax#spread_in_object_literals
      return {
        ...datapoint,
        // color: "orange",
        // wickColor: "orange"
      };
    });

    //Convert the candlestick data for use with a line series
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
    // console.log(candleStickData);

    // Setting volume bar series
    const volumeSeries = chart.addHistogramSeries({
      color: "#26a69a",
      priceFormat: {
        type: "volume",
      },
      priceScaleId: "", // set as an overlay by setting a blank priceScaleId
      // set the positioning of the volume series
      scaleMargins: {
        top: 0.95, // highest point of the series will be 70% away from the top
        bottom: 0,
      },
    });

    // Volumebar price scale
    volumeSeries.priceScale().applyOptions({
      scaleMargins: {
        top: 0.95, // highest point of the series will be 70% away from the top
        bottom: 0,
      },
    });

    // Set data for volume series
    volumeSeries.setData(volumeData);

    // Show OHLC on hover candlestick
    $(".ohlc").css("visibility", "hidden"); //by default hide OHLC

    chart.subscribeCrosshairMove((param) => {
      param.seriesData.forEach(myFunction);
      function myFunction(item) {
        // console.log(item);
        if (item.open != "") {
          $(".ohlc").css("visibility", "visible");
          $(".o span").text(item.open);
          $(".h span").text(item.high);
          $(".l span").text(item.low);
          $(".c span").text(item.close);
          if (item.value == 0 || item.value == "") {
            // For EOD chart
            $(".v span").html("Volume data not available!");
          } else {
            // For intra day chart
            $(".v span").text(item.value);
          }
        } else {
          $(".ohlc").css("visibility", "hidden");
        }
      }
    });

    // on change price scale input change price scale
    $(document).ready(function () {
      $("#price_scale").change(function () {
        if ($("#price_scale").find(":selected").val() == "Percentage") {
          chart.priceScale("right").applyOptions({
            borderColor: "#71649C",
            scaleMargins: {
              top: 0.1,
              bottom: 0.1,
            },
            mode: LightweightCharts.PriceScaleMode.Percentage,
            borderColor: "rgba(197, 203, 206, 0.4)",
          });
        }
        if ($("#price_scale").find(":selected").val() == "Logarithmic") {
          chart.priceScale("right").applyOptions({
            borderColor: "#71649C",
            scaleMargins: {
              top: 0.1,
              bottom: 0.1,
            },
            mode: LightweightCharts.PriceScaleMode.Logarithmic,
            borderColor: "rgba(197, 203, 206, 0.4)",
          });
        }
        if ($("#price_scale").find(":selected").val() == "Normal") {
          chart.priceScale("right").applyOptions({
            borderColor: "#71649C",
            scaleMargins: {
              top: 0.1,
              bottom: 0.1,
            },
            mode: LightweightCharts.PriceScaleMode.Normal,
            borderColor: "rgba(197, 203, 206, 0.4)",
          });
        }
      });
    });
    // END - on change price scale input change price scale

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

    // Adding a window resize event handler to resize the chart when
    // the window size changes.
    // Note: for more advanced examples (when the chart doesn't fill the entire window)
    // you may need to use ResizeObserver -> https://developer.mozilla.org/en-US/docs/Web/API/ResizeObserver
    window.addEventListener("resize", () => {
      chart.resize(window.innerWidth, window.innerHeight);
    });
  }
}

</script>

</body>
</html>