SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET collation_connection = 'utf8mb4_unicode_ci';

-- ==========================================================
-- USERS
-- ==========================================================
INSERT INTO users (fullname, email, phone, avatar_url, password_hash, role, status)
VALUES
('Admin Hệ Thống', 'admin@example.com', '0900000000', NULL,
 '$2y$10$m1xWh8B4mVNKS0clxoREeesIORONxrPx2D8QTq8WH3esy3T6q74z2', 'ADMIN', 'ACTIVE'),

('Nguyễn Văn A', 'user1@example.com', '0911001100', NULL,
 '$2y$10$m1xWh8B4mVNKS0clxoREeesIORONxrPx2D8QTq8WH3esy3T6q74z2', 'USER', 'ACTIVE'),

('Trần Thị B', 'user2@example.com', '0933004400', NULL,
 '$2y$10$m1xWh8B4mVNKS0clxoREeesIORONxrPx2D8QTq8WH3esy3T6q74z2', 'USER', 'ACTIVE');

-- ==========================================================
-- CATEGORIES (Global system categories)
-- ==========================================================
INSERT INTO categories (user_id, name, type, color, icon, description)
VALUES
(NULL, 'Lương', 'INCOME', '#00AA00', 'fa-dollar-sign', 'Thu nhập cố định hàng tháng'),
(NULL, 'Ăn uống', 'EXPENSE', '#FF5722', 'fa-utensils', 'Chi phí ăn uống mỗi ngày'),
(NULL, 'Di chuyển', 'EXPENSE', '#2196F3', 'fa-car', 'Xăng xe, Grab, taxi'),
(NULL, 'Giải trí', 'EXPENSE', '#9C27B0', 'fa-gamepad', 'Xem phim, cafe'),
(NULL, 'Nhà cửa', 'EXPENSE', '#795548', 'fa-home', 'Tiền thuê nhà, điện nước'),
(NULL, 'Sức khỏe', 'EXPENSE', '#E91E63', 'fa-heart', 'Khám chữa bệnh, thuốc men'),
(NULL, 'Giáo dục', 'EXPENSE', '#9C27B0', 'fa-graduation-cap', 'Học phí, sách vở'),
(NULL, 'Mua sắm', 'EXPENSE', '#FF9800', 'fa-shopping-cart', 'Quần áo, đồ dùng cá nhân'),
(NULL, 'Du lịch', 'EXPENSE', '#3F51B5', 'fa-plane', 'Đi chơi xa, nghỉ dưỡng'),
(NULL, 'Khác', 'EXPENSE', '#607D8B', 'fa-wallet', 'Các chi phí khác');

-- ==========================================================
-- TRANSACTIONS (Sample data using global categories)
-- ==========================================================
INSERT INTO transactions (user_id, category_id, amount, type, status, admin_note, transaction_date, note)
VALUES
(1, 1, 20000000, 'INCOME', 'COMPLETED', NULL, '2025-02-01', 'Lương tháng 2'),
(1, 2, 120000, 'EXPENSE', 'COMPLETED', NULL, '2025-02-02', 'Ăn trưa cơm tấm'),
(1, 3, 45000, 'EXPENSE', 'COMPLETED', NULL, '2025-02-03', 'Grab đến công ty'),
(1, 4, 90000, 'EXPENSE', 'PENDING', NULL, '2025-02-03', 'Cafe với bạn'),

(2, 1, 15000000, 'INCOME', 'COMPLETED', NULL, '2025-02-01', 'Lương'),
(2, 2, 250000, 'EXPENSE', 'COMPLETED', NULL, '2025-02-02', 'Đi siêu thị'),

(3, 1, 18000000, 'INCOME', 'COMPLETED', NULL, '2025-02-01', 'Lương'),
(3, 9, 5000000, 'EXPENSE', 'COMPLETED', NULL, '2025-02-04', 'Tour Vũng Tàu');

-- ==========================================================
-- BUDGETS
-- ==========================================================
INSERT INTO budgets (user_id, category_id, amount, period_start, period_end, note)
VALUES
(1, 2, 3000000, '2025-02-01', '2025-02-28', 'Hạn mức ăn uống tháng 2'),
(1, 3, 1000000, '2025-02-01', '2025-02-28', 'Hạn mức đi lại'),
(2, 2, 2500000, '2025-02-01', '2025-02-28', 'Hạn mức chợ'),
(3, 9, 8000000, '2025-02-01', '2025-02-28', 'Hạn mức du lịch');

-- ==========================================================
-- BILLS
-- ==========================================================
INSERT INTO bills (user_id, category_id, name, amount, due_date, paid_date, status, note)
VALUES
(1, 3, 'Tiền điện tháng 1', 350000, '2025-02-10', NULL, 'PENDING', 'Chờ đóng'),
(1, 5, 'Tiền nước tháng 1', 120000, '2025-02-12', NULL, 'OVERDUE', 'Quá hạn'),
(2, 2, 'Gói Internet FPT', 250000, '2025-02-15', '2025-02-16', 'PAID', NULL);

-- ==========================================================
-- GOALS
-- ==========================================================
INSERT INTO goals (user_id, name, target_amount, current_amount, start_date, deadline, description, status)
VALUES
(1, 'Mua Macbook', 30000000, 5000000, '2025-01-01', '2025-06-01', 'Tiết kiệm mua laptop mới', 'ACTIVE'),
(2, 'Du lịch Nhật Bản', 50000000, 10000000, '2025-01-01', '2025-12-31', 'Đi Nhật mùa thu', 'ACTIVE');

-- ==========================================================
-- RECURRING TRANSACTIONS
-- ==========================================================
INSERT INTO recurring_transactions (user_id, category_id, name, type, amount, note, start_date, end_date, frequency, next_run_date, last_run_date)
VALUES
(1, 2, 'Ăn sáng', 'EXPENSE', 30000, 'Bánh mì + cafe', '2025-01-01', NULL, 'DAILY', '2025-02-05', '2025-02-04'),
(2, 2, 'Mua thực phẩm hàng tuần', 'EXPENSE', 500000, 'Đi siêu thị', '2025-01-01', NULL, 'WEEKLY', '2025-02-09', '2025-02-02');

-- ==========================================================
-- NOTIFICATIONS
-- ==========================================================
INSERT INTO notifications (user_id, type, title, message, link_url)
VALUES
(1, 'info', 'Chào mừng trở lại!', 'Bạn có 3 giao dịch chưa xem.', '/transactions'),
(1, 'warning', 'Ngân sách ăn uống vượt mức', 'Bạn đã dùng 95% hạn mức.', '/budgets'),
(2, 'success', 'Đã hoàn thành thanh toán!', 'Hóa đơn Internet đã được ghi nhận.', '/bills');

-- ==========================================================
-- USER SETTINGS
-- ==========================================================
INSERT INTO user_settings (user_id, default_currency, monthly_budget_limit, notify_email, notify_push)
VALUES
(1, 'VND', 15000000, 1, 1),
(2, 'VND', 12000000, 1, 0),
(3, 'VND', 10000000, 0, 1);

-- ==========================================================
-- SYSTEM SETTINGS
-- ==========================================================
INSERT INTO system_settings (setting_key, setting_value, setting_type, description)
VALUES
('app_name', 'MoneyManager', 'string', 'Tên ứng dụng'),
('timezone', 'Asia/Ho_Chi_Minh', 'string', 'Múi giờ hệ thống'),
('currency_format', 'vnd', 'string', 'Định dạng tiền tệ'),
('language', 'vi', 'string', 'Ngôn ngữ mặc định'),
('default_budget', '10000000', 'number', 'Ngân sách mặc định cho user mới'),
('warning_threshold', '80', 'number', 'Ngưỡng cảnh báo ngân sách (%)'),
('exceeded_threshold', '100', 'number', 'Ngưỡng vượt ngân sách (%)'),
('session_timeout', '60', 'number', 'Thời gian phiên (phút)'),
('min_password_length', '8', 'number', 'Độ dài mật khẩu tối thiểu'),
('bill_reminder_days', '7', 'number', 'Số ngày nhắc trước khi đến hạn'),
('enable_user_registration', 'true', 'boolean', 'Cho phép đăng ký tài khoản mới'),
('enable_email_notifications', 'true', 'boolean', 'Bật thông báo email'),
('enable_push_notifications', 'false', 'boolean', 'Bật thông báo push'),
('maintenance_mode', 'false', 'boolean', 'Chế độ bảo trì'),
('default_currency', 'VND', 'string', 'Tiền tệ mặc định'),
('enable_notifications', 'true', 'boolean', 'Bật thông báo hệ thống'),
('auto_reset_budget', 'true', 'boolean', 'Tự động reset ngân sách hàng tháng'),
('email_budget_alert', 'true', 'boolean', 'Gửi email cảnh báo ngân sách'),
('email_bill_reminder', 'true', 'boolean', 'Gửi email nhắc nhở hóa đơn'),
('email_goal_achieved', 'true', 'boolean', 'Gửi email khi đạt mục tiêu'),
('email_monthly_report', 'false', 'boolean', 'Gửi báo cáo tháng qua qua email'),
('feature_budget_planner', 'true', 'boolean', 'Bật tính năng lập kế hoạch ngân sách'),
('feature_recurring', 'true', 'boolean', 'Bật tính năng giao dịch định kỳ'),
('feature_goals', 'true', 'boolean', 'Bật tính năng mục tiêu tiết kiệm'),
('feature_bill_calendar', 'true', 'boolean', 'Bật tính năng lịch hóa đơn'),
('require_strong_password', 'true', 'boolean', 'Yêu cầu mật khẩu mạnh'),
('enable_2fa', 'false', 'boolean', 'Bật xác thực 2 yếu tố');

-- ==========================================================
-- SUPPORT TICKETS
-- ==========================================================
INSERT INTO support_tickets (user_id, subject, category, status, priority, is_read)
VALUES
(1, 'Không đăng nhập được', 'bug', 'open', 'high', 0),
(2, 'Đề xuất thêm chế độ tối', 'feature', 'answered', 'medium', 1),
(3, 'Hỏi về cách tạo ngân sách', 'question', 'open', 'low', 0);

-- ==========================================================
-- SUPPORT MESSAGES
-- ==========================================================
INSERT INTO support_messages (ticket_id, sender_id, sender_type, message)
VALUES
(1, 1, 'user', 'Mình bị lỗi khi đăng nhập, giúp mình với.'),
(1, 1, 'admin', 'Bạn thử đặt lại mật khẩu giúp mình nhé!'),
(2, 2, 'user', 'App nên có dark mode.'),
(2, 1, 'admin', 'Cảm ơn bạn, team sẽ xem xét.'),
(3, 3, 'user', 'Mình muốn tạo ngân sách theo tuần.'),
(3, 1, 'admin', 'Bạn vào mục "Ngân sách" → "Tạo mới" nhé.');