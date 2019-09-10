<?php

use Illuminate\Database\Seeder;

use App\Invoice;

class Modify__Change_Invoice_Code_Format extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $invoiceGroups = DB::table('invoices')
            ->select(
                'id', 'code', 'created_at',
                DB::raw("DATE_FORMAT(created_at, '%Y%m') as yearMonth")
            )
            ->orderBy('id', 'asc')
            ->get()
            ->groupBy('yearMonth');

        foreach ($invoiceGroups as $key => $invoices) {
            $this->command->info('***'.$key.'***');
            foreach ($invoices as $i => $invoice) {
                $no = STR_PAD($i + 1, 4, 0, STR_PAD_LEFT);
                $code = 'UK-INV'.date('Ymd', strtotime($invoice->created_at)).'-'.$no;

                $this->command->info('- '.$invoice->code.' to '.$code.' -');

                Invoice::where('id', $invoice->id)->update([
                    'code' => $code
                ]);
            }
        }
    }
}
