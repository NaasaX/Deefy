<?php
namespace src\classes\audio\tracks;

use src\classes\exception\InvalidPropertyNameException;
use src\classes\exception\InvalidPropertyValueException;

class AudioTrack
{
    protected string $titre;
    protected string $genre;
    protected int $duree; // en secondes
    protected string $filename;
    protected int $ID;

    public function __construct(string $titre, string $nom_fichier_audio, int $duree)
    {
        $this->titre = $titre;
        $this->filename = $nom_fichier_audio;
        $this->setDuree($duree);
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
        throw new InvalidPropertyNameException($attrname);
    }

    public function setDuree(int $d): void
    {
        if ($d < 0) {
            throw new InvalidPropertyValueException("duree", $d);
        }
        $this->duree = $d;
    }

    public function setID(int $id): void
    {
        $this->ID = $id;
    }
}