<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\V1\Invoice;

use App\Utils\InvoiceUtil;

class InvoiceCalculateCanceledAmount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ultraklin-invoice:calculate-canceled-amount {code?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate invoice canceled amount';

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
        if (is_null($this->argument('code'))) {
            $this->all();
        } else {
            $this->single();
        }
    }

    public function all()
    {
        $invoices = Invoice::orderBy('id', 'asc')
            ->get();

        $counter = 0;

        foreach ($invoices as $invoice) {
            InvoiceUtil::calculateCanceledAmount($invoice);
            InvoiceUtil::calculateCanceledDiscount($invoice);

            $this->info($invoice->code.' :: '.$invoice->canceled_amount.' - '.$invoice->canceled_discount);

            $counter += 1;
        }

        $this->info('');
        $this->info('Total Invoice :: '.$counter);
    }

    public function single()
    {
        $invoice = Invoice::where('code', $this->argument('code'))
            ->first();

        InvoiceUtil::calculateCanceledAmount($invoice);
        InvoiceUtil::calculateCanceledDiscount($invoice);

        $this->info($invoice->code.' :: '.$invoice->canceled_amount.' - '.$invoice->canceled_discount);
    }
}
