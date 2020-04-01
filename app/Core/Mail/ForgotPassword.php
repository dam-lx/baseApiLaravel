<?php

namespace App\Core\Mail;

use App\Core\Common\LoggingConst;
use App\Core\Helpers\CommonHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ForgotPassword extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $params;
    protected $resetKey;
    public function __construct($params, $resetKey)
    {
        $this->params = $params;
        $this->resetKey = $resetKey;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $urlForgot = env('URL_BASE'). 'forgot/reset/'. $this->resetKey;

        try {
            $from = env('MAIL_USERNAME');
            $fromName = env('FROM_NAME','マーカムインターナショナル');
            $mail_subject = env('MAIL_SUBJECT_FORGOT_PASS', '[WOD ワインオンデマンド 古酒(オールドヴィンテージ)・希少ワイン専用] パスワード変更のご確認');
            $mail = $this->subject($mail_subject)
                ->from($from,$fromName)
                ->view('template_mail.forgot_password')->with([
                    'urlForgot' =>  $urlForgot,
                ]);
            return $mail;
        }
        catch (\Exception $e){
            CommonHelper::CommonLog($e);
        }
    }
}
