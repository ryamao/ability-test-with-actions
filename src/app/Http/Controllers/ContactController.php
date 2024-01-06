<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Category;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        $categories = Category::all();
        return view('index', compact('categories'));
    }

    public function confirm(ContactRequest $request): View
    {
        dd($request->validated());
        return view('confirm', $request->validated());
    }
}
