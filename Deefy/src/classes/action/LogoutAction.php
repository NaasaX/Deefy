<?php

namespace src\classes\action;

use src\classes\action\Action;

class LogoutAction extends Action {

    public function execute(): string {
        $html = "<!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Deefy - Déconnexion</title>
            <link rel='stylesheet' href='src/styles/logoutaction.css'> 
        </head>
        <body>";

        // Gestion de la requête GET : déconnexion
        if ($this->http_method === 'GET') {
            session_start();
            session_destroy();
            $html .= "<div class='logout-message'>
                        <p>Déconnexion réussie</p>
                        <p><a href='?action=login'>Se connecter</a></p>
                      </div>";
        }

        $html .= "</body></html>";
        return $html;
    }
}
