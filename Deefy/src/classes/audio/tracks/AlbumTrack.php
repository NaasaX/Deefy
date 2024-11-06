<?php
namespace src\classes\audio\tracks;

class AlbumTrack extends AudioTrack
{
    private ?string $artiste;
    private ?string $album;
    private ?int $annee;
    private ?int $numero_piste;

    public function __construct(string $titre, string $nom_fichier_audio, int $duree = 0, string $artiste = "Inconnu", string $album = "Inconnu", int $annee = 0, int $numero_piste = 0)
    {
        parent::__construct($titre, $nom_fichier_audio, $duree);
        $this->artiste = $artiste;
        $this->album = $album;
        $this->annee = $annee;
        $this->numero_piste = $numero_piste;
    }

    public function __toString()
    {
        return json_encode($this);
    }

    public function __get($attrname)
    {
        if (property_exists($this, $attrname)) {
            return $this->$attrname;
        }
        // Vérifiez aussi dans AudioTrack
        if (property_exists(get_parent_class(), $attrname)) {
            return parent::__get($attrname);
        }
        throw new \Exception("Invalid property: $attrname");
    }


    public function __set($attrname, $value)
    {
        if (property_exists($this, $attrname)) {
            $this->$attrname = $value;
        } else {
            parent::__set($attrname, $value); // Vérifie aussi dans AudioTrack
        }
    }
}
