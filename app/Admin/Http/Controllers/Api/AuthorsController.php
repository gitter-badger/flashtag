<?php

namespace Flashtag\Admin\Http\Controllers\Api;

use Flashtag\Admin\Http\Controllers\Controller;
use Flashtag\Data\Author;

class AuthorsController extends Controller
{
    public function index()
    {
        return Author::orderBy('created_at', 'desc')->get();
    }
}