<?php
require 'db.php';
session_start();

// Vérification si l'administrateur est connecté
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Non autorisé.']);
    exit;
}

// Gestion des requêtes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $action = $data['action'];

    try {
        // Ajouter un communiqué
        if ($action === 'ajouter_communique') {
            $texte = $data['texte'];
            $cible = $data['cible'];
            $stmt = $pdo->prepare("INSERT INTO communiques (texte, cible) VALUES (?, ?)");
            $stmt->execute([$texte, $cible]);
            echo json_encode(['success' => true]);
        }

        // Modifier un communiqué
        if ($action === 'modifier_communique') {
            $id = $data['id'];
            $texte = $data['texte'];
            $cible = $data['cible'];
            $stmt = $pdo->prepare("UPDATE communiques SET texte = ?, cible = ? WHERE id = ?");
            $stmt->execute([$texte, $cible, $id]);
            echo json_encode(['success' => true]);
        }

        // Supprimer un communiqué
        if ($action === 'supprimer_communique') {
            $id = $data['id'];
            $stmt = $pdo->prepare("DELETE FROM communiques WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        }

        // Ajouter un emploi du temps
        if ($action === 'ajouter_emploi_temps') {
            $jour = $data['jour'];
            $horaire = $data['horaire'];
            $matiere = $data['matiere'];
            $eleve_id = $data['eleve_id'];
            $stmt = $pdo->prepare("INSERT INTO emploi_du_temps (jour, horaire, matiere, eleve_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$jour, $horaire, $matiere, $eleve_id]);
            echo json_encode(['success' => true]);
        }

        // Ajouter une note
        if ($action === 'ajouter_note') {
            $eleve_id = $data['eleve_id'];
            $matiere = $data['matiere'];
            $note = $data['note'];
            $stmt = $pdo->prepare("INSERT INTO notes (eleve_id, matiere, note) VALUES (?, ?, ?)");
            $stmt->execute([$eleve_id, $matiere, $note]);
            echo json_encode(['success' => true]);
        }

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
    }
}

// Récupérer les données
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $parents = $pdo->query("SELECT * FROM parents")->fetchAll(PDO::FETCH_ASSOC);
        $enfants = $pdo->query("SELECT * FROM enfants")->fetchAll(PDO::FETCH_ASSOC);
        $paiements = $pdo->query("SELECT * FROM paiements")->fetchAll(PDO::FETCH_ASSOC);
        $communiques = $pdo->query("SELECT * FROM communiques")->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => [
                'parents' => $parents,
                'enfants' => $enfants,
                'paiements' => $paiements,
                'communiques' => $communiques,
            ],
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
    }
}
?>
