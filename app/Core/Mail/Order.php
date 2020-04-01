<?php

namespace App\Core\Mail;

use App\Core\Dao\SDB;
use App\Core\Helpers\CommonHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Core\Helpers\SendMail;

class Order extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $order;
    protected $shippings;

    public function __construct($order, $shippings)
    {
        $this->order = $order;
        $this->shippings = $shippings;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        try {
            $template = SDB::table('dtb_mail_template')
                ->where('template_id', 1)
                ->first();
            $from = env('MAIL_USERNAME');
            $from_name = $template->name;
            $mail_subject = $template->subject;
            $body = view('template_mail.order', [
                'template_mail' => $template,
                'order'=> $this->order,
                'shippings'=>$this->shippings,
            ])->render();
            $body = (trim(strip_tags($body)));
            SDB::table('dtb_mail_history')->insert([
                'order_id' => $this->order[0]->id,
                'template_id'=>1,
                'send_date'=>CommonHelper::dateNow(),
                'subject'=>$mail_subject,
                'mail_body'=>$body
            ]);
            $mail = $this->subject($mail_subject)
                ->from($from,$from_name)
                ->view('template_mail.order',[
                    'template_mail' => $template,
                    'order'=>$this->order,
                    'shippings'=>$this->shippings,
                ]);
            return $mail;
        } catch (\Exception $e) {
            CommonHelper::CommonLog($e->getMessage());
        }
    }
}
