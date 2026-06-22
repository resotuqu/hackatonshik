<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Support\PostLoginRedirect;
use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        $user = $request->user();
        $redirect = $user !== null
            ? PostLoginRedirect::intendedUrl($user)
            : route('home');

        return $request->wantsJson()
            ? new JsonResponse('', 204)
            : redirect()->intended($redirect);
    }
}
