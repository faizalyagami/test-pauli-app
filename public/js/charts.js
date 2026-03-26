/**
 * Chart.js Configurations for Pauli Test Results
 */

class PauliCharts {
    constructor() {
        this.charts = {};
    }
    
    createLineChart(elementId, data, options = {}) {
        const ctx = document.getElementById(elementId).getContext('2d');
        
        const defaultOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Jawaban'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Interval Waktu'
                    }
                }
            }
        };
        
        this.charts[elementId] = new Chart(ctx, {
            type: 'line',
            data: data,
            options: { ...defaultOptions, ...options }
        });
        
        return this.charts[elementId];
    }
    
    createBarChart(elementId, data, options = {}) {
        const ctx = document.getElementById(elementId).getContext('2d');
        
        const defaultOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah'
                    }
                }
            }
        };
        
        this.charts[elementId] = new Chart(ctx, {
            type: 'bar',
            data: data,
            options: { ...defaultOptions, ...options }
        });
        
        return this.charts[elementId];
    }
    
    createDoughnutChart(elementId, data, options = {}) {
        const ctx = document.getElementById(elementId).getContext('2d');
        
        const defaultOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        };
        
        this.charts[elementId] = new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: { ...defaultOptions, ...options }
        });
        
        return this.charts[elementId];
    }
    
    createPerformanceChart(elementId, lineData, columnData) {
        // Create line chart for performance over time
        const lineChart = this.createLineChart(`${elementId}-line`, lineData, {
            title: {
                display: true,
                text: 'Performa per Interval'
            }
        });
        
        // Create bar chart for column performance
        const barChart = this.createBarChart(`${elementId}-bar`, columnData, {
            title: {
                display: true,
                text: 'Performa per Kolom'
            }
        });
        
        return { lineChart, barChart };
    }
    
    createComparisonChart(elementId, datasets, labels) {
        const ctx = document.getElementById(elementId).getContext('2d');
        
        const data = {
            labels: labels,
            datasets: datasets.map(dataset => ({
                label: dataset.label,
                data: dataset.data,
                backgroundColor: dataset.backgroundColor || 'rgba(54, 162, 235, 0.5)',
                borderColor: dataset.borderColor || 'rgb(54, 162, 235)',
                borderWidth: 1
            }))
        };
        
        this.charts[elementId] = new Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        return this.charts[elementId];
    }
    
    updateChart(chartId, newData) {
        if (this.charts[chartId]) {
            this.charts[chartId].data = newData;
            this.charts[chartId].update();
        }
    }
    
    destroyChart(chartId) {
        if (this.charts[chartId]) {
            this.charts[chartId].destroy();
            delete this.charts[chartId];
        }
    }
    
    // Specific chart for Pauli Test Result
    createResultCharts(data) {
        // Performance by line chart
        const lineChartData = {
            labels: data.lineLabels,
            datasets: [
                {
                    label: 'Jawaban Benar',
                    data: data.correctByLine,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                },
                {
                    label: 'Total Jawaban',
                    data: data.totalByLine,
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.1
                }
            ]
        };
        this.createLineChart('lineChart', lineChartData);
        
        // Performance by column chart
        const columnChartData = {
            labels: data.columnLabels,
            datasets: [
                {
                    label: 'Jawaban Benar',
                    data: data.correctByColumn,
                    backgroundColor: 'rgba(153, 102, 255, 0.5)',
                    borderColor: 'rgb(153, 102, 255)',
                    borderWidth: 1
                },
                {
                    label: 'Total Jawaban',
                    data: data.totalByColumn,
                    backgroundColor: 'rgba(255, 159, 64, 0.5)',
                    borderColor: 'rgb(255, 159, 64)',
                    borderWidth: 1
                }
            ]
        };
        this.createBarChart('columnChart', columnChartData);
        
        // Speed chart
        const speedChartData = {
            labels: data.speedLabels,
            datasets: [{
                label: 'Jawaban per Menit',
                data: data.speedData,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1,
                fill: true
            }]
        };
        this.createLineChart('speedChart', speedChartData);
        
        // Accuracy doughnut
        const accuracyData = {
            labels: ['Benar', 'Salah'],
            datasets: [{
                data: [data.correctTotal, data.wrongTotal],
                backgroundColor: ['#27ae60', '#e74c3c'],
                borderWidth: 0
            }]
        };
        this.createDoughnutChart('accuracyChart', accuracyData);
    }
}

// Export
window.PauliCharts = PauliCharts;