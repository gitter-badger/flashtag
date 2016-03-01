<?php

namespace Flashtag\Admin\Http\Controllers\Api;

use Flashtag\Admin\Http\Controllers\Controller;
use Flashtag\Data\PostList;

class PostListsController extends Controller
{
    public function index()
    {
        return PostList::orderBy('created_at', 'desc')->get();
    }
}