<?php

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . 'login.php');
        exit();
    }
}

function sanitize($input) {
    global $conn;
    return htmlspecialchars(strip_tags(trim($input)));
}


function getUserGroups($user_id) {
    global $conn;
    $sql = "SELECT g.*, COUNT(m.id) as member_count 
            FROM study_groups g
            LEFT JOIN members m ON g.id = m.group_id
            WHERE g.created_by = ? OR m.user_id = ?
            GROUP BY g.id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    return $stmt->get_result();
}
?>