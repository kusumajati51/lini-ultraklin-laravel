<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;

use App\Invoice;
use App\Order;

class SendReport extends Command
{
    use \App\Traits\Telegram;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send report to telegram';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $today = Carbon::today();
        $start = Carbon::today()->subDay(1)->setTimeFromTimeString(config('ultraklin.time.cycle_time')[0]);
        $end = Carbon::today()->setTimeFromTimeString(config('ultraklin.time.cycle_time')[1]);

        $invoices = Invoice::whereHas('user', function ($user) {
            return $user->where('status', 'user');
        })
        ->whereBetween('created_at', [ $start, $end ])
        ->get();

        $orders = Order::whereHas('invoice', function ($invoice) {
            return $invoice->whereHas('user', function ($user) {
                return $user->where('status', 'user');
            });
        })
        ->whereBetween('created_at', [ $start, $end ])
        ->get();

        $detail = [
            'paid' => $invoices->sum(function ($invoice) {
                return strtolower($invoice->status) == 'paid';
            }),
            'unpaid' => $invoices->sum(function ($invoice) {
                return strtolower($invoice->status) == 'unpaid';
            }),
            'pending' => $orders->sum(function ($order) {
                return strtolower($order->status) == 'pending';
            }),
            'cancel' => $orders->sum(function ($order) {
                return strtolower($order->status) == 'cancel';
            }),
            'confirm' => $orders->sum(function ($order) {
                return strtolower($order->status) == 'confirm';
            }),
            'done' => $orders->sum(function ($order) {
                return strtolower($order->status) == 'done';
            }),
            'user_order' => $invoices->groupBy('user_id')->count()
        ];

        $this->info('Report '.$today->toFormattedDateString());
        $this->info('');
        $this->info('TInvoices :: '.$invoices->count());
        $this->info('Paid :: '.$detail['paid']);
        $this->info('Unpaid :: '.$detail['unpaid']);
        $this->info('');
        $this->info('Orders :: '.$orders->count());
        $this->info('Pending :: '.$detail['pending']);
        $this->info('Cancel :: '.$detail['cancel']);
        $this->info('Confirm :: '.$detail['confirm']);
        $this->info('Done :: '.$detail['done']);
        $this->info('');
        $this->info('User Order :: '.$detail['user_order']);

        $message = 
            '*Report '.$today->toFormattedDateString().'*'
            ."\n"
            ."\n*Invoices : *".$invoices->count()
            ."\n*Paid : *".$detail['paid']
            ."\n*Unpaid : *".$detail['unpaid']
            ."\n"
            ."\n*Orders : *".$orders->count()
            ."\n*Pending : *".$detail['pending']
            ."\n*Cancel : *".$detail['cancel']
            ."\n*Confirm : *".$detail['confirm']
            ."\n*Done : *".$detail['done']
            ."\n"
            ."\n*User Order : *".$detail['user_order'];

        $this->sendMessage($message);
    }
}
