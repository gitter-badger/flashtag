<?php

namespace Flashtag\Admin\Http\Controllers\Api;

use Flashtag\Admin\Http\Controllers\Controller;
use Flashtag\Data\Category;

class CategoriesController extends Controller
{
    public function index()
    {
        return Category::with('tags')->orderBy('created_at', 'desc')->get();
    }
}
