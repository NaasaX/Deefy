<?php
namespace src\classes\auth;

use src\classes\repository\DeefyRepository;

class Authz {

    public static function checkPlaylistOwner(int $playlistId): bool {
        if (!isset($_SESSION['user'])) {
            return false;
        }

        $user = unserialize($_SESSION['user']);


        if (!$user instanceof User) {
            return false;
        }

        $userId = $user->getId();
        $userRole = $user->getRole();

        if ($userRole == 100) {
            return true;
        }

        // Vérifiez si l'utilisateur est le propriétaire de la playlist
        $query = "SELECT * FROM user2playlist WHERE id_user = :userId AND id_pl = :playlistId";
        $stmt = DeefyRepository::getInstance()->pdo->prepare($query);
        $stmt->execute(['userId' => $userId, 'playlistId' => $playlistId]);
        $result = $stmt->fetch();

        return !empty($result);
    }

}
