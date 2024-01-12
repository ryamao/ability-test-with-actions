<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Gender;
use App\Http\Requests\ContactRequest;
use App\Models\Category;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * お問い合わせフォームのコントローラクラス。
 * 以下の3ページを担当している。
 *
 * * お問い合わせフォーム入力ページ
 * * お問い合わせフォーム確認ページ
 * * サンクスページ
 */
class ContactController extends Controller
{
    /**
     * 「GET /」のアクション。
     * 入力ページを表示する。
     */
    public function index(): View
    {
        $categories = Category::all();
        return view('contact', compact('categories'));
    }

    /**
     * 「POST /」のアクション。
     * 入力ページを表示する。
     * 確認ページから修正ボタンで戻ってきた場合に使われる。
     */
    public function revise(ContactRequest $request): View
    {
        $categories = Category::all();
        return view('contact', compact('categories'));
    }

    /**
     * 「POST /confirm」のアクション。
     * 確認ページを表示する。
     * 入力ページの確認画面ボタンから送信される。
     */
    public function confirm(ContactRequest $request): View
    {
        $easternOrderedName = request('last_name') . '　' . request('first_name');
        $genderName = Gender::from((int) request('gender'))->name();
        $phoneNumber = request('area_code') . request('city_code') . request('subscriber_code');
        $categoryContent = Category::find($request->input('category_id'))->content;
        return view('confirm', compact('easternOrderedName', 'phoneNumber', 'genderName', 'categoryContent'));
    }

    /**
     * 「POST /contact」のアクション。
     * お問い合わせ情報をデータベースに保存する。
     * 確認ページの送信ボタンから送信される。
     */
    public function store(ContactRequest $request): RedirectResponse
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

        return redirect('/thanks');
    }

    /**
     * 「GET /thanks」のアクション。
     * サンクスページを表示する。
     */
    public function thanks(): View
    {
        return view('thanks');
    }
}
