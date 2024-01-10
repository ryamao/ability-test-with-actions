<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Category;
use App\Models\Contact;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        $categories = Category::all();
        return view('contact', compact('categories'));
    }

    public function revise(ContactRequest $request): View
    {
        $params = $request->validated();
        $params['categories'] = Category::all();
        return view('contact', $params);
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

    public function store(ContactRequest $request): View
    {
        $data = $request->only([
            'category_id',
            'first_name',
            'last_name',
            'gender',
            'email',
            'address',
            'building',
            'detail',
        ]);

        $data['tel'] = '';
        foreach ($request->only(['area_code', 'city_code', 'subscriber_code']) as $key => $code) {
            $data['tel'] .= $code;
        }

        Contact::create($data);

        return view('thanks');
    }
}
