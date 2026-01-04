<?php
/**
 * Admin Database Connection File
 * 
 * This file provides backward compatibility with existing code
 * while using the new Database class internally.
 */
require_once __DIR__ . '/../dbconnect/load_env.php';
require_once __DIR__ . '/../classes/Database.php';

// Get the Database singleton instance
$db = Database::getInstance();

// Backward compatibility: $conn still works as the mysqli connection
$conn = $db->getConnection();

/**
 * Execute a raw SQL query (backward compatible function)
 * 
 * @param string $query SQL query to execute
 * @return mysqli_result|bool
 */
function executeQuery($query)
{
    $db = Database::getInstance();
    return $db->query($query);
}