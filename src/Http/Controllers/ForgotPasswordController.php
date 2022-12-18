<?php

namespace Tripsome\Blog\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;
use Tripsome\Blog\Mail\ResetPasswordEmail;
use Tripsome\Blog\BlogAuthor;

class ForgotPasswordController extends Controller
{
    /**
     * Show the reset-password form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showResetRequestForm()
    {
        return view('blog::request-password-reset');
    }

    /**
     * Send password reset email.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResetLinkEmail()
    {
        validator(request()->all(), [
            'email' => 'required|email',
        ])->validate();

        if ($author = BlogAuthor::whereEmail(request('email'))->first()) {
            cache(['password.reset.'.$author->id => $token = Str::random()],
                now()->addMinutes(30)
            );

            Mail::to($author->email)->send(new ResetPasswordEmail(
                encrypt($author->id.'|'.$token)
            ));
        }

        return redirect()->route('blog.password.forgot')->with('sent', true);
    }

    /**
     * Show the new password to the user.
     *
     * @param  string  $token
     * @return \Illuminate\Http\Response
     */
    public function showNewPassword($token)
    {
        try {
            $token = decrypt($token);

            [$authorId, $token] = explode('|', $token);

            $author = BlogAuthor::findOrFail($authorId);
        } catch (Throwable $e) {
            return redirect()->route('blog.password.forgot')->with('invalidResetToken', true);
        }

        if (cache('password.reset.'.$authorId) != $token) {
            return redirect()->route('blog.password.forgot')->with('invalidResetToken', true);
        }

        cache()->forget('password.reset.'.$authorId);

        $author->password = \Hash::make($password = Str::random());

        $author->save();

        return view('blog::reset-password', [
            'password' => $password,
        ]);
    }
}
