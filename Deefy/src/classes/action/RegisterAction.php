<?php
namespace src\classes\action;

use src\classes\repository\DeefyRepository;
use src\classes\exception\AuthException;

class RegisterAction extends Action
{
    public function execute(): string
    {
        $html = "<!DOCTYPE html>
    <html lang='fr'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Deefy - Inscription</title>
        <link rel='stylesheet' href='src/styles/registeraction.css'>
    </head>
    
    <body>";

        // Gestion de la requête GET : afficher le formulaire d'inscription
        if ($this->http_method === 'GET') {
            $html .= "<h1>Inscription</h1>
        <form action='?action=register' method='post'>
            <label for='login'>Login (Email)</label>
            <input type='email' id='login' name='login' required>
            <label for='password'>Mot de passe</label>
            <input type='password' id='password' name='password' required>
            <label for='password2'>Confirmer le mot de passe</label>
            <input type='password' id='password2' name='password2' required>
            <input type='submit' value='Inscription'>
        </form>";
        }

        // Gestion de la requête POST : inscription
        elseif ($this->http_method === 'POST') {
            session_start();
            $login = filter_var($_POST['login'], FILTER_SANITIZE_EMAIL);
            $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
            $password2 = filter_var($_POST['password2'], FILTER_SANITIZE_STRING);

            // Vérification des champs requis et validation du mot de passe
            if (empty($login) || empty($password) || empty($password2)) {
                $html .= "<p>Veuillez remplir tous les champs</p>";
            } elseif (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
                $html .= "<p>Format de l'email invalide</p>";
            } elseif ($password !== $password2) {
                $html .= "<p>Les mots de passe ne correspondent pas</p>";
            } elseif (strlen($password) < 10) {
                $html .= "<p>Le mot de passe doit contenir au moins 10 caractères</p>";
            } else {
                try {
                    DeefyRepository::getInstance()->register($login, $password);
                    $html .= "<p>Inscription réussie</p>";
                } catch (AuthException $e) {
                    $html .= "<p>" . $e->getMessage() . "</p>";
                } catch (\Exception $e) {
                    $html .= "<p>Une erreur est survenue lors de l'inscription</p>";
                }
            }
        }

        $html .= "</body></html>";

        return $html;
    }
}
