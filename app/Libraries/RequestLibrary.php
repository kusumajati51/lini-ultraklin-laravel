<?php

namespace App\Libraries;

class RequestLibrary
{
    protected $filter, $request;

    public function __construct($filter, $request)
    {
        $this->filter = $filter;
        $this->request = $request;
    }

    public function has($name)
    {
        return $this->request->has($name);
    }

    public function hasSameCount($name, $total)
    {
        return count($this->request->input($name)) == $total;
    }

    public function in($name, $array)
    {
        return in_array($this->request->input($name), $array);
    }

    public function isArray($name)
    {
        return is_array($this->request->input($name));
    }
}
