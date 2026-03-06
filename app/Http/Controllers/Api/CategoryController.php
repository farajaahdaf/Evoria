<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\EventCategory;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = EventCategory::all();
        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }
}
