<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    protected $redirect = '/';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'last_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'integer', 'between:1,3'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'area_code' => ['required', 'digits_between:1,5'],
            'city_code' => ['required', 'digits_between:1,5'],
            'subscriber_code' => ['required', 'digits_between:1,5'],
            'address' => ['required', 'string', 'max:255'],
            'building' => ['nullable', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'detail' => ['required', 'string', 'max:120'],
        ];
    }

    public function messages()
    {
        return [
            'last_name.*' => '姓を入力してください',
            'first_name.*' => '名を入力してください',
            'gender.*' => '性別を選択してください',
            'email.required' => 'メールアドレスを入力してください',
            'email.*' => 'メールアドレスはメール形式で入力してください',
            'area_code.required' => '電話番号を入力してください',
            'area_code.digits_between' => '電話番号は5桁までの数字で入力してください',
            'city_code.required' => '電話番号を入力してください',
            'city_code.digits_between' => '電話番号は5桁までの数字で入力してください',
            'subscriber_code.required' => '電話番号を入力してください',
            'subscriber_code.digits_between' => '電話番号は5桁までの数字で入力してください',
            'address.*' => '住所を入力してください',
            'category_id.*' => 'お問い合わせの種類を選択してください',
            'detail.max' => 'お問合せ内容は120文字以内で入力してください',
            'detail.*' => 'お問い合わせ内容を入力してください',
        ];
    }
}
