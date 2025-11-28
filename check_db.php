<?php
require_once 'config.php';

try {
    // Check support_tickets
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM support_tickets");
    $tickets = $stmt->fetch()['count'];

    // Check support_messages
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM support_messages");
    $messages = $stmt->fetch()['count'];

    // Check notifications
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM notifications");
    $notifs = $stmt->fetch()['count'];

    echo "Database status:\n";
    echo "Support tickets: $tickets\n";
    echo "Support messages: $messages\n";
    echo "Notifications: $notifs\n";

    if ($tickets == 0) {
        echo "\nNo support tickets found. Adding test data...\n";

        // Add test ticket
        $pdo->exec("
            INSERT INTO support_tickets (user_id, subject, category, status, priority)
            VALUES (2, 'Test ticket', 'question', 'open', 'medium')
        ");

        $ticketId = $pdo->lastInsertId();

        // Add test message
        $pdo->exec("
            INSERT INTO support_messages (ticket_id, sender_id, sender_type, message)
            VALUES ($ticketId, 2, 'user', 'This is a test message')
        ");

        // Add test notification
        $pdo->exec("
            INSERT INTO notifications (user_id, type, title, message)
            VALUES (2, 'info', 'Test notification', 'This is a test notification')
        ");

        echo "Test data added successfully!\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>