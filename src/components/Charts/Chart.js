import Chart from "chart.js";

export const salesChart = {
  createChart(chartId, chartData) {
    const chartColor = "#FFFFFF";
    const fallBackColor = "#f96332";
    const color = this.color || fallBackColor;
    const ctx = document.getElementById(chartId).getContext("2d");
    const gradientStroke = ctx.createLinearGradient(500, 0, 100, 0);
    gradientStroke.addColorStop(0, color);
    gradientStroke.addColorStop(1, chartColor);

    new Chart(ctx, {
      type: "line",
      data: {
        labels: ["May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [
          {
            label: "Performance",
            tension: 0.4,
            borderWidth: 4,
            borderColor: "#5e72e4",
            pointRadius: 0,
            backgroundColor: "transparent",
            data: chartData,
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
              barPercentage: 1.6,
              gridLines: {
                drawBorder: false,
                color: "rgba(29,140,248,0.0)",
                zeroLineColor: "transparent",
              },
              ticks: {
                padding: 0,
                fontColor: "#8898aa",
                fontSize: 13,
                fontFamily: "Open Sans",
              },
            },
          ],
          xAxes: [
            {
              barPercentage: 1.6,
              gridLines: {
                drawBorder: false,
                color: "rgba(29,140,248,0.0)",
                zeroLineColor: "transparent",
              },
              ticks: {
                padding: 10,
                fontColor: "#8898aa",
                fontSize: 13,
                fontFamily: "Open Sans",
              },
            },
          ],
        },
        layout: {
          padding: 0,
        },
      },
    });
  },
};

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
  salesChart() {},
  ordersChart() {},
};

export default funcs;
