<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Gender;
use App\Models\Category;
use App\Models\Contact;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * 管理画面のコントローラクラス。
 */
class AdminController extends Controller
{
    /** 検索文字列の最大長。この長さ以上は切り捨てている。 */
    private const int MAX_SEARCH_STRING_LENGTH = 255;

    /** エクスポートするCSVのヘッダ */
    private const array CSV_HEADER = [
        'contact_id',
        'last_name',
        'first_name',
        'gender',
        'gender_name',
        'email',
        'address',
        'building',
        'category_id',
        'category_content',
        'detail',
        'created_at',
        'updated_at',
    ];

    /**
     * 「GET /admin」のアクション。
     * 管理画面を表示する。
     *
     * 以下のクエリストリングを受け付ける。
     *
     * * search   - 検索文字列
     * * gender   - 性別 (1 ~ 3)
     * * category - お問い合わせ種類 (categories テーブルの id)
     * * date     - お問い合わせ年月日 (例: 2024-01-10)
     * * page     - ページネーションが使用
     */
    public function index(Request $request): View
    {
        $categories = Category::all();
        $contacts = $this->makeQueryForContactsFromQueryString($request)->paginate(10);
        $contacts->withQueryString();
        return view('admin', compact('categories', 'contacts'));
    }

    /**
     * 「DELETE /admin/{contact}」のアクション。
     * お問い合わせ情報の削除を行う。
     */
    public function destroyContact(Request $request, Contact $contact): RedirectResponse
    {
        $contact->delete();

        $params = $request->only(['search', 'gender', 'category', 'date', 'page']);
        if (empty($params)) return redirect('/admin');

        $queryString = http_build_query($params);
        return redirect("/admin?{$queryString}");
    }

    /**
     * 「GET /admin/export」のアクション。
     * CSVファイルのストリーミングを行う。
     * 「GET /admin」と同じクエリリクエストを受け付ける。
     * ただし page は無視する。
     */
    public function exportContacts(Request $request): StreamedResponse
    {
        $contacts = $this->makeQueryForContactsFromQueryString($request)->lazy();
        $timestampString = CarbonImmutable::now()->format('YmdHis');

        return response()->streamDownload(
            callback: function () use ($contacts) {
                $output = fopen('php://output', 'w');

                fputcsv($output, self::CSV_HEADER);

                foreach ($contacts as $contact) {
                    fputcsv($output, [
                        $contact['id'],
                        $contact['last_name'],
                        $contact['first_name'],
                        $contact['gender'],
                        $contact->gender()->name(),
                        $contact['email'],
                        $contact['address'],
                        $contact['building'],
                        $contact->category->id,
                        $contact->category->content,
                        $contact['detail'],
                        $contact['created_at'],
                        $contact['updated_at'],
                    ]);
                }

                fclose($output);
            },
            name: "contacts_{$timestampString}.csv",
            headers: [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ],
        );
    }

    /** クエリストリングの内容からデータベースのクエリを作成する。 */
    private function makeQueryForContactsFromQueryString(Request $request): Builder
    {
        $keywordsString = $this->keywordsStringFromQueryString($request);
        $gender = $this->genderFromQueryString($request);
        $categoryId = $this->categoryIdFromQueryString($request);
        $datetime = $this->dateFromQueryString($request);

        return Contact::with('category')
            ->searchByKeywords($keywordsString)
            ->searchByGender($gender)
            ->searchByCategory($categoryId)
            ->searchByDate($datetime)
            ->orderBy('created_at');
    }

    /** クエリストリングから検索文字列を正規化して取得する。 */
    private function keywordsStringFromQueryString(Request $request): ?string
    {
        if (is_null($request->query('search'))) return null;
        $search = $request->query('search');
        return substr($search, 0, self::MAX_SEARCH_STRING_LENGTH);
    }

    /** クエリストリングから性別を取得する。 */
    private function genderFromQueryString(Request $request): ?Gender
    {
        if (!is_numeric($request->query('gender'))) return null;
        return Gender::tryFrom((int) $request->query('gender'));
    }

    /** クエリストリングからお問い合わせ種類を正規化して取得する。 */
    private function categoryIdFromQueryString(Request $request): ?int
    {
        if (!is_numeric($request->query('category'))) return null;
        $categoryId = (int) $request->query('category');
        if (is_null(Category::find($categoryId))) return null;
        return $categoryId;
    }

    /** クエリストリングからお問い合わせ年月日を正規化して取得する。 */
    private function dateFromQueryString(Request $request): ?CarbonImmutable
    {
        if (empty($request->query('date'))) return null;
        $dateString = $request->query('date');
        if (!preg_match('/^[0-9]{4}-(?:0?[1-9]|1[0-2])-(?:0?[1-9]|[12][0-9]|3[01])$/', $dateString)) return null;
        return CarbonImmutable::parse($dateString);
    }
}
