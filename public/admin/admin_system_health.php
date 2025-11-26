<?php
require_once '../../config.php';
require_once 'admin_functions.php';
requireAdminLogin();

include 'partials/header.php';
include 'partials/sidebar.php';
include 'partials/navbar.php';
?>

<div class="container">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold">Tình trạng hệ thống</h2>
            <p class="text-muted text-sm">Giám sát sức khỏe và hiệu suất hệ thống</p>
        </div>
        <button class="btn btn-outline" onclick="refreshHealth()">
            <i class="fas fa-sync-alt mr-2"></i> Làm mới
        </button>
    </div>

    <!-- Overall Status -->
    <div class="card mb-6">
        <div class="p-6 text-center">
            <div class="w-20 h-20 rounded-full mx-auto mb-4 flex items-center justify-center" id="overallStatusIcon"
                style="width: 80px; height: 80px; background-color: #d1fae5;">
                <i class="fas fa-check-circle text-5xl" style="color: #10b981; font-size: 3rem;"></i>
            </div>
            <h3 class="text-2xl font-bold mb-2" id="overallStatusText">Hệ thống hoạt động bình thường</h3>
            <p class="text-muted" id="lastCheckTime">Kiểm tra lần cuối: <span id="lastCheck">--:--</span></p>
        </div>
    </div>

    <!-- System Components -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="card">
            <div class="p-4">
                <div class="flex justify-between items-start mb-3">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-server text-blue-600 text-xl"></i>
                    </div>
                    <span class="status-badge status-ok" id="apiStatus">OK</span>
                </div>
                <h4 class="font-bold mb-1">API Server</h4>
                <p class="text-sm text-muted">Trạng thái API</p>
                <div class="mt-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-muted">Response time:</span>
                        <span class="font-medium" id="apiResponseTime">--ms</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="p-4">
                <div class="flex justify-between items-start mb-3">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                        <i class="fas fa-database text-green-600 text-xl"></i>
                    </div>
                    <span class="status-badge status-ok" id="dbStatus">OK</span>
                </div>
                <h4 class="font-bold mb-1">Database</h4>
                <p class="text-sm text-muted">Kết nối CSDL</p>
                <div class="mt-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-muted">Connections:</span>
                        <span class="font-medium" id="dbConnections">--</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="p-4">
                <div class="flex justify-between items-start mb-3">
                    <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                        <i class="fas fa-memory text-purple-600 text-xl"></i>
                    </div>
                    <span class="status-badge status-ok" id="memoryStatus">OK</span>
                </div>
                <h4 class="font-bold mb-1">Memory</h4>
                <p class="text-sm text-muted">Bộ nhớ hệ thống</p>
                <div class="mt-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-muted">Usage:</span>
                        <span class="font-medium" id="memoryUsage">--%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="p-4">
                <div class="flex justify-between items-start mb-3">
                    <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                        <i class="fas fa-hdd text-orange-600 text-xl"></i>
                    </div>
                    <span class="status-badge status-ok" id="diskStatus">OK</span>
                </div>
                <h4 class="font-bold mb-1">Disk Space</h4>
                <p class="text-sm text-muted">Dung lượng ổ đĩa</p>
                <div class="mt-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-muted">Free:</span>
                        <span class="font-medium" id="diskFree">-- GB</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Info -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Thông tin hệ thống</h3>
            </div>
            <div class="p-4">
                <div class="system-info-grid">
                    <div class="system-info-item">
                        <span class="system-info-label">PHP Version</span>
                        <span class="system-info-value" id="phpVersion">--</span>
                    </div>
                    <div class="system-info-item">
                        <span class="system-info-label">Server Software</span>
                        <span class="system-info-value" id="serverSoftware">--</span>
                    </div>
                    <div class="system-info-item">
                        <span class="system-info-label">Operating System</span>
                        <span class="system-info-value" id="os">--</span>
                    </div>
                    <div class="system-info-item">
                        <span class="system-info-label">Server Time</span>
                        <span class="system-info-value" id="serverTime">--</span>
                    </div>
                    <div class="system-info-item">
                        <span class="system-info-label">Uptime</span>
                        <span class="system-info-value" id="uptime">--</span>
                    </div>
                    <div class="system-info-item">
                        <span class="system-info-label">Max Upload Size</span>
                        <span class="system-info-value" id="maxUpload">--</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Hoạt động gần đây</h3>
            </div>
            <div class="p-4">
                <div class="activity-list" id="recentActivity">
                    <!-- Loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-ok {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-warning {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-error {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .system-info-grid {
        display: grid;
        gap: 1rem;
    }

    .system-info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        background-color: #f9fafb;
        border-radius: 8px;
    }

    .system-info-label {
        color: #6b7280;
        font-size: 0.875rem;
    }

    .system-info-value {
        font-weight: 600;
        color: #111827;
    }

    .activity-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .activity-item {
        display: flex;
        gap: 1rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .activity-icon.success {
        background-color: #d1fae5;
        color: #10b981;
    }

    .activity-icon.warning {
        background-color: #fef3c7;
        color: #f59e0b;
    }

    .activity-icon.error {
        background-color: #fee2e2;
        color: #ef4444;
    }

    .activity-content {
        flex: 1;
    }

    .activity-title {
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.25rem;
    }

    .activity-time {
        font-size: 0.75rem;
        color: #9ca3af;
    }
</style>

<script>
    $(document).ready(function () {
        loadSystemHealth();

        // Auto refresh every 30 seconds
        setInterval(loadSystemHealth, 30000);
    });

    function refreshHealth() {
        showToast('info', 'Đang làm mới...');
        loadSystemHealth();
    }

    function loadSystemHealth() {
        $.get('/api/admin_data.php?action=get_system_health', function (response) {
            if (response.success) {
                updateHealthStatus(response.data);
            }
        });
    }

    function updateHealthStatus(data) {
        // Update last check time
        const now = new Date();
        $('#lastCheck').text(now.toLocaleTimeString('vi-VN'));

        // Update overall status
        const overallStatus = data.overall_status;
        const statusIcon = $('#overallStatusIcon');
        const statusText = $('#overallStatusText');

        if (overallStatus === 'healthy') {
            statusIcon.css('background-color', '#d1fae5');
            statusIcon.find('i').css('color', '#10b981').removeClass('fa-exclamation-triangle fa-times-circle').addClass('fa-check-circle');
            statusText.text('Hệ thống hoạt động bình thường');
        } else if (overallStatus === 'warning') {
            statusIcon.css('background-color', '#fef3c7');
            statusIcon.find('i').css('color', '#f59e0b').removeClass('fa-check-circle fa-times-circle').addClass('fa-exclamation-triangle');
            statusText.text('Hệ thống có cảnh báo');
        } else {
            statusIcon.css('background-color', '#fee2e2');
            statusIcon.find('i').css('color', '#ef4444').removeClass('fa-check-circle fa-exclamation-triangle').addClass('fa-times-circle');
            statusText.text('Hệ thống có lỗi');
        }

        // Update component statuses
        updateComponentStatus('api', data.api);
        updateComponentStatus('db', data.database);
        updateComponentStatus('memory', data.memory);
        updateComponentStatus('disk', data.disk);

        // Update system info
        $('#phpVersion').text(data.system_info.php_version);
        $('#serverSoftware').text(data.system_info.server_software);
        $('#os').text(data.system_info.os);
        $('#serverTime').text(data.system_info.server_time);
        $('#uptime').text(data.system_info.uptime);
        $('#maxUpload').text(data.system_info.max_upload);

        // Update recent activity
        renderRecentActivity(data.recent_activity);
    }

    function updateComponentStatus(component, data) {
        const statusBadge = $(`#${component}Status`);

        if (data.status === 'ok') {
            statusBadge.removeClass('status-warning status-error').addClass('status-ok').text('OK');
        } else if (data.status === 'warning') {
            statusBadge.removeClass('status-ok status-error').addClass('status-warning').text('WARNING');
        } else {
            statusBadge.removeClass('status-ok status-warning').addClass('status-error').text('ERROR');
        }

        // Update specific metrics
        if (component === 'api') {
            $('#apiResponseTime').text(data.response_time + 'ms');
        } else if (component === 'db') {
            $('#dbConnections').text(data.connections);
        } else if (component === 'memory') {
            $('#memoryUsage').text(data.usage + '%');
            if (data.usage > 80) {
                $(`#${component}Status`).removeClass('status-ok').addClass('status-warning').text('HIGH');
            }
        } else if (component === 'disk') {
            $('#diskFree').text(data.free_gb + ' GB');
            if (data.free_gb < 10) {
                $(`#${component}Status`).removeClass('status-ok').addClass('status-warning').text('LOW');
            }
        }
    }

    function renderRecentActivity(activities) {
        const container = $('#recentActivity');
        container.empty();

        if (activities.length === 0) {
            container.html('<p class="text-center text-muted py-4">Không có hoạt động gần đây</p>');
            return;
        }

        activities.forEach(activity => {
            const iconClass = activity.type === 'success' ? 'success' : activity.type === 'warning' ? 'warning' : 'error';
            const icon = activity.type === 'success' ? 'fa-check' : activity.type === 'warning' ? 'fa-exclamation-triangle' : 'fa-times';

            const html = `
            <div class="activity-item">
                <div class="activity-icon ${iconClass}">
                    <i class="fas ${icon}"></i>
                </div>
                <div class="activity-content">
                    <div class="activity-title">${activity.title}</div>
                    <div class="activity-time">${activity.time}</div>
                </div>
            </div>
        `;
            container.append(html);
        });
    }
</script>

<?php include 'partials/footer.php'; ?>