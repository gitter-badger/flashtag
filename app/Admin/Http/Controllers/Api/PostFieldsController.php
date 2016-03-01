<?php

namespace Flashtag\Admin\Http\Controllers\Api;

use Flashtag\Admin\Http\Controllers\Controller;
use Flashtag\Data\Field;

class PostFieldsController extends Controller
{
    public function index()
    {
        return Field::orderBy('created_at', 'desc')->get();
    }
}