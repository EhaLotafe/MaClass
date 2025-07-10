<?php
require 'db.php';
session_start();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['parent_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Non autorisé.']);
    exit;
}

// Vérifier qu'un enfant est sélectionné
if (!isset($_GET['enfant_id'])) {
    echo json_encode(['success' => false, 'message' => 'Enfant non spécifié.']);
    exit;
}

$enfant_id = $_GET['enfant_id'];

try {
    // Récupérer les infos de l'enfant
    $stmtEnfant = $pdo->prepare("SELECT * FROM enfants WHERE id = :enfant_id AND parent_id = :parent_id");
    $stmtEnfant->bindParam(':enfant_id', $enfant_id, PDO::PARAM_INT);
    $stmtEnfant->bindParam(':parent_id', $_SESSION['parent_id'], PDO::PARAM_INT);
    $stmtEnfant->execute();
    $enfant = $stmtEnfant->fetch(PDO::FETCH_ASSOC);

    if (!$enfant) {
        echo json_encode(['success' => false, 'message' => 'Enfant introuvable.']);
        exit;
    }

    // Récupérer les notes
    $stmtNotes = $pdo->prepare("SELECT * FROM notes WHERE enfant_id = :enfant_id");
    $stmtNotes->bindParam(':enfant_id', $enfant_id, PDO::PARAM_INT);
    $stmtNotes->execute();
    $notes = $stmtNotes->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer l'emploi du temps
    $stmtEmploi = $pdo->prepare("SELECT * FROM emploi_du_temps WHERE enfant_id = :enfant_id");
    $stmtEmploi->bindParam(':enfant_id', $enfant_id, PDO::PARAM_INT);
    $stmtEmploi->execute();
    $emploi_du_temps = $stmtEmploi->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les paiements
    $stmtPaiements = $pdo->prepare("SELECT * FROM paiements WHERE enfant_id = :enfant_id");
    $stmtPaiements->bindParam(':enfant_id', $enfant_id, PDO::PARAM_INT);
    $stmtPaiements->execute();
    $paiements = $stmtPaiements->fetchAll(PDO::FETCH_ASSOC);

    // Retourner les données
    echo json_encode([
        'success' => true,
        'data' => [
            'enfant' => $enfant,
            'notes' => $notes,
            'emploi_du_temps' => $emploi_du_temps,
            'paiements' => $paiements
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
}
?>
