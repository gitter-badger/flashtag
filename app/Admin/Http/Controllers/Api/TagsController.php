<?php

namespace Flashtag\Admin\Http\Controllers\Api;

use Flashtag\Admin\Http\Controllers\Controller;
use Flashtag\Data\Tag;

class TagsController extends Controller
{
    public function index()
    {
        return Tag::orderBy('created_at', 'desc')->get();
    }
}