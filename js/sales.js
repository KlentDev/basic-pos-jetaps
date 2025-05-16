function filterChart() {
  var startDate = document.getElementById("startDate").value;
  var endDate = document.getElementById("endDate").value;

  // Filter the data based on the selected date range
  var filteredData = [];
  for (var i = 0; i < chartData.length; i++) {
      var orderDate = chartData[i].order_date;
      if (orderDate >= startDate && orderDate <= endDate) {
          filteredData.push(chartData[i]);
      }
  }

  // Update the Morris chart with the filtered data
  chart.setData(filteredData);
}

var chartData = [<?php echo $chart_data; ?>];

var chart = Morris.Bar({
  element: 'chart',
  data: chartData,
  xkey: 'order_date',
  ykeys: ['paid', 'subtotal', 'discount',],
  labels: ['Paid', 'Subtotal', 'discount',],
  hideHover: 'auto',
  stacked: true
});