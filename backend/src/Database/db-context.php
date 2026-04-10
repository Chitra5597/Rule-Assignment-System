<?php
// Database connection and helper functions
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'rule_assignment_system';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Helper function to execute query and return results
function query($sql, $params = array()) {
    global $conn;
    
    $stmt = $conn->prepare($sql);
    
    if ($params) {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Get single row
function getOne($sql, $params = array()) {
    $result = query($sql, $params);
    return isset($result[0]) ? $result[0] : null;
}

// Get all rows
function getAll($sql, $params = array()) {
    return query($sql, $params);
}

// Insert data
function insert($table, $data) {
    global $conn;
    
    $cols = implode(', ', array_keys($data));
    $vals = implode(', ', array_fill(0, count($data), '?'));
    
    $sql = "INSERT INTO $table ($cols) VALUES ($vals)";
    $stmt = $conn->prepare($sql);
    
    $types = '';
    $values = array();
    foreach ($data as $val) {
        if (is_int($val)) {
            $types .= 'i';
        } elseif (is_float($val)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
        $values[] = $val;
    }
    
    $stmt->bind_param($types, ...$values);
    $stmt->execute();
    
    return $conn->insert_id;
}

// Update data
function update($table, $data, $where, $whereParams = array()) {
    global $conn;
    
    $set = array();
    $types = '';
    $values = array();
    
    foreach ($data as $col => $val) {
        $set[] = "$col = ?";
        if (is_int($val)) {
            $types .= 'i';
        } elseif (is_float($val)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
        $values[] = $val;
    }
    
    foreach ($whereParams as $val) {
        if (is_int($val)) {
            $types .= 'i';
        } elseif (is_float($val)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
        $values[] = $val;
    }
    
    $sql = "UPDATE $table SET " . implode(', ', $set) . " WHERE $where";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$values);
    $stmt->execute();
    
    return $stmt->affected_rows;
}

// Delete data
function delete($table, $where, $params = array()) {
    global $conn;
    
    $sql = "DELETE FROM $table WHERE $where";
    $stmt = $conn->prepare($sql);
    
    if ($params) {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    return $stmt->affected_rows;
}

// Close connection function
function closeConnection() {
    global $conn;
    $conn->close();
}
?>