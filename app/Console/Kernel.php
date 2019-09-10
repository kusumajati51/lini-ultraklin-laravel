<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\V1\Invoice;
use App\Traits\V1\PaymentTrait;

class Kernel extends ConsoleKernel
{
    use PaymentTrait;
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\SendReport',
        'App\Console\Commands\InvoiceCalculateCanceledAmount',
        'App\Console\Commands\CreateRegionCommand'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        $schedule->call(function() {
            $invoices = Invoice::with('payments')->get();
            foreach ($invoices as $invoice) {
                if (!is_null($invoice->payments) && $invoice->payments->status_payment == 1) {
                    if ($invoice->status == 'Paid') {
                        foreach ($invoice->orders as $order) {
                            if ($order->status == 'Cancel') {
                                $order->status = 'Done';
                                // $order->save();
                            }
                        }
                        continue;
                    }

                    $status = $this->getPaymentStatus($invoice->payments);
                    $invoice->payments->setAttribute('detail_status', $status);
                    $detail_status = collect($invoice->payments->detail_status->original);
                    if ($detail_status->has('payment_status_code')) {
                        $payment_status_code = json_decode($detail_status)->payment_status_code;
                        if ($payment_status_code == 7) {
                            foreach ($invoice->orders as $order) {
                                $order->status = 'Cancel';
                                $order->save();
                            }
                        }
                    }
                }
            }
        })
        ->everyMinute()
        ->name('canceling-expired-payment')
        ->withoutOverlapping();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
