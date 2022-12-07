<?php

session_start();

class CalculadoraMilan {
    protected $screen;
    protected $memoria;

    public function __construct() {

    }

    public function getScreen() {
        return $this->screen;
    }

    public function number($val) {
        $this->screen = $this->screen .$dig;
    }
}
>