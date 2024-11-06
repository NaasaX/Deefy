<?php

namespace src\classes\dispatch;

use src\classes\action\AddPlaylistAction;
use src\classes\action\AddTrackAction;
use src\classes\action\DefaultAction;
use src\classes\action\DisplayPlaylistAction;
use src\classes\action\MesPlaylistsAction;
use src\classes\action\SigninAction;
use src\classes\action\RegisterAction;
use src\classes\action\LogoutAction;

class Dispatcher
{
    private ?string $action;

    public function __construct()
    {
        // Déterminer l'action à exécuter
        $this->action = $_GET['action'] ?? 'default';
    }

    public function run(): void
    {
        $action = null;

        switch ($this->action) {
            case 'display-playlist':
                $action = new DisplayPlaylistAction();
                break;
            case 'add-playlist':
                $action = new AddPlaylistAction();
                break;
            case 'add-track':
                $action = new AddTrackAction();
                break;
            case 'login':
                $action = new SigninAction();
                break;
            case 'register':
                $action = new RegisterAction();
                break;
            case 'logout':
                $action = new LogoutAction();
                break;
            case 'mes-playlists':
                $action = new MesPlaylistsAction();
                break;
            default:
                $action = new DefaultAction();
                break;
        }

        // Vérifier que $action est bien initialisée avant d'exécuter
        if ($action) {
            $this->renderPage($action->execute());
        } else {
            $this->renderPage("<p>Erreur : action non reconnue.</p>");
        }
    }

    private function renderPage(string $html): void
    {
        echo $html;
    }
}
