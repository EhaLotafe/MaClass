<?php
require 'db.php';
session_start();

// Vérification si l'administrateur est connecté
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403); // Accès interdit
    echo json_encode(['success' => false, 'message' => 'Non autorisé.']);
    exit;
}

$action = $_GET['action'] ?? '';

try {
    if ($action === 'add') {
        // Ajouter un paiement
        $eleve_id = $_POST['eleve_id'];
        $montant = $_POST['montant'];
        $date = $_POST['date'];

        $stmt = $pdo->prepare("INSERT INTO paiements (eleve_id, montant, date) VALUES (:eleve_id, :montant, :date)");
        $stmt->bindParam(':eleve_id', $eleve_id, PDO::PARAM_INT);
        $stmt->bindParam(':montant', $montant);
        $stmt->bindParam(':date', $date);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Paiement ajouté avec succès.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout du paiement.']);
        }
    } elseif ($action === 'delete') {
        // Supprimer un paiement
        $paiement_id = $_POST['id'];

        $stmt = $pdo->prepare("DELETE FROM paiements WHERE id = :id");
        $stmt->bindParam(':id', $paiement_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Paiement supprimé avec succès.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression du paiement.']);
        }
    } elseif ($action === 'update') {
        // Mettre à jour un paiement
        $paiement_id = $_POST['id'];
        $montant = $_POST['montant'];
        $date = $_POST['date'];

        $stmt = $pdo->prepare("UPDATE paiements SET montant = :montant, date = :date WHERE id = :id");
        $stmt->bindParam(':id', $paiement_id, PDO::PARAM_INT);
        $stmt->bindParam(':montant', $montant);
        $stmt->bindParam(':date', $date);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Paiement mis à jour avec succès.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour du paiement.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Action non reconnue.']);
    }
} catch (PDOException $e) {
    http_response_code(500); // Erreur serveur
    echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
}
?>
