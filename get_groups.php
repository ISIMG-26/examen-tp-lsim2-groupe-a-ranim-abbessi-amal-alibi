<?php
session_start();
include '../includes/config.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT g.*, u.username as creator_name, 
        (SELECT COUNT(*) FROM members WHERE group_id = g.id) as member_count
        FROM study_groups g
        JOIN users u ON g.created_by = u.id
        WHERE g.name LIKE ? OR g.subject LIKE ? OR g.level LIKE ?
        ORDER BY g.created_at DESC
        LIMIT 10";

$searchTerm = "%$search%";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="group-card">';
        echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
        echo '<p><strong>Matière:</strong> ' . htmlspecialchars($row['subject']) . '</p>';
        echo '<p><strong>Niveau:</strong> ' . htmlspecialchars($row['level']) . '</p>';
        echo '<p><strong>Membres:</strong> ' . $row['member_count'] . '</p>';
        echo '<p>' . htmlspecialchars(substr($row['description'], 0, 100)) . '...</p>';
        if (isset($_SESSION['user_id'])) {
            echo '<button class="btn join-group-btn" data-group-id="' . $row['id'] . '">Rejoindre</button>';
        }
        echo '</div>';
    }
} else {
    echo '<p>Aucun groupe trouvé.</p>';
}
?>