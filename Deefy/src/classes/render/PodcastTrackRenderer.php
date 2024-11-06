<?php

namespace src\classes\render;

use src\classes\audio\tracks\PodcastTrack;

class PodcastTrackRenderer extends AudioTrackRenderer
{


    public function __construct(PodcastTrack $track)
    {
        parent::__construct($track);
    }

    public function render(int $mode): string
    {
        $html = parent::render($mode);
        switch ($mode) {
            case self::COMPACT: // MODE COMPACT
                $html .= '<p>' . $this->track->auteur . '</p>';
                break;
            case self::LONG: //MODE LONG
                $html .= '<p>' . $this->track->auteur . '</p>';
                $html .= '<p>' . $this->track->date . '</p>';
                break;
        }

        return $html;

    }
}