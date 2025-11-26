API_REPORT_FROM_FE.md
All frontend PHP/JS pages use jQuery AJAX to /api/data.php (user), /api/auth.php (auth), and /api/admin_data.php (admin). Most calls pass an action parameter to select the operation; GET uses action in query, POST uses hidden action in form data.

Module: Auth (User + Admin)
Method	Endpoint	Purpose	Request (fields)	Response (fields used by FE)	Used by FE files
POST	/api/auth.php	Login (user/admin)	action=login; email (or username); password	success (bool), message, data.redirect (URL)	public/user/login.php
POST	/api/auth.php	Register (user)	action=register; fullname; email; password; confirm_password	success, message	public/user/register.php
POST	/api/auth.php	Forgot password	action=forgot_password; email	success, message	public/user/forgot_password.php
POST	/api/auth.php	Reset password	action=reset_password; token (hidden from URL); password; confirm_password	success, message; on success FE redirects to login	public/user/reset_password.php
GET	/public/user/logout.php	Logout user	—	Redirect to login	public/user/logout.php
GET	/public/admin/admin_logout.php	Logout admin	—	Redirect to /public/user/login.php	public/admin/admin_logout.php
Module: User Dashboard & Statistics
Method	Endpoint	Purpose	Request (fields)	Response (fields used by FE)	Used by FE files
GET	/api/data.php	Dashboard stats	action=dashboard_stats	data.income, data.expense, data.balance, data.transaction_count	user/dashboard.php
GET	/api/data.php	Dashboard recent transactions	action=recent_transactions	data[] (id, amount, date, note, category_name/icon/color, type)	user/dashboard.php
GET	/api/data.php	Dashboard charts (line/pie)	action=chart_data	data.pie (category => {amount,color}); data.line {labels[], income[], expense[]}	user/dashboard.php
GET	/api/data.php	Statistics summary	action=statistics_summary; period	data.totals (income, expense, balance), counts	user/statistics.php
GET	/api/data.php	Statistics charts	action=statistics_charts; period	data.line {labels[], income[], expense[]}; data.area/bar (per file expectations)	user/statistics.php
GET	/api/data.php	Top categories	action=top_categories; period	data[] (category, amount, percent, color, type)	user/statistics.php
Module: User Categories
Method	Endpoint	Purpose	Request (fields)	Response (fields used by FE)	Used by FE files
GET	/api/data.php	List categories	action=get_categories; optional search, type	data[] (id, name, type, color, icon, limit, usage_count)	user/categories.php, others needing options
GET	/api/data.php	Get category detail	action=get_category; id	data {id, name, type, color, icon, limit}	user/categories.php
POST	/api/data.php	Add category	action=add_category; name; type (income/expense); color; icon; limit (optional)	success, message	user/categories.php
POST	/api/data.php	Update category	action=update_category; id; name; type; color; icon; limit	success, message	user/categories.php
POST	/api/data.php	Delete category	action=delete_category; id	success, message	user/categories.php
Module: User Transactions
Method	Endpoint	Purpose	Request (fields)	Response (fields used by FE)	Used by FE files
GET	/api/data.php	List transactions with filters	action=get_transactions; search; category; type; date	data[] (id, date, category_name/icon/color, note, type, amount)	user/transactions.php
GET	/api/data.php	Get single transaction	action=get_transaction; id	data {id, type, amount, category_id, date, note}	user/transactions.php
POST	/api/data.php	Add transaction	action=add_transaction; type; amount; category_id; date; note	success, message	user/transactions.php
POST	/api/data.php	Edit transaction	action=edit_transaction; id; type; amount; category_id; date; note	success, message	user/transactions.php
POST	/api/data.php	Delete transaction	action=delete_transaction; id	success, message	user/transactions.php
GET	/api/data.php	List categories (for selects)	action=get_categories	data[] (id,name,...)	user/transactions.php
Module: User Budgets
Method	Endpoint	Purpose	Request (fields)	Response (fields used by FE)	Used by FE files
GET	/api/data.php	List budgets	action=get_budgets	data.budgets[] (id, category, category_id, color, amount, spent, note); data.summary {total_budget, total_spent}	user/budget_planner.php
GET	/api/data.php	Get budget detail	action=get_budget; id	data {id, category_id, amount, note}	user/budget_planner.php
POST	/api/data.php	Save budget (create/update)	action=save_budget; id (optional for update); category_id; amount; note	success, message	user/budget_planner.php
POST	/api/data.php	Delete budget	action=delete_budget; id	success, message	user/budget_planner.php
GET	/api/data.php	List categories (for select)	action=get_categories	data[]	user/budget_planner.php
Module: User Bills (Bill Calendar)
Method	Endpoint	Purpose	Request (fields)	Response (fields used by FE)	Used by FE files
GET	/api/data.php	List bills	action=get_bills	data[] (id, title, amount, due_date, status, category,color)	user/bill_calendar.php
GET	/api/data.php	List categories (select)	action=get_categories	data[]	user/bill_calendar.php
POST	/api/data.php	Save bill (create/update)	action=save_bill; id (optional); title; amount; category_id; due_date; note; repeat (optional per form)	success, message	user/bill_calendar.php
POST	/api/data.php	Mark bill paid	action=mark_bill_paid; id	success, message	user/bill_calendar.php
POST	/api/data.php	Delete bill	action=delete_bill; id	success, message	user/bill_calendar.php
Module: User Goals
Method	Endpoint	Purpose	Request (fields)	Response (fields used by FE)	Used by FE files
GET	/api/data.php	List goals	action=get_goals; filter (all/active/completed)	data[] (id, title, target_amount, current_amount, deadline, status, progress, icon/color)	user/goals.php
GET	/api/data.php	Get goal detail	action=get_goal; id	data {id, title, target_amount, current_amount, deadline, note, icon, color, status}	user/goals.php
POST	/api/data.php	Save goal (create/update)	action=save_goal; id (optional); title; target_amount; deadline; note; icon; color	success, message	user/goals.php
POST	/api/data.php	Delete goal	action=delete_goal; id	success, message	user/goals.php
POST	/api/data.php	Add savings to goal	action=add_savings; id (goal_id); amount	success, message; FE refreshes list/progress	user/goals.php
Module: User Recurring Transactions
Method	Endpoint	Purpose	Request (fields)	Response (fields used by FE)	Used by FE files
GET	/api/data.php	List recurring transactions	action=get_recurring_transactions	data[] (id, title/description, amount, category_name/color, frequency, next_date, active)	user/recurring_transactions.php
GET	/api/data.php	Get recurring transaction detail	action=get_recurring; id	data {id, type, amount, category_id, start_date, frequency, note, active}	user/recurring_transactions.php
GET	/api/data.php	List categories (select)	action=get_categories	data[]	user/recurring_transactions.php
POST	/api/data.php	Save recurring transaction (create/edit)	action=save_recurring; id (optional); type; amount; category_id; start_date; frequency; note; active checkbox	success, message	user/recurring_transactions.php
POST	/api/data.php	Toggle recurring status	action=toggle_recurring_status; id	success, message	user/recurring_transactions.php
POST	/api/data.php	Delete recurring transaction	action=delete_recurring; id	success, message	user/recurring_transactions.php
Module: User Notifications
Method	Endpoint	Purpose	Request (fields)	Response (fields used by FE)	Used by FE files
GET	/api/data.php	List notifications	action=get_notifications; filter (all/unread); type/date?	data[] (id, title, content, time, read, type/icon)	user/notifications.php
POST	/api/data.php	Mark single notification read	action=mark_notification_read; id	success, message	user/notifications.php
POST	/api/data.php	Mark all notifications read	action=mark_all_notifications_read	success, message	user/notifications.php
Module: User Profile
Method	Endpoint	Purpose	Request (fields)	Response (fields used by FE)	Used by FE files
GET	/api/data.php	User stats	action=user_stats	data {transactions, goals, budgets, recurring}	user/profile.php
POST	/api/data.php	Update profile info	action=update_profile; fullname; email; phone; address; dob	success, message	user/profile.php
POST	/api/data.php	Change password	action=change_password; current_password; new_password; confirm	success, message	user/profile.php
Module: Admin Dashboard & Analytics
Method	Endpoint	Purpose	Request (fields)	Response (fields used by FE)	Used by FE files
GET	/api/admin_data.php	Dashboard stats	action=dashboard_stats	data {total_users,total_transactions,total_expense,total_income,total_categories}	admin/admin_dashboard.php
GET	/api/admin_data.php	Dashboard charts	action=chart_data	data.line {labels[], data[]}; data.pie {income, expense}; data.bar (categories)	admin/admin_dashboard.php, admin/admin_statistics.php
GET	/api/admin_data.php	Recent transactions (dashboard)	action=get_transactions	data[] (id, user, type, category, amount, date, status)	admin/admin_dashboard.php
GET	/api/admin_data.php	Recent users (dashboard)	action=get_users	data[] (id, name, email, transactions, expense, status, created_at)	admin/admin_dashboard.php
Module: Admin Users
Method	Endpoint	Purpose	Request (fields)	Response (fields used by FE)	Used by FE files
GET	/api/admin_data.php	User stats	action=user_stats	data {total_users, active_users, banned_users, new_users}	admin/admin_users.php
GET	/api/admin_data.php	List users	action=get_users; search; status; role; sort?	data[] (id, name, email, transactions, expense, status, created_at)	admin/admin_users.php
POST	/api/admin_data.php	Delete user	action=delete_user; id	success, message	admin/admin_users.php
(Inferred)	/api/admin_data.php	Reset password / lock / add user	action could be reset_password/ban/unban (buttons present)	success, message	admin/admin_users.php (buttons for reset/ban)
Module: Admin Categories
Method	Endpoint	Purpose	Request (fields)	Response (fields used by FE)	Used by FE files
GET	/api/admin_data.php	Category stats	action=category_stats	data {total_categories, income_count, expense_count}	admin/admin_categories.php
GET	/api/admin_data.php	List categories	action=get_categories; search; type	data[] (id, name, type, icon, color, usage_count, created_at)	admin/admin_categories.php
POST	/api/admin_data.php	Add category	action=add_category; name; type; color; icon	success, message	admin/admin_categories.php
POST	/api/admin_data.php	Delete category	action=delete_category; id	success, message	admin/admin_categories.php
Module: Admin Transactions
Method	Endpoint	Purpose	Request (fields)	Response (fields used by FE)	Used by FE files
GET	/api/admin_data.php	List transactions	action=get_transactions	data[] (id, user, type, category, amount, date, status)	admin/admin_transactions.php
Module: Admin Reports
Method	Endpoint	Purpose	Request (fields)	Response (fields used by FE)	Used by FE files
GET	/api/admin_data.php	Get report data (various types)	action=get_report_data; type (transaction_report, etc.); date_from; date_to	data {tables/series per type}; success	admin/admin_reports.php
GET	/api/admin_data.php	Export CSV (commented/inferred)	action=export_csv; type; date_from; date_to	CSV file download	admin/admin_reports.php
GET	/api/admin_data.php	Export PDF (commented/inferred)	action=export_pdf; type; date_from; date_to	PDF download	admin/admin_reports.php
Module: Admin Logs
Method	Endpoint	Purpose	Request (fields)	Response (fields used by FE)	Used by FE files
GET	/api/admin_data.php	System/user logs	action=get_logs	data[] (id, time, user, action, ip, note)	admin/admin_logs.php
Module: Admin Support Tickets
Method	Endpoint	Purpose	Request (fields)	Response (fields used by FE)	Used by FE files
GET	/api/admin_data.php	List support tickets	action=get_support_tickets; filters (status, priority, date, search)	data[] (id, subject, user_name/email, status, priority, created_at)	admin/admin_support.php
GET	/api/admin_data.php	Ticket detail	action=get_ticket_detail; id	data {id, subject, user, messages[], status, priority, created_at}	admin/admin_support.php
POST	/api/admin_data.php	Reply to ticket	action=reply_ticket; ticket_id; message; (optional attachment refs)	success, message	admin/admin_support.php
POST	/api/admin_data.php	Close ticket	action=close_ticket; ticket_id	success, message	admin/admin_support.php
Module: Admin Settings
Method	Endpoint	Purpose	Request (fields)	Response (fields used by FE)	Used by FE files
GET	/api/admin_data.php	Get settings	action=get_settings	data {general/notification/security toggles, thresholds}	admin/admin_settings.php
POST	/api/admin_data.php	Save settings	action=save_settings; form fields: site_name, currency, toggles (email, sms, 2fa, etc.), limits	success, message	admin/admin_settings.php
Module: Admin System Health
Method	Endpoint	Purpose	Request (fields)	Response (fields used by FE)	Used by FE files
GET	/api/admin_data.php	System health summary	action=get_system_health	data {uptime, cpu, memory, db_status, queue_status, services[]}	admin/admin_system_health.php
Module: Admin Statistics
(Uses same chart_data as dashboard.)

Method	Endpoint	Purpose	Request (fields)	Response (fields used by FE)	Used by FE files
GET	/api/admin_data.php	Charts overview	action=chart_data	data.line, data.pie, data.bar	admin/admin_statistics.php
Module: Admin Reports (Quick Stats in Dashboard)
Covered above; dashboard gets get_users/get_transactions.

Module: Notifications (User) already above.
Other User Modules (inferred but present):
Goals, Budgets, Bills, Recurring Transactions, Notifications: see corresponding tables.
Future optional: achievements, etc. (not in current code).
Summary of API counts (by action endpoints)
Auth: 4 actions (login, register, forgot_password, reset_password) + logout redirects.
User dashboard/stats: 5 actions.
User categories: 5 actions.
User transactions: 5 actions.
User budgets: 4 actions.
User bills: 5 actions.
User goals: 5 actions.
User recurring: 6 actions.
User notifications: 3 actions.
User profile: 3 actions.
Admin dashboard/stats: 4 actions.
Admin users: 3 implemented + inferred reset/ban/unban.
Admin categories: 4 actions.
Admin transactions: 1 action.
Admin reports: 1 active + 2 inferred exports.
Admin logs: 1 action.
Admin support: 4 actions.
Admin settings: 2 actions.
Admin system health: 1 action.
Gaps / ambiguities (recommendations)
Many actions are mock-only in current /api/data.php and /api/admin_data.php; backend should implement real DB-backed versions matching the fields above.
Some admin user buttons (reset password, ban/unban, add user) are implied by UI but not wired; recommend actions reset_user_password, ban_user, unban_user, create_user.
Reports page hints at CSV/PDF export; implement export_csv and export_pdf with type, date_from, date_to.
Ensure consistent pagination support where lists can be large (users, transactions, categories, tickets). FE currently does not pass page/size; consider extending with optional page/limit and update FE accordingly.
Normalise date filters: transactions currently send date only; consider date_from/date_to if backend supports ranges.
Authentication/authorization: protect admin endpoints separately; current mock relies on session flags.
Standardise error payload: FE expects success (bool) and message (string), and often data object/array.
This report is derived directly from the existing frontend code references; backend can implement the listed endpoints/actions to satisfy current UI behaviour.
