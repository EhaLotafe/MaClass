<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $mot_de_passe = $_POST['mot_de_passe'];
    $matricule = htmlspecialchars(trim($_POST['matricule']));

    if (empty($nom) || empty($email) || empty($mot_de_passe) || empty($matricule)) {
        echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email invalide.']);
        exit;
    }

    if (strlen($mot_de_passe) < 8) {
        echo json_encode(['success' => false, 'message' => 'Le mot de passe doit contenir au moins 8 caractères.']);
        exit;
    }

    try {
        $stmtMatricule = $pdo->prepare("SELECT id, parent_id FROM enfants WHERE matricule = :matricule");
        $stmtMatricule->bindParam(':matricule', $matricule);
        $stmtMatricule->execute();
        $enfant = $stmtMatricule->fetch();

        if (!$enfant) {
            echo json_encode(['success' => false, 'message' => 'Matricule invalide ou inexistant.']);
            exit;
        }

        if (!is_null($enfant['parent_id'])) {
            echo json_encode(['success' => false, 'message' => 'Cet enfant est déjà lié à un parent.']);
            exit;
        }

        $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_BCRYPT);

        $stmtParent = $pdo->prepare("INSERT INTO parents (nom, email, mot_de_passe) VALUES (:nom, :email, :mot_de_passe)");
        $stmtParent->bindParam(':nom', $nom);
        $stmtParent->bindParam(':email', $email);
        $stmtParent->bindParam(':mot_de_passe', $mot_de_passe_hache);

        if ($stmtParent->execute()) {
            $parent_id = $pdo->lastInsertId();

            $stmtUpdateEnfant = $pdo->prepare("UPDATE enfants SET parent_id = :parent_id WHERE id = :enfant_id");
            $stmtUpdateEnfant->bindParam(':parent_id', $parent_id);
            $stmtUpdateEnfant->bindParam(':enfant_id', $enfant['id']);

            if ($stmtUpdateEnfant->execute()) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Inscription réussie. Redirection vers la connexion...', 
                    'redirect' => '../frontend/connexion_parent.html'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour de l\'enfant.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'inscription.']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
    }
}
?>
