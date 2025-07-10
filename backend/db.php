<?php
$host = 'localhost';       // Hôte de la base de données
$dbname = 'gestion_scolaire'; // Nom de la base de données
$username = 'root';         // Nom d'utilisateur MySQL
$password = '';             // Mot de passe MySQL (vide si tu n'en as pas configuré)

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Pour activer le mode d'erreur de PDO
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage()); // Si erreur, afficher message
}
?>
