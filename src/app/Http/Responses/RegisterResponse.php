<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

/**
 * ユーザ登録完了後の遷移先を決めるクラス。
 * ログインページに遷移するために一度ログアウトしている。
 */
class RegisterResponse implements RegisterResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        Auth::logout();
        return redirect('/login');
    }
}
