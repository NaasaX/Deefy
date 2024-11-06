<?php

namespace src\classes\repository;

use src\classes\audio\lists\Playlists;
use src\classes\audio\tracks\AudioTrack;
use src\classes\audio\tracks\AlbumTrack;
use src\classes\audio\tracks\PodcastTrack;
use src\classes\auth\AuthProvider;
use PDO;
use PDOException;

class DeefyRepository
{
    public PDO $pdo;
    private static ?DeefyRepository $instance = null;
    private static array $config = [];

    private function __construct()
    {
        try {
            $this->pdo = new PDO(
                self::$config['dsn'],
                self::$config['username'],
                self::$config['password']
            );

        } catch (PDOException $e) {
            echo 'Erreur de connexion à la base de données : ' . htmlspecialchars($e->getMessage());
            exit;
        }
    }

    public static function getInstance(): DeefyRepository
    {
        if (is_null(self::$instance)) {
            self::$instance = new DeefyRepository();
        }
        return self::$instance;
    }

    public static function setConfig(string $file): void
    {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new \Exception("Erreur lors de la lecture du fichier de configuration");
        }
        self::$config = $conf;
    }

    public static function getUserPlaylists(int $userId): array
    {
        $query = "SELECT * FROM user2playlist INNER JOIN playlist ON user2playlist.id_pl = playlist.ID WHERE user2playlist.id_user = ?";
        $stmt = self::getInstance()->pdo->prepare($query);
        $stmt->execute([$userId]);
        $playlists = [];
        while ($row = $stmt->fetch()) {
            $playlist = new Playlists($row['nom']);
            $playlist->setID($row['id']);
            $playlists[] = $playlist;
        }
        return $playlists;
    }


    public function saveEmptyPlaylist(Playlists $p): Playlists
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Vérifiez si l'utilisateur est connecté
        try {
            $user = AuthProvider::getSignedInUser();
        } catch (AuthException $e) {
            throw new \Exception("L'utilisateur n'est pas connecté.");
        }

        // Préparez et exécutez la requête pour insérer la playlist
        $query = "INSERT INTO playlist (nom) VALUES (:nom)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['nom' => $p->__get('nom')]);

        // Récupérez l'ID de la nouvelle playlist
        $playlistId = $this->pdo->lastInsertId();
        $p->setID($playlistId);

        // Lier la playlist à l'utilisateur
        $userId = $user->getId();
        $linkQuery = "INSERT INTO user2playlist (id_user, id_pl) VALUES (:id_user, :id_pl)";
        $linkStmt = $this->pdo->prepare($linkQuery);
        $linkStmt->execute(['id_user' => $userId, 'id_pl' => $playlistId]);

        // Ajoutez la playlist à la session
        $_SESSION['current_playlist'] = $p;
        $_SESSION['current_playlist_id'] = $playlistId;

        return $p;
    }

    public static function getPlaylists(): array
    {
        $query = "SELECT * FROM playlist";
        $stmt = self::getInstance()->pdo->query($query);
        $playlists = [];
        while ($row = $stmt->fetch()) {
            $playlist = new Playlists($row['nom']);
            $playlist->setID($row['id']);
            $playlists[] = $playlist;
        }
        return $playlists;
    }

    public function saveTrack($track): int
    {
        // Vérifiez si la session est démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Obtenir l'ID de la playlist courante en session
        $currentPlaylist = $_SESSION['current_playlist'] ?? null;
        $currentPlaylistId = $currentPlaylist ? $_SESSION['current_playlist_id'] : null;

        // Requête d'insertion dans la table track
        $query = "INSERT INTO track (titre, duree, genre, filename, artiste_album, titre_album, annee_album, numero_album, date_posdcast, auteur_podcast, type) 
              VALUES (:titre, :duree, :genre, :filename, :artiste_album, :titre_album, :annee_album, :numero_album, :date_posdcast, :auteur_podcast, :type)";
        $stmt = $this->pdo->prepare($query);

        // Préparer les valeurs
        $data = [
            'titre' => $track->__get('titre'),
            'duree' => $track->__get('duree'),
            'genre' => $track->__get('genre'),
            'filename' => $track->__get('filename'),
            'artiste_album' => $track instanceof AlbumTrack ? $track->__get('artiste') : null,
            'titre_album' => $track instanceof AlbumTrack ? $track->__get('album') : null,
            'annee_album' => $track instanceof AlbumTrack ? $track->__get('annee') : null,
            'numero_album' => $track instanceof AlbumTrack ? $track->__get('numero_piste') : null,
            'date_posdcast' => $track instanceof PodcastTrack ? $track->__get('date') : null,
            'auteur_podcast' => $track instanceof PodcastTrack ? $track->__get('auteur') : null,
            'type' => $track instanceof AlbumTrack ? 'A' : 'P'
        ];

        try {
            // Exécute la requête pour insérer la piste
            if ($stmt->execute($data)) {
                // Récupérer l'ID généré pour la piste ajoutée
                $trackId = (int) $this->pdo->lastInsertId();
                $track->setId($trackId);

                // Ajouter la piste à la playlist courante si elle existe
                if ($currentPlaylistId !== null) {
                    $this->addTrackToPlaylist($trackId, $currentPlaylistId);
                }

                return $trackId;
            } else {
                echo "Erreur lors de l'exécution de la requête : ";
                print_r($stmt->errorInfo());
                return 0;
            }
        } catch (\Exception $e) {
            echo "Erreur lors de l'ajout de la piste : " . htmlspecialchars($e->getMessage());
            return 0;
        }
    }




    public function addTrackToPlaylist(int $trackId, int $playlistId): void
    {
        $query = "INSERT INTO playlist2track (id_pl, id_track) VALUES (:playlistId, :trackId)";
        $stmt = $this->pdo->prepare($query);

        try {
            $stmt->execute([
                'playlistId' => $playlistId,
                'trackId' => $trackId
            ]);
        } catch (\Exception $e) {
            echo "Erreur lors de l'ajout de la piste à la playlist : " . htmlspecialchars($e->getMessage());
        }
    }



    public function findPlaylistById(int $id): Playlists
    {
        $query = "SELECT id, nom FROM playlist WHERE ID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch($this->pdo::FETCH_ASSOC);

        if (!$row) {
            throw new \Exception("Aucune playlist trouvée pour l'ID $id");
        }

        $playlist = new Playlists($row['nom']);
        $playlist->setID($row['id']);
        $playlist->setTracks($this->getTracksForPlaylist($playlist));

        // Ajoutez la playlist à la session
        $_SESSION['current_playlist'] = $playlist;
        $_SESSION['current_playlist_id'] = $row['id'];

        return $playlist;
    }

    public function getTracksForPlaylist(Playlists $p): array
    {
        $query = "SELECT track.ID, track.titre, track.filename, track.duree 
              FROM track 
              INNER JOIN playlist2track ON track.ID = playlist2track.id_track 
              WHERE playlist2track.id_pl = :id_pl";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_pl' => $p->getId()]);

        $tracks = [];
        while ($row = $stmt->fetch()) {
            // Vérifiez que 'filename' n'est pas null
            if (empty($row['filename'])) {
                throw new \Exception("Le champ 'filename' est manquant ou vide pour la piste ID: " . $row['ID']);
            }

            $track = new AudioTrack($row['titre'], $row['filename'], $row['duree']);
            $track->setID($row['ID']);
            $tracks[] = $track;
        }

        return $tracks;
    }



    public function getPlaylistTracksById(int $id): array
    {
        $playlist = $this->findPlaylistById($id);
        return $this->getTracksForPlaylist($playlist);
    }


    public function register(string $email, string $passwd): void
    {
        AuthProvider::register($this->pdo, $email, $passwd);
    }

    public function signin(string $email, string $passwd): void
    {
        AuthProvider::signin($this->pdo, $email, $passwd);
    }

    public function getTrackInPlaylist($getId, ?int $trackId)
    {
        $query = "SELECT * FROM playlist2track WHERE id_pl = :id_pl AND id_track = :id_track";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_pl' => $getId, 'id_track' => $trackId]);
        return $stmt->fetch();
    }

}
