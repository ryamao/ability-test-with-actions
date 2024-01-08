<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
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
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'max:255'],
        ];

        if ($this->is('/login')) {
            unset($rules['name']);
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'お名前を入力してください',
            'name.string' => 'お名前を入力してください',
            'name.max' => 'お名前は255文字以内で入力してください',
            'email.required' => 'メールアドレスを入力してください',
            'email.string' => 'メールアドレスを入力してください',
            'email.email' => 'メールアドレスは「ユーザー名@ドメイン」形式で入力してください',
            'email.max' => 'メールアドレスは255文字以内で入力してください',
            'password.required' => 'パスワードを入力してください',
            'password.string' => 'パスワードを入力してください',
            'password.max' => 'パスワードは255文字以内で入力してください',
        ];
    }
}
