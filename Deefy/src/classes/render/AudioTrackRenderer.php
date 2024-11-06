<?php

namespace src\classes\render;

use src\classes\audio\tracks\AudioTrack;

class AudioTrackRenderer implements Renderer
{

    protected AudioTrack $track;

    public function __construct(AudioTrack $track)
    {
        $this->track = $track;
    }

    public function render(int $mode): string
    {
        $html = '';
        switch ($mode) {
            case self::COMPACT: // MODE COMPACT
                $html = '<div class="track">';
                $html .= '<h2>' . $this->track->titre . '</h2>';
                $html .= '<p>' . $this->track->genre . '</p>';
                $html .= '</div>';
                break;
            case self::LONG: //MODE LONG
                $html = '<div class="track">';
                $html .= '<h2>' . $this->track->titre . '</h2>';
                $html .= '<p>' . $this->track->genre . '</p>';
                $html .= '<p>' . $this->track->duree . '</p>';
                $html .= '<audio controls>';
                $html .= '<source src="' . $this->track->filename . '" type="audio/mpeg">';
                $html .= '</audio>';
                $html .= '</div>';
                break;
        }

        return $html;

    }
}