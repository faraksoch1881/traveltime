<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chart.js with Draggable Line and Intersections</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background-color: white;
      margin: 0;
      position: relative;

        background-image: url('https://t4.ftcdn.net/jpg/02/10/45/95/360_F_210459536_XmLDEcKq2DpeNLVmheuWeu9NM9aGKnih.jpg');
  background-size: cover; /* Ensures the image covers the entire viewport */
  background-position: center; /* Centers the image */
  background-repeat: no-repeat; /* Prevents the image from repeating */
    }

    #chart-container {
      width: 60%;
      height: 90%;
      position: relative;
    }

    canvas {
      background-color: white;
    }

    /* Draggable vertical line */
    /* Draggable vertical line */
    #draggable {
      position: absolute;
      width: 5px;
      height: 100%;
      background-color: rgba(0, 0, 255, 0.5);
      z-index: 10;
      cursor: pointer;
    }

    /* Tooltip to show values */
    #tooltip {
      position: absolute;
      background-color: rgba(0, 0, 0, 0.75);
      color: white;
      padding: 5px;
      border-radius: 3px;
      font-size: 12px;
      display: none;
      z-index: 20;
      max-width: 150px; /* Set a reasonable max width */
    }


  </style>
</head>
<body>

  <div id="chart-container">
    <div id="draggable"></div>
    <canvas id="myChart"></canvas>
    <div id="tooltip"></div> <!-- Tooltip to display values -->

      <div id ="errorbox"></div>
    <div id ="errorbox2"></div>
    <div id="pixelbox"></div>
  </div>
  <br>


  <script>


    // Initialize the draggable line
    $(function() {
      $("#draggable").draggable({
        axis: "x",
        containment: "#myChart",
        drag: function(event, ui) {
          const canvas = document.getElementById('myChart');
          const ctx = canvas.getContext('2d');
          const rect = canvas.getBoundingClientRect();
          const chartLeft = rect.left;
          const chartHeight = rect.height;
          
          const xPos = ui.position.left; // Position of the draggable line
          const xValue = myChart.scales.x.getValueForPixel(xPos); // X value on the chart




          // Find the nearest points on the datasets
          const pYValue = getYValueAtX(xValue, myChart.data.datasets[0].data);
          const sYValue = getYValueAtX(xValue, myChart.data.datasets[1].data);

          const difference = pYValue && sYValue ? (pYValue - sYValue) : null;
          const xOffset = -500; // To shift the tooltip slightly right of the draggable line
          const yOffset = -30; // To position it a bit above the draggable line


          // Update the tooltip with the current x, p, and s values
          const tooltip = document.getElementById('tooltip');
          tooltip.innerHTML = `Distance: ${xValue.toFixed(2) } km<br>P: ${pYValue ? pYValue.toFixed(2) : 'N/A'} min<br>S: ${sYValue ? sYValue.toFixed(2) : 'N/A'}min<br>Time: ${difference ? difference.toFixed(2) : 'N/A'}min`;
          tooltip.style.display = 'block';
        tooltip.style.left = `${xPos + chartLeft + xOffset}px`;
tooltip.style.top = `${rect.top + yOffset}px`;

    const xPixelPos = myChart.scales.x.getPixelForValue(xValue); // Get pixel for xValue
    const yPixelPos = myChart.scales.y.getPixelForValue(pYValue); // Get pixel for sYValue

    const y1PixelPos = myChart.scales.y.getPixelForValue(sYValue); // Get pixel for sYValue


    const height = Math.abs(yPixelPos - y1PixelPos);



    const blackLine = document.getElementById('draggable');
    blackLine.style.top = `${rect.top}px`;
    blackLine.style.height = `${chartHeight}px`;


        }
      });
    });

    // Helper function to find Y value for a given X in the dataset
    function getYValueAtX(x, data) {
      // Find the nearest point in the data for the given x
      const point = data.find(p => p.x >= x);
      return point ? point.y : null;
    }




    // AJAX function to fetch JSON data
    function fetchJSONFile(filePath, callback) {
      var xhr = new XMLHttpRequest();
      xhr.open('GET', filePath, true);
      xhr.responseType = 'json';
      xhr.onload = function() {
        if (xhr.status === 200) {
      callback(xhr.response);
        } else {
          console.error('Failed to load JSON file.');
        }
      };
      xhr.send();
    }

    // Callback to process the data and plot the chart
    function plotChart(jsonData) {
      const pData = jsonData
        .filter(item => item.p !== "")
        .map(item => ({
          x: item.x,
          y: item.p
        }));
      const sData = jsonData
        .filter(item => item.s !== "")
        .map(item => ({
          x: item.x,
          y: item.s
        }));

      // Chart.js configuration
      const ctx = document.getElementById('myChart').getContext('2d');
      myChart = new Chart(ctx, {
        type: 'line',
        data: {
          datasets: [
            {
              label: 'p Values',
              data: pData,
              borderColor: 'blue',
              fill: false,
              tension: 0.1,
              pointRadius: 0,
              showLine: true
            },
            {
              label: 's Values',
              data: sData,
              borderColor: 'red',
              fill: false,
              tension: 0.1,
              pointRadius: 0,
              showLine: true
            }
          ]
        },
        options: {
          scales: {
            x: {
              type: 'linear',
              position: 'bottom',
              title: {
                display: true,
                text: 'Time (Minutes)'
              }
            },
            y: {
              title: {
                display: true,
                text: 'Distance(km)'
              }
            }
          }
        }
      });
    }

    // Fetch and plot the chart using AJAX
    fetchJSONFile('csvjson.json', plotChart);
  </script>
</body>
</html>



