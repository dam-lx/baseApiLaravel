<?php

namespace App\Core\Mail;

use App\Core\Helpers\CommonHelper;
use App\Core\Helpers\SendMail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Contact extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $params;
    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        try {
            $from         = env('MAIL_USERNAME');
            $mail_subject = env('MAIL_SUBJECT_CONTACT', '[WOD ワインオンデマンド 古酒(オールドヴィンテージ)・希少ワイン専用] お問い合わせを受け付けました。');
            $mail         = $this->subject($mail_subject)
                ->from($from)
                ->view('template_mail.contact',['param'=>$this->params]);
            return $mail;
        }
        catch (\Exception $e){
            CommonHelper::CommonLog($e->getMessage());
        }
    }
}
