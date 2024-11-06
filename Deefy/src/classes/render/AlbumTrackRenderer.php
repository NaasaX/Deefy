<?php

namespace src\classes\render;

use src\classes\audio\tracks\AudioTrack;

class AlbumTrackRenderer extends AudioTrackRenderer
{


    public function __construct(AudioTrack $track)
    {
        parent::__construct($track);
    }


    public function render(int $mode): string
    {
        $html = parent::render($mode);
        switch ($mode) {
            case self::COMPACT: // MODE COMPACT
                $html .= '<p>' . $this->track->artiste . '</p>';
                break;
            case self::LONG: //MODE LONG
                $html .= '<p>' . $this->track->artiste . '</p>';
                $html .= '<p>' . $this->track->album . '</p>';
                break;
        }

        return $html;

    }



}