<?php

namespace Perelson;

class RootController
{
    public function get_index()
    {
        View::show('landing');
    }
}
