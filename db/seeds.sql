SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- ==========================================================
-- USERS
-- ==========================================================
INSERT INTO users (fullname, email, phone, avatar_url, password_hash, role, status)
VALUES
('Admin Hệ Thống', 'admin@example.com', '0900000000', NULL,
 '$2y$10$EqQkyeZD1bunDKPUeMFcPe1x8U4cHi.ovSPSd5Hp2WVoMBB85xKzi', 'ADMIN', 'ACTIVE'),

('Nguyễn Văn A', 'user1@example.com', '0911001100', NULL,
 '$2y$10$EqQkyeZD1bunDKPUeMFcPe1x8U4cHi.ovSPSd5Hp2WVoMBB85xKzi', 'USER', 'ACTIVE'),

('Trần Thị B', 'user2@example.com', '0933004400', NULL,
 '$2y$10$EqQkyeZD1bunDKPUeMFcPe1x8U4cHi.ovSPSd5Hp2WVoMBB85xKzi', 'USER', 'ACTIVE');


-- ==========================================================
-- CATEGORIES
-- ==========================================================
INSERT INTO categories (user_id, name, type, color, icon, description)
VALUES
(1, 'Lương', 'INCOME', '#00AA00', 'dollar', 'Thu nhập cố định hàng tháng'),
(1, 'Ăn uống', 'EXPENSE', '#FF5722', 'utensils', 'Chi phí ăn uống mỗi ngày'),
(1, 'Đi lại', 'EXPENSE', '#2196F3', 'car', 'Xăng xe, Grab, taxi'),
(1, 'Giải trí', 'EXPENSE', '#9C27B0', 'gamepad', 'Xem phim, cafe'),

(2, 'Lương', 'INCOME', '#00AA00', 'dollar', 'Thu nhập chính'),
(2, 'Đi chợ', 'EXPENSE', '#FF9800', 'shopping-bag', 'Mua thực phẩm'),

(3, 'Lương', 'INCOME', '#00AA00', 'dollar', 'Thu nhập mỗi tháng'),
(3, 'Du lịch', 'EXPENSE', '#3F51B5', 'plane', 'Đi chơi xa');


-- ==========================================================
-- TRANSACTIONS
-- ==========================================================
INSERT INTO transactions (user_id, category_id, amount, type, status, admin_note, transaction_date, note)
VALUES
(1, 1, 20000000, 'INCOME', 'COMPLETED', NULL, '2025-02-01', 'Lương tháng 2'),
(1, 2, 120000, 'EXPENSE', 'COMPLETED', NULL, '2025-02-02', 'Ăn trưa cơm tấm'),
(1, 3, 45000, 'EXPENSE', 'COMPLETED', NULL, '2025-02-03', 'Grab đến công ty'),
(1, 4, 90000, 'EXPENSE', 'PENDING', NULL, '2025-02-03', 'Cafe với bạn'),

(2, 5, 15000000, 'INCOME', 'COMPLETED', NULL, '2025-02-01', 'Lương'),
(2, 6, 250000, 'EXPENSE', 'COMPLETED', NULL, '2025-02-02', 'Đi siêu thị'),

(3, 7, 18000000, 'INCOME', 'COMPLETED', NULL, '2025-02-01', 'Lương'),
(3, 8, 5000000, 'EXPENSE', 'COMPLETED', NULL, '2025-02-04', 'Tour Vũng Tàu');


-- ==========================================================
-- BUDGETS
-- ==========================================================
INSERT INTO budgets (user_id, category_id, amount, period_start, period_end, note)
VALUES
(1, 2, 3000000, '2025-02-01', '2025-02-28', 'Hạn mức ăn uống tháng 2'),
(1, 3, 1000000, '2025-02-01', '2025-02-28', 'Hạn mức đi lại'),
(2, 6, 2500000, '2025-02-01', '2025-02-28', 'Hạn mức chợ'),
(3, 8, 8000000, '2025-02-01', '2025-02-28', 'Hạn mức du lịch');


-- ==========================================================
-- BILLS
-- ==========================================================
INSERT INTO bills (user_id, category_id, name, amount, due_date, paid_date, status, note)
VALUES
(1, 3, 'Tiền điện tháng 1', 350000, '2025-02-10', NULL, 'PENDING', 'Chờ đóng'),
(1, 3, 'Tiền nước tháng 1', 120000, '2025-02-12', NULL, 'OVERDUE', 'Quá hạn'),
(2, 6, 'Gói Internet FPT', 250000, '2025-02-15', '2025-02-16', 'PAID', NULL);


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
(2, 6, 'Mua thực phẩm hàng tuần', 'EXPENSE', 500000, 'Đi siêu thị', '2025-01-01', NULL, 'WEEKLY', '2025-02-09', '2025-02-02');


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
('default_currency', 'VND', 'string', 'Tiền tệ mặc định'),
('max_budget_limit', '50000000', 'number', 'Giới hạn ngân sách tối đa'),
('enable_notifications', 'true', 'boolean', 'Bật thông báo hệ thống');


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