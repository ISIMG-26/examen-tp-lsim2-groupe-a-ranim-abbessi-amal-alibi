<?php
// Désactiver complètement l'affichage des erreurs pour ce fichier
error_reporting(0);
ini_set('display_errors', 0);

session_start();
header('Content-Type: application/json');

// Initialiser la réponse
$response = ['success' => false, 'message' => ''];

// Inclure la configuration
require_once __DIR__ . '/../includes/config.php';

// Vérification de la connexion à la base de données
if (!isset($conn) || $conn->connect_error) {
    $response['message'] = 'Erreur de connexion à la base de données.';
    echo json_encode($response);
    exit();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Veuillez vous connecter pour rejoindre un groupe.';
    echo json_encode($response);
    exit();
}

// Vérifier si group_id est présent
if (!isset($_POST['group_id']) && !isset($_GET['group_id'])) {
    $response['message'] = 'ID du groupe manquant.';
    echo json_encode($response);
    exit();
}

$user_id = $_SESSION['user_id'];
$group_id = isset($_POST['group_id']) ? (int)$_POST['group_id'] : (int)$_GET['group_id'];

// Vérifier si le groupe existe
$check_group = $conn->query("SELECT id FROM study_groups WHERE id = $group_id");
if (!$check_group || $check_group->num_rows == 0) {
    $response['message'] = 'Ce groupe n\'existe pas.';
    echo json_encode($response);
    exit();
}

// Vérifier si l'utilisateur est déjà membre
$check_member = $conn->query("SELECT id FROM members WHERE group_id = $group_id AND user_id = $user_id");
if ($check_member && $check_member->num_rows > 0) {
    $response['message'] = 'Vous êtes déjà membre de ce groupe.';
    echo json_encode($response);
    exit();
}

// Ajouter l'utilisateur au groupe
$insert = $conn->query("INSERT INTO members (group_id, user_id) VALUES ($group_id, $user_id)");
if ($insert) {
    $response['success'] = true;
    $response['message'] = '✅ Vous avez rejoint le groupe avec succès !';
} else {
    $response['message'] = 'Erreur lors de l\'inscription : ' . $conn->error;
}

echo json_encode($response);
exit();
?>