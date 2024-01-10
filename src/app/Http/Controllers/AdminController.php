<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Contact;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    private const MAX_SEARCH_STRING_LENGTH = 255;

    public function index(Request $request): View
    {
        $keywordsString = $this->keywordsStringFromInput($request);
        $gender = $this->genderFromInput($request);
        $categoryId = $this->categoryIdFromInput($request);
        $datetime = $this->dateFromInput($request);

        $categories = Category::all();
        $contacts = Contact::with('category')
            ->searchByKeywords($keywordsString)
            ->searchByGender($gender)
            ->searchByCategory($categoryId)
            ->searchByDate($datetime)
            ->orderBy('created_at')
            ->paginate(10);

        $contacts->appends([
            'search' => $keywordsString ?? '',
            'gender' => $gender ?? '',
            'category' => $categoryId ?? '',
            'date' => $datetime?->format('Y-m-d') ?? '',
        ]);

        return view('admin', compact(
            'categories',
            'contacts',
            'keywordsString',
            'gender',
            'categoryId',
            'datetime',
        ));
    }

    public function destroyContact(Request $request, Contact $contact): RedirectResponse
    {
        $contact->delete();

        $params = $request->only(['search', 'gender', 'category', 'date', 'page']);
        if (empty($params)) return redirect('/admin');

        $queryString = http_build_query($params);
        return redirect("/admin?{$queryString}");
    }

    private function keywordsStringFromInput(Request $request): ?string
    {
        if (is_null($request->input('search'))) return null;
        $search = $request->input('search');
        return substr($search, 0, self::MAX_SEARCH_STRING_LENGTH);
    }

    private function genderFromInput(Request $request): ?int
    {
        if (!is_numeric($request->input('gender'))) return null;
        $gender = (int) $request->input('gender');
        return min(max($gender, 1), 3);
    }

    private function categoryIdFromInput(Request $request): ?int
    {
        if (!is_numeric($request->input('category'))) return null;
        $categoryId = (int) $request->input('category');
        if (is_null(Category::find($categoryId))) return null;
        return $categoryId;
    }

    private function dateFromInput(Request $request): ?CarbonImmutable
    {
        if (empty($request->input('date'))) return null;
        $dateString = $request->input('date');
        if (!preg_match('/^[0-9]{4}-(?:0?[1-9]|1[0-2])-(?:0?[1-9]|[12][0-9]|3[01])$/', $dateString)) return null;
        return CarbonImmutable::parse($dateString);
    }
}
