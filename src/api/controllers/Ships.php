<?php

class Ships {
    private $name;
    private $size;
    private $positions;

    public function __construct($name, $size) {
        $this->name = $name;
        $this->size = $size;
    }

    public function getName() {
        return $this->name;
    }

    public function getSize() {
        return $this->size;
    }

    public function setPositions($positions) {
        $this->positions = $positions;
    }
}