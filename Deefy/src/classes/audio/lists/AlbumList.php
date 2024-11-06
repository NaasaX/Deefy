<?php

namespace src\classes\audio\lists;

class AlbumList extends AudioList
{

    private String $artiste;
    private String $date;

    function __construct(string $nom, array $tracks)
    {
        parent::__construct($nom, $tracks);
    }

    function artisteSetter(String $artiste): void
    {
        $this.$this->artiste;
    }

    function dateSetter(String $date): void
    {
        $this.$this->date;
    }



}