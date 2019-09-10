<?php

namespace App\Interfaces\V1;

interface CollectionInterface {
    public function filterMethods();

    public function model();

    public function collect();

    public function filter();

    public function get();

    public function paginate();
}