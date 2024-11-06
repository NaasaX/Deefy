<?php
namespace src\classes\audio\tracks;

class PodcastTrack extends AudioTrack
{
    private ?string $auteur;
    private ?string $date;

    public function __construct(string $titre, string $nom_fichier_audio, int $duree = 0, string $auteur = "Inconnu", string $date = "Inconnu")
    {
        parent::__construct($titre, $nom_fichier_audio, $duree);
        $this->auteur = $auteur;
        $this->date = $date;
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
            if ($attrname === 'duree') {
                if ($value < 0) {
                    throw new \Exception("Invalid property value for: $attrname");
                }
            }
            $this->$attrname = $value;
        } else {
            throw new \Exception("Invalid property: $attrname");
        }
    }
}
