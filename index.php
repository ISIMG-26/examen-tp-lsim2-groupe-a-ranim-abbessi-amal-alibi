<?php include 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyMatch - Trouvez vos partenaires d'étude</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo">📚 StudyMatch</a>
        <ul class="nav-links">
            <li><a href="index.php">Accueil</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="dashboard.php">Tableau de bord</a></li>
                <li><a href="create_group.php">Créer un groupe</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            <?php else: ?>
                <li><a href="login.php">Connexion</a></li>
                <li><a href="register.php">Inscription</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="container">
        <div class="card" style="text-align: center;">
            <h1>Bienvenue sur StudyMatch</h1>
            <p style="font-size: 1.2rem; margin: 1rem 0;">
                Trouvez des partenaires d'étude partageant les mêmes intérêts académiques
            </p>
            
            <div class="grid" style="margin-top: 2rem;">
                <div class="group-card">
                    <h3>📖 Trouvez un groupe</h3>
                    <p>Recherchez par matière, niveau ou université</p>
                </div>
                <div class="group-card">
                    <h3>🤝 Collaborez</h3>
                    <p>Partagez des ressources et travaillez ensemble</p>
                </div>
                <div class="group-card">
                    <h3>💬 Communiquez</h3>
                    <p>Chat en temps réel avec vos partenaires</p>
                </div>
            </div>
            
            <?php if (!isset($_SESSION['user_id'])): ?>
                <div style="margin-top: 2rem;">
                    <a href="register.php" class="btn">Commencer maintenant</a>
                </div>
            <?php endif; ?>
        </div>
        
      
        <div class="card">
            <h2>Rechercher des groupes d'étude</h2>
            <div class="form-group">
                <input type="text" id="searchGroups" placeholder="Rechercher par matière, niveau..." class="search-input">
            </div>
            <div id="groupsList">
                <?php
                $sql = "SELECT g.*, u.username as creator_name, 
                        (SELECT COUNT(*) FROM members WHERE group_id = g.id) as member_count
                        FROM study_groups g
                        JOIN users u ON g.created_by = u.id
                        ORDER BY g.created_at DESC
                        LIMIT 6";
                $result = $conn->query($sql);
                
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
                    echo '<p>Aucun groupe pour le moment. Soyez le premier à en créer un !</p>';
                }
                ?>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <p>&copy; 2026 StudyMatch - Trouvez vos partenaires d'étude</p>
    </footer>
    
    <script src="assets/js/script.js"></script>
</body>
</html>