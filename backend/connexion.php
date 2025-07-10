<?php
require 'db.php';
session_start();

header("Content-Type: application/json"); // 📌 Assurez-vous que la réponse est bien en JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $mot_de_passe = htmlspecialchars($_POST['mot_de_passe']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email invalide.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM parents WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $parent = $stmt->fetch();

    if ($parent && password_verify($mot_de_passe, $parent['mot_de_passe'])) {
        $_SESSION['parent_id'] = $parent['id'];
        echo json_encode([
            'success' => true, 
            'message' => 'Connexion réussie. Redirection...',
            'redirect' => '../frontend/dashboard_parent.html'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Identifiants incorrects.']);
    }
}
?>
