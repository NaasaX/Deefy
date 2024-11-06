<?php


namespace src\classes\audio\lists;

class Playlists extends AudioList
{
    private ?int $id;

    public function __construct(string $nom, ?int $id = null)
    {
        parent::__construct($nom);
        $this->id = $id;
    }

    // Setters et Getters pour l'ID
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    // Méthode pour ajouter une piste
    public function addTrack($track): void
    {
        $this->tracks[] = $track;
        $this->nbpistes = count($this->tracks);
        $this->dureetotale += $track->duree;
    }

    // Méthode pour supprimer une piste
    public function deleteTrack($index): void
    {
        if (isset($this->tracks[$index])) {
            $this->dureetotale -= $this->tracks[$index]->duree;
            unset($this->tracks[$index]);
            $this->tracks = array_values($this->tracks);
            $this->nbpistes = count($this->tracks);
        } else {
            throw new \Exception("L'index $index du track n'existe pas");
        }
    }

    public function setTracks(array $tracks): void
    {
        $this->tracks = $tracks;
        $this->nbpistes = count($tracks);
        $this->dureetotale = array_sum(array_map(fn($track) => $track->duree, $tracks));
    }


}


