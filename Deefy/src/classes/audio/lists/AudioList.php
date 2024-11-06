<?php

namespace src\classes\audio\lists;

use src\classes\exception\InvalidPropertyNameException;


class AudioList
{
    protected string $nom;
    protected int $nbpistes = 0;
    protected int $dureetotale = 0;
    protected array $tracks = [];
    protected int $ID;

    public function __construct(string $nom, array $tracks = [])
    {
        $this->nom = $nom;
        $this->tracks = $tracks;
        $this->nbpistes = count($tracks);
        foreach ($tracks as $track) {
            $this->dureetotale += $track->duree;
        }

    }


    public function __get($attrname)
    {
        if (property_exists($this, $attrname)) {
            return $this->$attrname;
        }
        throw new InvalidPropertyNameException($attrname);
    }

    public function __set($attrname, $value)
    {
        if (property_exists($this, $attrname)) {
            $this->$attrname = $value;
        } else {
            throw new InvalidPropertyNameException($attrname);
        }
    }


    public function setID(int $id): void
    {
        $this->ID = $id;
    }



}