<div class="admin-dashboard">
    <div class="dashboard-grid">
        <div class="card stat">
            <div class="stat-title">Total Orders</div>
            <div class="stat-value"><?= isset($total_orders) ? number_format((int)$total_orders) : '-'; ?></div>
        </div>
        <div class="card stat">
            <div class="stat-title">Revenue</div>
            <div class="stat-value"><?= isset($revenue) ? htmlspecialchars($revenue) : '-'; ?></div>
        </div>
        <div class="card stat">
            <div class="stat-title">Active Users</div>
            <div class="stat-value"><?= isset($active_users) ? htmlspecialchars($active_users) : '-'; ?></div>
        </div>
        <div class="card stat">
            <div class="stat-title">Pending Orders</div>
            <div class="stat-value"><?= isset($pending_orders) ? htmlspecialchars($pending_orders) : '-'; ?></div>
        </div>
    </div>

    <div class="charts-row">
        <div class="chart-col">
                <div class="card recent chart-card">
                    <h3>Weekly Trend (orders)</h3>
                    <div class="muted" style="margin-bottom:8px;">Total: <?= isset($weekly_total) ? number_format((int)$weekly_total) : '0'; ?></div>
                    <div class="chart-container" style="height:320px;">
                        <canvas id="weeklyTrendChart"></canvas>
                    </div>
                </div>
        </div>
        <div class="chart-col">
            <div class="card recent chart-card">
                <h3>Monthly Trend (last 12 months)</h3>
                <div class="muted" style="margin-bottom:8px;">Total (12m): <?= isset($monthly_total) ? number_format((int)$monthly_total) : '0'; ?></div>
                <div class="chart-container" style="height:320px;">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card recent mt-3">
        <h3>Produk Es Krim Unggulan (Pie Chart)</h3>
        <div class="muted" style="margin-bottom:8px;">Total sold (all featured): <?= isset($featured_total) ? number_format((int)$featured_total) : '0'; ?></div>
        <div class="d-flex" style="gap:18px;align-items:center;">
            <div style="flex:1;max-width:420px;">
                <div class="chart-container" style="height:320px;">
                    <canvas id="featuredPieChart"></canvas>
                </div>
            </div>
            <div style="flex:1;">
                <ul class="list-unstyled">
                    <?php if (isset($featured_labels) && is_array($featured_labels)): ?>
                        <?php foreach ($featured_labels as $idx => $label): ?>
                            <li style="margin-bottom:8px;">
                                <strong><?= htmlspecialchars($label); ?></strong>
                                <span class="muted"> — <?= isset($featured_values[$idx]) ? (int)$featured_values[$idx] : 0; ?> sold</span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>No featured data</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function(){
        // Minimal chart options shared
        var minimalOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false }
            },
            elements: {
                point: { radius: 0 }
            },
            scales: {
                x: { grid: { display: false }, ticks: { display: false }, border: { display: false } },
                y: { grid: { display: false }, ticks: { display: false }, border: { display: false }, beginAtZero: true }
            },
            layout: { padding: 0 }
        };

        // Weekly (line) - flat 2D color palette
        var wLabels = <?php echo isset($weekly_labels_display) ? json_encode($weekly_labels_display) : (isset($weekly_labels) ? json_encode($weekly_labels) : '[]'); ?>;
        var wValues = <?php echo isset($weekly_values) ? json_encode($weekly_values) : '[]'; ?>;
        var wCtx = document.getElementById('weeklyTrendChart');
        if (wCtx) {
            new Chart(wCtx, {
                type: 'line',
                data: {
                    labels: wLabels,
                    datasets: [{
                        data: wValues,
                        fill: true,
                        backgroundColor: '#e6f0ff',
                        borderColor: '#1f64d3',
                        borderWidth: 2,
                        pointRadius: 0,
                        tension: 0.2
                    }]
                },
                options: Object.assign({}, minimalOptions, {
                    scales: {
                        x: { grid: { display: false }, ticks: { display: true, color: '#6b6b6b', font: { size: 11 } }, border: { display: false } },
                        y: { grid: { display: false }, ticks: { display: true, color: '#6b6b6b', font: { size: 11 } }, border: { display: false }, beginAtZero: true }
                    }
                })
            });
        }

        // Monthly (bar) - flat 2D coral palette
        var mLabels = <?php echo isset($monthly_labels_display) ? json_encode($monthly_labels_display) : (isset($monthly_labels) ? json_encode($monthly_labels) : '[]'); ?>;
        var mValues = <?php echo isset($monthly_values) ? json_encode($monthly_values) : '[]'; ?>;
        var mCtx = document.getElementById('monthlyTrendChart');
        if (mCtx) {
            new Chart(mCtx, {
                type: 'bar',
                data: {
                    labels: mLabels,
                    datasets: [{
                        data: mValues,
                        backgroundColor: '#ffb59e',
                        borderColor: '#ff7a5c',
                        borderWidth: 0
                    }]
                },
                options: Object.assign({}, minimalOptions, {
                    scales: {
                        x: { grid: { display: false }, ticks: { display: true, color: '#6b6b6b', font: { size: 11 } }, border: { display: false } },
                        y: { grid: { display: false }, ticks: { display: true, color: '#6b6b6b', font: { size: 11 } }, border: { display: false }, beginAtZero: true }
                    }
                })
            });
        }

        // Featured products pie chart
        var pLabels = <?php echo isset($featured_labels) ? json_encode($featured_labels) : '[]'; ?>;
        var pValues = <?php echo isset($featured_values) ? json_encode($featured_values) : '[]'; ?>;
        var pCtx = document.getElementById('featuredPieChart');
        if (pCtx) {
            var colors = ['#ff7a5c','#ffb59e','#ffd9a6','#a8d5d1','#8fd3c7','#b3c7ff'];
            new Chart(pCtx, {
                type: 'pie',
                data: { labels: pLabels, datasets: [{ data: pValues, backgroundColor: colors.slice(0, pValues.length) }] },
                options: Object.assign({}, minimalOptions, { plugins: { tooltip: { enabled: true }, legend: { display: true, position: 'right', labels: { boxWidth:12 } } } })
            });
        }
    })();
</script>

<!-- debug block removed -->
