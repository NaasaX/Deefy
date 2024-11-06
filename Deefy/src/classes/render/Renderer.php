<?php

namespace src\classes\render;

interface Renderer

{

    const COMPACT = 1;
    const LONG = 2;

    public function render(int $mode): String;



}