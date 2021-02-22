import Chart from "chart.js";

export const ordersChart = {
  createChart(chartId) {
    const chartColor = "#FFFFFF";
    const fallBackColor = "#f96332";
    const color = this.color || fallBackColor;
    const ctx = document.getElementById(chartId).getContext("2d");
    const gradientStroke = ctx.createLinearGradient(500, 0, 100, 0);
    gradientStroke.addColorStop(0, color);
    gradientStroke.addColorStop(1, chartColor);

    new Chart(ctx, {
      type: "bar",
      data: {
        labels: ["Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [
          {
            label: "Sales",
            tension: 0.4,
            borderWidth: 0,
            pointRadius: 0,
            backgroundColor: "#fb6340",
            data: [25, 20, 30, 22, 17, 29],
            maxBarThickness: 10,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
          display: false,
        },
        tooltips: {
          enabled: true,
          mode: "index",
          intersect: false,
        },
        scales: {
          yAxes: [
            {
              gridLines: {
                borderDash: [2],
                borderDashOffset: [2],
                drawBorder: false,
                drawTicks: false,
                lineWidth: 0,
                zeroLineWidth: 0,
                zeroLineBorderDash: [2],
                zeroLineBorderDashOffset: [2],
              },
              ticks: {
                beginAtZero: true,
                padding: 10,
                fontSize: 13,
                fontColor: "#8898aa",
                fontFamily: "Open Sans",
                callback: function (value) {
                  if (!(value % 10)) {
                    return value;
                  }
                },
              },
            },
          ],
          xAxes: [
            {
              gridLines: {
                drawBorder: false,
                drawOnChartArea: false,
                drawTicks: false,
              },
              ticks: {
                padding: 20,
                fontSize: 13,
                fontColor: "#8898aa",
                fontFamily: "Open Sans",
              },
            },
          ],
        },
      },
    });
  },
};

const funcs = {
  ordersChart() {},
};

export default funcs;
