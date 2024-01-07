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

    public function revise(ContactRequest $request): View
    {
        $params = $request->validated();
        $params['categories'] = Category::all();
        return view('index', $params);
    }

    public function confirm(ContactRequest $request): View
    {
        $validated = $request->validated();

        $validated['gender_name'] = match ((int) $validated['gender']) {
            1 => '男性',
            2 => '女性',
            3 => 'その他',
        };

        $validated['category_content'] = Category::find($validated['category_id'])->content;

        return view('confirm', $validated);
    }
}
