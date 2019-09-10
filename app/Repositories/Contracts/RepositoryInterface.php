<?php

namespace App\Repositories\Contracts;

interface RepositoryInterface {
    public function get();
    public function paginate();
}
