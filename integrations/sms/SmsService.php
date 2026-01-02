<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/IprogSms.php';

class SmsService
{
    private $smsGateway;
    private $conn;

    public function __construct()
    {
        $this->smsGateway = new IprogSms();
        $this->initDatabase();
    }
    private function initDatabase()
    {
        $this->conn = new mysqli(SMS_DB_HOST, SMS_DB_USER, SMS_DB_PASS, SMS_DB_NAME);

        if ($this->conn->connect_error) {
            throw new Exception('Database connection failed: ' . $this->conn->connect_error);
        }

        $this->conn->set_charset('utf8mb4');
    }

    /**
     * Send SMS notification for booking approval
     * 
     * @param int $bookingId The booking ID
     * @param string $phoneNumber The recipient's phone number
     * @param string $customerName The customer's name
     * @param string $checkInDate The check-in date
     * @return array Response with success status
     */
    public function sendBookingApprovalSms($bookingId, $phoneNumber, $customerName, $checkInDate)
    {
        $message = sprintf(SMS_TEMPLATE_BOOKING_APPROVED, $customerName, $bookingId, $checkInDate);
        return $this->sendAndLog($bookingId, $phoneNumber, $message, 'booking_approved');
    }

    /**
     * Send SMS notification for booking cancellation
     * 
     * @param int $bookingId The booking ID
     * @param string $phoneNumber The recipient's phone number
     * @param string $customerName The customer's name
     * @return array Response with success status
     */
    public function sendBookingCancelledSms($bookingId, $phoneNumber, $customerName)
    {
        $message = sprintf(SMS_TEMPLATE_BOOKING_CANCELLED, $customerName, $bookingId);
        return $this->sendAndLog($bookingId, $phoneNumber, $message, 'booking_cancelled');
    }

    /**
     * Send SMS notification for booking completion
     * 
     * @param int $bookingId The booking ID
     * @param string $phoneNumber The recipient's phone number
     * @param string $customerName The customer's name
     * @return array Response with success status
     */
    public function sendBookingCompletedSms($bookingId, $phoneNumber, $customerName)
    {
        $message = sprintf(SMS_TEMPLATE_BOOKING_COMPLETED, $customerName, $bookingId);
        return $this->sendAndLog($bookingId, $phoneNumber, $message, 'booking_completed');
    }

    /**
     * Send a custom SMS message
     * 
     * @param string $phoneNumber The recipient's phone number
     * @param string $message The message content
     * @param int|null $bookingId Optional booking ID
     * @return array Response with success status
     */
    public function sendCustomSms($phoneNumber, $message, $bookingId = null)
    {
        return $this->sendAndLog($bookingId, $phoneNumber, $message, 'custom');
    }

    /**
     * Send SMS and log the result
     * 
     * @param int|null $bookingId The booking ID
     * @param string $phoneNumber The recipient's phone number
     * @param string $message The message content
     * @param string $messageType The type of message
     * @return array Response with success status
     */
    private function sendAndLog($bookingId, $phoneNumber, $message, $messageType)
    {
        $result = $this->smsGateway->sendSms($phoneNumber, $message);       // Send the SMS

        $this->logMessage(         // Log the message
            $bookingId,
            $phoneNumber,
            $message,
            $messageType,
            $result['status'],
            'outgoing',
            json_encode($result)
        );

        return $result;
    }

    /**
     * Log SMS message to database
     * 
     * @param int|null $bookingId The booking ID
     * @param string $phoneNumber The phone number
     * @param string $message The message content
     * @param string $messageType The type of message
     * @param string $status The message status
     * @param string $direction Incoming or outgoing
     * @param string $response The API response
     * @return bool Success status
     */
    public function logMessage($bookingId, $phoneNumber, $message, $messageType, $status, $direction, $response)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO sms_logs (booking_id, phone_number, message, message_type, status, direction, response, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
        );

        if (!$stmt) {
            error_log('SMS Log Error: ' . $this->conn->error);
            return false;
        }

        $stmt->bind_param('issssss', $bookingId, $phoneNumber, $message, $messageType, $status, $direction, $response);
        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Get all SMS logs with optional filtering
     * 
     * @param array $filters Optional filters (status, direction, date_from, date_to)
     * @param int $limit Number of records to return
     * @param int $offset Offset for pagination
     * @return array Array of SMS logs
     */
    public function getSmsLogs($filters = [], $limit = 50, $offset = 0)
    {
        $where = [];
        $params = [];
        $types = '';

        if (!empty($filters['status'])) {
            $where[] = 'status = ?';
            $params[] = $filters['status'];
            $types .= 's';
        }

        if (!empty($filters['direction'])) {
            $where[] = 'direction = ?';
            $params[] = $filters['direction'];
            $types .= 's';
        }

        if (!empty($filters['date_from'])) {
            $where[] = 'DATE(created_at) >= ?';
            $params[] = $filters['date_from'];
            $types .= 's';
        }

        if (!empty($filters['date_to'])) {
            $where[] = 'DATE(created_at) <= ?';
            $params[] = $filters['date_to'];
            $types .= 's';
        }

        if (!empty($filters['phone_number'])) {
            $where[] = 'phone_number LIKE ?';
            $params[] = '%' . $filters['phone_number'] . '%';
            $types .= 's';
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $query = "SELECT * FROM sms_logs $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';

        $stmt = $this->conn->prepare($query);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $logs = [];
        while ($row = $result->fetch_assoc()) {
            $logs[] = $row;
        }

        $stmt->close();
        return $logs;
    }

    /**
     * Get SMS statistics
     * 
     * @return array Statistics array
     */
    public function getStatistics()
    {
        $stats = [
            'total_sent' => 0,
            'total_received' => 0,
            'total_failed' => 0,
            'today_sent' => 0,
            'today_received' => 0
        ];

        // Total sent
        $result = $this->conn->query("SELECT COUNT(*) as count FROM sms_logs WHERE direction = 'outgoing' AND status = 'sent'");
        if ($row = $result->fetch_assoc()) {
            $stats['total_sent'] = (int)$row['count'];
        }

        // Total received
        $result = $this->conn->query("SELECT COUNT(*) as count FROM sms_logs WHERE direction = 'incoming'");
        if ($row = $result->fetch_assoc()) {
            $stats['total_received'] = (int)$row['count'];
        }

        // Total failed
        $result = $this->conn->query("SELECT COUNT(*) as count FROM sms_logs WHERE status = 'failed'");
        if ($row = $result->fetch_assoc()) {
            $stats['total_failed'] = (int)$row['count'];
        }

        // Today sent
        $result = $this->conn->query("SELECT COUNT(*) as count FROM sms_logs WHERE direction = 'outgoing' AND status = 'sent' AND DATE(created_at) = CURDATE()");
        if ($row = $result->fetch_assoc()) {
            $stats['today_sent'] = (int)$row['count'];
        }

        // Today received
        $result = $this->conn->query("SELECT COUNT(*) as count FROM sms_logs WHERE direction = 'incoming' AND DATE(created_at) = CURDATE()");
        if ($row = $result->fetch_assoc()) {
            $stats['today_received'] = (int)$row['count'];
        }

        return $stats;
    }

    /**
     * Get total count of SMS logs with filters
     * 
     * @param array $filters Optional filters
     * @return int Total count
     */
    public function getTotalCount($filters = [])
    {
        $where = [];
        $params = [];
        $types = '';

        if (!empty($filters['status'])) {
            $where[] = 'status = ?';
            $params[] = $filters['status'];
            $types .= 's';
        }

        if (!empty($filters['direction'])) {
            $where[] = 'direction = ?';
            $params[] = $filters['direction'];
            $types .= 's';
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $query = "SELECT COUNT(*) as count FROM sms_logs $whereClause";
        $stmt = $this->conn->prepare($query);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return (int)$row['count'];
    }

    /**
     * Delete old SMS logs
     * 
     * @param int $daysOld Delete logs older than this many days
     * @return int Number of deleted records
     */
    public function cleanupOldLogs($daysOld = 90)
    {
        $stmt = $this->conn->prepare("DELETE FROM sms_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)");
        $stmt->bind_param('i', $daysOld);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        return $affected;
    }
    public function __destruct()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
