<?php
include 'includes/config.php';
include 'includes/functions.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get user's groups
$my_groups_sql = "SELECT g.*, u.username as creator_name,
                  (SELECT COUNT(*) FROM members WHERE group_id = g.id) as member_count
                  FROM study_groups g
                  JOIN users u ON g.created_by = u.id
                  LEFT JOIN members m ON g.id = m.group_id
                  WHERE g.created_by = ? OR m.user_id = ?
                  GROUP BY g.id
                  ORDER BY g.created_at DESC";
$stmt = $conn->prepare($my_groups_sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$my_groups = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - StudyMatch</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">📚 StudyMatch</a>
        <ul class="nav-links">
            <li><a href="index.php">Accueil</a></li>
            <li><a href="dashboard.php">Tableau de bord</a></li>
            <li><a href="create_group.php">Créer un groupe</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="card">
            <h1>Bonjour, <?php echo htmlspecialchars($username); ?> !</h1>
            <p>Bienvenue sur votre tableau de bord StudyMatch.</p>
        </div>
        
        <div class="card">
            <h2>Mes groupes d'étude</h2>
            <div class="grid">
                <?php if ($my_groups->num_rows > 0): ?>
                    <?php while ($group = $my_groups->fetch_assoc()): ?>
                        <div class="group-card">
                            <h3><?php echo htmlspecialchars($group['name']); ?></h3>
                            <p><strong>Matière:</strong> <?php echo htmlspecialchars($group['subject']); ?></p>
                            <p><strong>Niveau:</strong> <?php echo htmlspecialchars($group['level']); ?></p>
                            <p><strong>Créé par:</strong> <?php echo htmlspecialchars($group['creator_name']); ?></p>
                            <p><strong>Membres:</strong> <?php echo $group['member_count']; ?></p>
                            <p><?php echo htmlspecialchars(substr($group['description'], 0, 100)); ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Vous n'avez encore rejoint aucun groupe. 
                    <a href="create_group.php">Créez votre premier groupe</a> ou explorez les groupes disponibles sur la page d'accueil !</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <h2>Groupes recommandés</h2>
            <div id="recommendedGroups">
                <?php
                $recommended_sql = "SELECT g.*, u.username as creator_name,
                                    (SELECT COUNT(*) FROM members WHERE group_id = g.id) as member_count
                                    FROM study_groups g
                                    JOIN users u ON g.created_by = u.id
                                    WHERE NOT EXISTS (
                                        SELECT 1 FROM members m WHERE m.group_id = g.id AND m.user_id = ?
                                    )
                                    ORDER BY g.created_at DESC
                                    LIMIT 3";
                $stmt = $conn->prepare($recommended_sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $recommended = $stmt->get_result();
                
                if ($recommended->num_rows > 0):
                ?>
                <div class="grid" id="recommendedGroupsGrid">
                    <?php while ($group = $recommended->fetch_assoc()): ?>
                        <div class="group-card" data-group-id="<?php echo $group['id']; ?>">
                            <h3><?php echo htmlspecialchars($group['name']); ?></h3>
                            <p><strong>Matière:</strong> <?php echo htmlspecialchars($group['subject']); ?></p>
                            <p><strong>Niveau:</strong> <?php echo htmlspecialchars($group['level']); ?></p>
                            <p><strong>Créé par:</strong> <?php echo htmlspecialchars($group['creator_name']); ?></p>
                            <p><strong>Membres:</strong> <?php echo $group['member_count']; ?></p>
                            <p><?php echo htmlspecialchars(substr($group['description'], 0, 100)); ?></p>
                            <button class="btn join-group-btn" data-group-id="<?php echo $group['id']; ?>">➕ Rejoindre ce groupe</button>
                        </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                    <p>Aucun groupe recommandé pour le moment. Revenez plus tard !</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <p>&copy; 2026 StudyMatch - Trouvez vos partenaires d'étude</p>
    </footer>
    
    <script src="assets/js/script.js"></script>
</body>
</html>