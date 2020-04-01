<?php

namespace App\Core\Jobs;

use App\Core\Mail\RegisterNormal;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendMailRegisterNormal implements ShouldQueue
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
    protected $shortLink;
    public function __construct($params, $shortLink)
    {
        $this->params = $params;
        $this->shortLink = $shortLink;
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
                ->send(new RegisterNormal($this->params, $this->shortLink));
        }
    }
}
