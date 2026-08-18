// Teacher Dashboard Analytics
jQuery(document).ready(function($) {
    // Initialize charts when the analytics tab is active
    function initAnalyticsCharts() {
        // Lesson Completion Trends (Line Chart)
        const completionCtx = document.getElementById('lesson-completion-chart');
        if (completionCtx) {
            new Chart(completionCtx, {
                type: 'line',
                data: {
                    labels: [], // Will be populated via AJAX
                    datasets: [{
                        label: 'Lessons Completed per Day',
                        data: [], // Will be populated via AJAX
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: false,
                        lineTension: 0.1,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Lesson Completion Trends'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Lessons'
                            }
                        }
                    }
                }
            });
        }

        // Student Progress Distribution (Pie Chart)
        const progressCtx = document.getElementById('student-progress-chart');
        if (progressCtx) {
            new Chart(progressCtx, {
                type: 'doughnut',
                data: {
                    labels: [], // Will be populated via AJAX
                    datasets: [{
                        label: 'Student Progress Distribution',
                        data: [], // Will be populated via AJAX
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255,99,132,1)',
                            'rgba(54,162,235,1)',
                            'rgba(255,206,86,1)',
                            'rgba(75,192,192,1)',
                            'rgba(153,102,255,1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                        },
                        title: {
                            display: true,
                            text: 'Student Progress Distribution'
                        }
                    }
                }
            });
        }

        // Enrollment Trends (Line Chart)
        const enrollmentCtx = document.getElementById('enrollment-trends-chart');
        if (enrollmentCtx) {
            new Chart(enrollmentCtx, {
                type: 'line',
                data: {
                    labels: [], // Will be populated via AJAX
                    datasets: [{
                        label: 'Weekly Enrollments',
                        data: [], // Will be populated via AJAX
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.4)',
                        fill: false,
                        lineTension: 0.1,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Enrollment Trends'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Enrollments'
                            }
                        }
                    }
                }
            });
        }
    }

    // Fetch analytics data via AJAX
    function fetchAnalyticsData() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'nctb_get_analytics_data',
                nonce: nctb_ajax_nonce
            },
            success: function(response) {
                if (response.success) {
                    updateCharts(response.data);
                    // Update average completion rate
                    if (response.data.avg_completion_rate !== undefined) {
                        $('#avg-completion-rate').text(response.data.avg_completion_rate + '%');
                    }
                }
            },
            error: function() {
                console.error('Failed to fetch analytics data');
                $('#avg-completion-rate').text('Error');
            }
        });
    }

    // Update charts with fetched data
    function updateCharts(data) {
        // Update lesson completion chart
        const completionChart = Chart.getChart('lesson-completion-chart');
        if (completionChart && data.lesson_completion) {
            completionChart.data.labels = data.lesson_completion.labels || [];
            completionChart.data.datasets[0].data = data.lesson_completion.datasets[0].data || [];
            completionChart.update();
        }

        // Update student progress chart
        const progressChart = Chart.getChart('student-progress-chart');
        if (progressChart && data.student_progress) {
            progressChart.data.labels = data.student_progress.labels || [];
            progressChart.data.datasets[0].data = data.student_progress.datasets[0].data || [];
            progressChart.update();
        }

        // Update enrollment trends chart
        const enrollmentChart = Chart.getChart('enrollment-trends-chart');
        if (enrollmentChart && data.enrollment_trends) {
            enrollmentChart.data.labels = data.enrollment_trends.labels || [];
            enrollmentChart.data.datasets[0].data = data.enrollment_trends.datasets[0].data || [];
            enrollmentChart.update();
        }
    }

    // Initialize when analytics section is visible
    function checkAnalyticsVisibility() {
        const analyticsSection = $('#analytics');
        if (analyticsSection.length && analyticsSection.is(':visible')) {
            initAnalyticsCharts();
            fetchAnalyticsData();
        }
    }

    // Check visibility on load and when tabs change
    checkAnalyticsVisibility();

    // Handle tab changes
    $('.dashboard-nav a, .teacher-dashboard-grid .dashboard-nav a').on('click', function(e) {
        // Small delay to allow tab switch
        setTimeout(checkAnalyticsVisibility, 100);
    });

    // Auto-refresh analytics data every 5 minutes
    setInterval(fetchAnalyticsData, 300000);
});