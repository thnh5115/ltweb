-- Seed data for MoneyManager (development/testing)
-- Assumes schema from db/schema.sql has been applied

-- Users
INSERT INTO users (id, fullname, email, password_hash, role, phone, avatar_url) VALUES
    (1, 'Super Admin', 'admin@test.com', '$2y$12$RDIAPI4lz/3LmcHOYlP4g.utgqj4EXAmgoGacIQbBDgKDzbsD7FXi', 'ADMIN', '0900000000', NULL),
    (2, 'Nguyen Van A', 'user@test.com', '$2y$12$RDIAPI4lz/3LmcHOYlP4g.utgqj4EXAmgoGacIQbBDgKDzbsD7FXi', 'USER', '0911111111', NULL)
ON DUPLICATE KEY UPDATE
    fullname = VALUES(fullname),
    password_hash = VALUES(password_hash),
    role = VALUES(role),
    phone = VALUES(phone),
    avatar_url = VALUES(avatar_url);

-- User settings
INSERT INTO user_settings (user_id, default_currency, monthly_budget_limit, notify_email, notify_push) VALUES
    (1, 'VND', NULL, 1, 1),
    (2, 'VND', 10000000, 1, 1)
ON DUPLICATE KEY UPDATE
    default_currency = VALUES(default_currency),
    monthly_budget_limit = VALUES(monthly_budget_limit),
    notify_email = VALUES(notify_email),
    notify_push = VALUES(notify_push);

-- Categories (for user 2)
INSERT INTO categories (id, user_id, name, type, color, icon, spending_limit) VALUES
    (1, 2, 'Ăn uống', 'EXPENSE', '#EF4444', 'fa-utensils', NULL),
    (2, 2, 'Di chuyển', 'EXPENSE', '#F59E0B', 'fa-car', NULL),
    (3, 2, 'Mua sắm', 'EXPENSE', '#EC4899', 'fa-shopping-bag', NULL),
    (4, 2, 'Hóa đơn', 'EXPENSE', '#8B5CF6', 'fa-file-invoice', NULL),
    (5, 2, 'Giải trí', 'EXPENSE', '#06B6D4', 'fa-gamepad', NULL),
    (6, 2, 'Lương', 'INCOME', '#10B981', 'fa-money-bill-wave', NULL),
    (7, 2, 'Thưởng', 'INCOME', '#3B82F6', 'fa-gift', NULL)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    color = VALUES(color),
    icon = VALUES(icon);

-- Transactions (for user 2)
INSERT INTO transactions (user_id, category_id, amount, type, transaction_date, note) VALUES
    (2, 1, 50000, 'EXPENSE', '2024-11-25', 'Ăn trưa'),
    (2, 6, 15000000, 'INCOME', '2024-11-01', 'Lương tháng 11'),
    (2, 2, 30000, 'EXPENSE', '2024-11-26', 'Grab đi làm'),
    (2, 3, 250000, 'EXPENSE', '2024-11-24', 'Mua áo thun'),
    (2, 7, 1000000, 'INCOME', '2024-11-20', 'Thưởng dự án'),
    (2, 5, 200000, 'EXPENSE', '2024-11-23', 'Xem phim'),
    (2, 4, 500000, 'EXPENSE', '2024-11-15', 'Tiền điện'),
    (2, 1, 80000, 'EXPENSE', '2024-11-22', 'Ăn tối')
ON DUPLICATE KEY UPDATE
    amount = VALUES(amount),
    note = VALUES(note);

-- Budgets (for user 2, month 11/2024)
INSERT INTO budgets (user_id, category_id, amount, period_start, period_end, note, status) VALUES
    (2, 1, 2000000, '2024-11-01', '2024-11-30', 'Ngân sách ăn uống tháng 11', 'ACTIVE'),
    (2, 2, 1000000, '2024-11-01', '2024-11-30', 'Ngân sách di chuyển tháng 11', 'ACTIVE'),
    (2, 3, 1500000, '2024-11-01', '2024-11-30', 'Ngân sách mua sắm tháng 11', 'ACTIVE'),
    (2, 5, 500000,  '2024-11-01', '2024-11-30', 'Ngân sách giải trí tháng 11', 'ACTIVE')
ON DUPLICATE KEY UPDATE
    amount = VALUES(amount),
    note = VALUES(note),
    status = VALUES(status);

-- Bills (for user 2)
INSERT INTO bills (user_id, category_id, name, amount, due_date, paid_date, status, note) VALUES
    (2, 4, 'Tiền điện', 700000, '2024-11-25', NULL, 'PENDING', 'Hóa đơn điện tháng 11'),
    (2, 4, 'Tiền nước', 300000, '2024-11-28', NULL, 'PENDING', 'Hóa đơn nước tháng 11'),
    (2, 3, 'Internet', 250000, '2024-11-20', '2024-11-20', 'PAID', 'Cước internet')
ON DUPLICATE KEY UPDATE
    amount = VALUES(amount),
    status = VALUES(status),
    paid_date = VALUES(paid_date),
    note = VALUES(note);

-- Goals (for user 2)
INSERT INTO goals (user_id, name, target_amount, current_amount, start_date, deadline, description, status) VALUES
    (2, 'Tiết kiệm mua laptop', 20000000, 5000000, '2024-11-01', '2025-03-31', 'Mua laptop mới phục vụ công việc', 'ACTIVE'),
    (2, 'Quỹ du lịch Tết', 10000000, 3000000, '2024-10-01', '2025-01-15', 'Du lịch đầu năm', 'ACTIVE')
ON DUPLICATE KEY UPDATE
    target_amount = VALUES(target_amount),
    current_amount = VALUES(current_amount),
    deadline = VALUES(deadline),
    status = VALUES(status),
    description = VALUES(description);

-- Recurring transactions (for user 2)
INSERT INTO recurring_transactions (user_id, category_id, name, type, amount, note, start_date, end_date, frequency, next_run_date, last_run_date, status) VALUES
    (2, 4, 'Tiền điện định kỳ', 'EXPENSE', 700000, 'Thanh toán tiền điện mỗi tháng', '2024-11-01', NULL, 'MONTHLY', '2024-12-01', '2024-11-01', 'ACTIVE'),
    (2, 6, 'Lương hàng tháng', 'INCOME', 15000000, 'Nhận lương cố định', '2024-11-01', NULL, 'MONTHLY', '2024-12-01', '2024-11-01', 'ACTIVE')
ON DUPLICATE KEY UPDATE
    amount = VALUES(amount),
    next_run_date = VALUES(next_run_date),
    last_run_date = VALUES(last_run_date),
    status = VALUES(status),
    note = VALUES(note);

-- Notifications (for user 2)
INSERT INTO notifications (user_id, type, title, message, link_url, is_read, read_at) VALUES
    (2, 'reminder', 'Nhắc nhở hóa đơn', 'Đến hạn thanh toán tiền điện ngày 25/11', '/public/user/bill_calendar.php', 0, NULL),
    (2, 'success', 'Đã đạt 25% mục tiêu laptop', 'Bạn đã tiết kiệm 5,000,000 / 20,000,000', '/public/user/goals.php', 0, NULL),
    (2, 'info', 'Giao dịch định kỳ', 'Đã thực thi lương hàng tháng', '/public/user/recurring_transactions.php', 1, NOW())
ON DUPLICATE KEY UPDATE
    is_read = VALUES(is_read),
    read_at = VALUES(read_at),
    link_url = VALUES(link_url),
    message = VALUES(message);

-- System settings (default values for admin configuration)
INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES
    -- General
    ('app_name', 'MoneyManager', 'string', 'Tên ứng dụng'),
    ('timezone', 'Asia/Ho_Chi_Minh', 'string', 'Múi giờ mặc định'),
    ('currency_format', 'vnd', 'string', 'Định dạng tiền tệ'),
    ('language', 'vi', 'string', 'Ngôn ngữ mặc định'),
    
    -- Budget
    ('default_budget', '10000000', 'number', 'Ngân sách mặc định cho user mới'),
    ('warning_threshold', '80', 'number', 'Ngưỡng cảnh báo (%)'),
    ('exceeded_threshold', '100', 'number', 'Ngưỡng vượt ngân sách (%)'),
    ('auto_reset_budget', '1', 'boolean', 'Tự động reset ngân sách hàng tháng'),
    
    -- Notifications
    ('email_budget_alert', '1', 'boolean', 'Email cảnh báo vượt ngân sách'),
    ('email_bill_reminder', '1', 'boolean', 'Email nhắc nhở hóa đơn'),
    ('email_goal_achieved', '1', 'boolean', 'Email thông báo đạt mục tiêu'),
    ('email_monthly_report', '0', 'boolean', 'Email báo cáo tháng'),
    ('bill_reminder_days', '3', 'number', 'Số ngày nhắc trước hóa đơn'),
    
    -- Features
    ('feature_budget_planner', '1', 'boolean', 'Bật tính năng kế hoạch ngân sách'),
    ('feature_recurring', '1', 'boolean', 'Bật tính năng giao dịch định kỳ'),
    ('feature_goals', '1', 'boolean', 'Bật tính năng mục tiêu tiết kiệm'),
    ('feature_bill_calendar', '1', 'boolean', 'Bật tính năng lịch hóa đơn'),
    
    -- Security
    ('session_timeout', '60', 'number', 'Thời gian hết hạn phiên (phút)'),
    ('min_password_length', '8', 'number', 'Độ dài mật khẩu tối thiểu'),
    ('require_strong_password', '1', 'boolean', 'Yêu cầu mật khẩu mạnh'),
    ('enable_2fa', '0', 'boolean', 'Bật xác thực 2 yếu tố'),
    ('log_failed_logins', '1', 'boolean', 'Ghi log đăng nhập thất bại')
ON DUPLICATE KEY UPDATE
    setting_value = VALUES(setting_value),
    description = VALUES(description);

-- Support tickets (for user 2)
INSERT INTO support_tickets (user_id, subject, category, status, is_read) VALUES
    (2, 'Lỗi không thể thêm giao dịch', 'bug', 'open', 0),
    (2, 'Đề xuất tính năng xuất báo cáo Excel', 'feature', 'answered', 1),
    (2, 'Làm sao để xóa danh mục?', 'question', 'closed', 1)
ON DUPLICATE KEY UPDATE
    subject = VALUES(subject);

-- Support messages (for tickets above)
INSERT INTO support_messages (ticket_id, sender_id, sender_type, message) VALUES
    (1, 2, 'user', 'Tôi không thể thêm giao dịch mới. Khi bấm nút Lưu thì không có gì xảy ra.'),
    (2, 2, 'user', 'Tôi muốn xuất báo cáo chi tiêu ra file Excel để dễ phân tích.'),
    (2, 1, 'admin', 'Cảm ơn bạn đã đề xuất. Chúng tôi sẽ cân nhắc thêm tính năng này trong phiên bản sau.'),
    (3, 2, 'user', 'Tôi muốn xóa một danh mục nhưng không tìm thấy nút xóa.'),
    (3, 1, 'admin', 'Bạn vào trang Danh mục, click vào danh mục cần xóa, sau đó chọn "Xóa" ở góc phải.')
ON DUPLICATE KEY UPDATE
    message = VALUES(message);
