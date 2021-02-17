import Chart from "chart.js";

export const activeUsersChart = {
  createChart(chartId) {
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
        labels: ['May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [
          {
            label: "Performance",
            tension: .4,
            borderWidth: 4,
            borderColor: "#5e72e4",
            pointRadius: 0,
            backgroundColor: 'transparent',
            data: [0, 20, 10, 30, 15, 40, 20, 60, 60],
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
          mode: 'index',
          intersect: false,
        },
        scales: {
          yAxes: [
            {
              barPercentage: 1.6,
              gridLines: {
                drawBorder: false,
                color: 'rgba(29,140,248,0.0)',
                zeroLineColor: 'transparent'
              },
              ticks: {
                suggestedMin: 60,
                suggestedMax: 125,
                padding: 20,
                fontColor: '#9e9e9e'
              }
            }
          ],
          xAxes: [
            {
              barPercentage: 1.6,
              gridLines: {
                drawBorder: false,
                color: 'rgba(29,140,248,0.1)',
                zeroLineColor: 'transparent'
              },
              ticks: {
                padding: 20,
                fontColor: '#9e9e9e'
              }
            }
          ]
        },
        layout: {
          padding: { left: 0, right: 0, top: 15, bottom: 15 },
        },
      },
    });
  },
};

const funcs = {
  activeUsersChart() {}
};

export default funcs;
