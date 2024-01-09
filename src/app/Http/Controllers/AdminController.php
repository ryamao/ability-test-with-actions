<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Contact;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $categories = Category::all();
        $contacts = Contact::with('category')
            ->orderBy('created_at')
            ->paginate(10);
        return view('admin', compact('categories', 'contacts'));
    }
}
