<?php

namespace src\classes\auth;


use src\classes\exception\AuthException;
use \PDO;

class AuthProvider
{


    public static function signin(PDO $pdo, string $email, string $passwd2check): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifier si l'email existe
        $query = "SELECT * FROM user WHERE email = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new AuthException("Erreur d'authentification : email inconnu");
        }

        // Vérifier le mot de passe
        if (!password_verify($passwd2check, $user['passwd'])) {
            throw new AuthException("Erreur d'authentification : mot de passe incorrect");
        }

        // Créez une instance de l'utilisateur à partir des données récupérées
        $userInstance = new User($user['id'], $user['email'], $user['role']);

        $_SESSION['user'] = serialize($userInstance);
    }





    public static function register(PDO $pdo, string $email, string $passwd): void
    {
        // Vérifier la longueur du mot de passe
        if (strlen($passwd) < 10) {
            throw new AuthException("Erreur d'authentification : le mot de passe est trop court (minimum 10 caractères)");
        }

        // Vérifier si l'email est déjà utilisé
        $query = "SELECT COUNT(*) FROM user WHERE email = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$email]);
        $exists = $stmt->fetchColumn();

        if ($exists) {
            throw new AuthException("Erreur d'authentification : un utilisateur avec cet email existe déjà");
        }

        // Encoder le mot de passe
        $hash = password_hash($passwd, PASSWORD_DEFAULT);

        $query = "INSERT INTO user (email, passwd, role) VALUES (?, ?, 1)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$email, $hash]);
    }



    public static function getSignedInUser(): User
    {
        if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
            // Lancer une exception avec un message d'erreur
            throw new AuthException("Vous devez être connecté pour accéder à cette page. Veuillez vous connecter.");
        }

        try {
            return unserialize($_SESSION['user']);
        } catch (\Exception $e) {
            // Si une erreur se produit lors de la désérialisation
            throw new AuthException("Erreur de session : les données de votre session semblent être corrompues. Veuillez vous reconnecter.");
        }
    }


}