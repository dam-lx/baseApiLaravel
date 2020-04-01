<?php

namespace App\Core\Jobs;

use App\Core\Dao\SDB;
use App\Core\Helpers\CommonHelper;
use App\Core\Helpers\ShoppingHelper;
use App\Core\Mail\Contact;
use App\Core\Mail\Order;
use App\Core\Mail\RegisterNormal;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendMailOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tries = 2;
    public $timeout = 600;

    protected $order;
    protected $shippings;
    public function __construct($order, $shippings)
    {
        $this->order = $order;
        $this->shippings = $shippings;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        SDB::beginTransaction();
        try {
            if (!empty($this->order[0]->email)) {
                Mail::to($this->order[0]->email)
                    ->bcc(env('EMAIL_ADMIN', 'thanhtrucle.1993@gmail.com'))
                    ->send(new Order($this->order, $this->shippings));
            }
            SDB::commit();
        }
        catch (\Exception $e){
            CommonHelper::CommonLog($e->getMessage());
            SDB::rollBack();
        }
    }
}
