<?php

namespace App\Core\Mail;

use App\Core\Helpers\CommonHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Core\Helpers\SendMail;

class RegisterNormal extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $params;
    protected $shortLink;

    public function __construct($params, $shortLink)
    {
        $this->params = $params;
        $this->shortLink = $shortLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        try {
            $this->params['name'] = $this->params['name01'] . $this->params['name02'] . '様';

            $from = env('MAIL_USERNAME');
            $from_name = env('MAIL_FROM_NAME', 'マーカムインターナショナル');
            $mail_subject = env('MAIL_SUBJECT', '[マーカムインターナショナル] 会員登録のご確認');
            $mail = $this->subject($mail_subject)
                ->from($from, $from_name)
                ->view('template_mail.register_normal', [
                    'name' => $this->params['name'],
                    'urlActive' => $this->shortLink]);
            return $mail;
        } catch (\Exception $e) {
            CommonHelper::CommonLog($this->shortLink);
        }
    }
}
