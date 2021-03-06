<?php

namespace App\Core\Jobs;

use App\Core\Mail\ForgotPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendMailForgotPassword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tries = 2;
    public $timeout = 600;

    protected $params;
    protected $resetKey;
    public function __construct($params, $resetKey)
    {
        $this->params = $params;
        $this->resetKey = $resetKey;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!empty($this->params['email'])) {
            Mail::to($this->params['email'])
                ->bcc(env('EMAIL_ADMIN','thanhtrucle.1993@gmail.com'))
                ->send(new ForgotPassword($this->params, $this->resetKey));
        }
    }
}
