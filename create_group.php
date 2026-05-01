<?php
include 'includes/config.php';
include 'includes/functions.php';
requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $subject = sanitize($_POST['subject']);
    $level = sanitize($_POST['level']);
    $description = sanitize($_POST['description']);
    $user_id = $_SESSION['user_id'];
    
    $sql = "INSERT INTO study_groups (name, subject, level, description, created_by) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $subject, $level, $description, $user_id);
    
    if ($stmt->execute()) {
        $group_id = $stmt->insert_id;
        // Auto-join the creator
        $join_sql = "INSERT INTO members (group_id, user_id) VALUES (?, ?)";
        $join_stmt = $conn->prepare($join_sql);
        $join_stmt->bind_param("ii", $group_id, $user_id);
        $join_stmt->execute();
        
        $success = "Groupe créé avec succès !";
        header("refresh:2;url=dashboard.php");
    } else {
        $error = "Erreur lors de la création du groupe.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un groupe - StudyMatch</title>
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
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <h2>Créer un nouveau groupe d'étude</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form id="groupForm" method="POST" action="">
                <div class="form-group">
                    <label>Nom du groupe *</label>
                    <input type="text" name="name" id="name" required>
                </div>
                
                <div class="form-group">
                    <label>Matière *</label>
                    <select name="subject" id="subject" required>
                        <option value="">Sélectionnez une matière</option>
                        <option value="Mathématiques">Mathématiques</option>
                        <option value="Physique">Physique</option>
                        <option value="Chimie">Chimie</option>
                        <option value="Informatique">Informatique</option>
                        <option value="Anglais">Anglais</option>
                        <option value="Français">Français</option>
                        <option value="Histoire">Histoire</option>
                        <option value="Biologie">Biologie</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Niveau *</label>
                    <!-- Remplacer la fin du select niveau par : -->
<div class="form-group">
    <label>Niveau *</label>
    <select name="level" id="level" required>
        <option value="">Sélectionnez un niveau</option>
        <option value="Licence 1">Licence 1</option>
        <option value="Licence 2">Licence 2</option>
        <option value="Licence 3">Licence 3</option>
        <option value="Master 1">Master 1</option>
        <option value="Master 2">Master 2</option>
        <option value="Doctorat">Doctorat</option>
    </select>
</div>

<div class="form-group">
    <label>Description</label>
    <textarea name="description" id="description" rows="4" placeholder="Décrivez votre groupe d'étude..."></textarea>
</div>

<button type="submit" class="btn">Créer le groupe</button>
</form>
</div>
</div>

<footer class="footer">
    <p>&copy; 2024 StudyMatch - Trouvez vos partenaires d'étude</p>
</footer>

<script src="assets/js/script.js"></script>
</body>
</html>