// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

// Fetch monthly revenue data
function fetchMonthlyRevenue() {
    fetch('fetch_monthly_revenue.php')
        .then(response => response.json())
        .then(data => {
            // Update Revenue Chart
            revenueChart.data.labels = data.labels;
            revenueChart.data.datasets[0].data = data.values;
            revenueChart.update();
        })
        .catch(error => console.error('Error fetching revenue data:', error));
}

// Fetch new members data
function fetchNewMembers() {
    fetch('fetch_new_members.php')
        .then(response => response.json())
        .then(data => {
            // Update New Members Chart
            newMembersChart.data.labels = data.labels;
            newMembersChart.data.datasets[0].data = data.values;
            newMembersChart.update();
        })
        .catch(error => console.error('Error fetching new members data:', error));
}

// Revenue Chart
var ctx = document.getElementById("revenueChart");
var revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [], // Will be populated by AJAX
        datasets: [{
            label: "Revenue",
            lineTension: 0.3,
            backgroundColor: "rgba(255, 193, 7, 0.05)",
            borderColor: "rgba(255, 193, 7, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(255, 193, 7, 1)",
            pointBorderColor: "rgba(255, 193, 7, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(255, 193, 7, 1)",
            pointHoverBorderColor: "rgba(255, 193, 7, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            data: [], // Will be populated by AJAX
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            xAxes: [{
                time: {
                    unit: 'date'
                },
                gridLines: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 7
                }
            }],
            yAxes: [{
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                    callback: function(value, index, values) {
                        return '₱' + number_format(value);
                    }
                },
                gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            }],
        },
        legend: {
            display: false
        },
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            titleMarginBottom: 10,
            titleFontColor: '#6e707e',
            titleFontSize: 14,
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            intersect: false,
            mode: 'index',
            caretPadding: 10,
            callbacks: {
                label: function(tooltipItem, chart) {
                    var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                    return datasetLabel + ': ₱' + number_format(tooltipItem.yLabel);
                }
            }
        }
    }
});

// New Members Chart
var ctx2 = document.getElementById("newMembersChart");
var newMembersChart = new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: [], // Will be populated by AJAX
        datasets: [{
            label: "New Members",
            backgroundColor: "rgba(255, 193, 7, 1)",
            hoverBackgroundColor: "rgba(255, 193, 7, 0.9)",
            borderColor: "rgba(255, 193, 7, 1)",
            data: [], // Will be populated by AJAX
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            xAxes: [{
                gridLines: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 6
                }
            }],
            yAxes: [{
                ticks: {
                    min: 0,
                    maxTicksLimit: 5,
                    padding: 10,
                },
                gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            }],
        },
        legend: {
            display: false
        },
        tooltips: {
            titleMarginBottom: 10,
            titleFontColor: '#6e707e',
            titleFontSize: 14,
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
            callbacks: {
                label: function(tooltipItem, chart) {
                    var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                    return datasetLabel + ': ' + number_format(tooltipItem.yLabel);
                }
            }
        },
    }
});

// Call the fetch functions initially
fetchMonthlyRevenue();
fetchNewMembers();

// Update every 5 minutes
setInterval(function() {
    fetchMonthlyRevenue();
    fetchNewMembers();
}, 300000); 