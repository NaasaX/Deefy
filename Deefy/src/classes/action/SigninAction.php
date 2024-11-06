<?php

namespace src\classes\action;

use src\classes\action\Action;
use src\classes\exception\AuthException;
use src\classes\repository\DeefyRepository;

class SigninAction extends Action
{
    public function execute(): string {
        session_start();
        $html = "<!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Connexion</title>
            <link rel='stylesheet' href='src/styles/siginaction.css'>
        </head>
        <body>
        <h1>Connexion</h1>";

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Afficher le formulaire de connexion
            $html .= "
        <form action='?action=login' method='post'>
            <label for='email'>Email :</label>
            <input type='email' id='email' name='email' required>
            <label for='password'>Mot de passe :</label>
            <input type='password' id='password' name='password' required>
            <input type='submit' value='Se connecter'>
        </form>";
        } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier les champs et traiter l'authentification
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

            $deefyRepository = DeefyRepository::getInstance();
            try {
                $deefyRepository->signin($email, $password);
                header("Location: ?action=default"); // Redirection vers l'action par défaut
                exit();
            } catch (AuthException $e) {
                $html .= "<p>Erreur : " . $e->getMessage() . "</p>";
            }
        }

        $html .= "</body></html>";
        return $html;
    }
}