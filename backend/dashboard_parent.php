<?php
require 'db.php';
session_start();

// Vérification si le parent est connecté
if (!isset($_SESSION['parent_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Non autorisé.']);
    exit;
}

try {
    $parent_id = $_SESSION['parent_id'];

    // Récupérer tous les enfants du parent connecté
    $stmtEnfants = $pdo->prepare("SELECT * FROM enfants WHERE parent_id = :parent_id");
    $stmtEnfants->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
    $stmtEnfants->execute();
    $enfants = $stmtEnfants->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les paiements des enfants du parent
    $stmtPaiements = $pdo->prepare("
        SELECT p.id, p.montant, p.date_paiement, e.nom AS enfant_nom, e.classe
        FROM paiements p
        JOIN enfants e ON p.enfant_id = e.id
        WHERE e.parent_id = :parent_id
    ");
    $stmtPaiements->bindParam(':parent_id', $parent_id, PDO::PARAM_INT);
    $stmtPaiements->execute();
    $paiements = $stmtPaiements->fetchAll(PDO::FETCH_ASSOC);

    // Envoyer les données sous format JSON
    echo json_encode([
        'success' => true,
        'data' => [
            'enfants' => $enfants,
            'paiements' => $paiements,
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
}
?>