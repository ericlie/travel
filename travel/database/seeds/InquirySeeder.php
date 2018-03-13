<?php

use App\Events\IncomingInquiry;
use App\Helpers\QuotationGenerator;
use App\Inquiry;
use Illuminate\Database\Seeder;

class InquirySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Inquiry::class, 5)->create()->each(function ($inquiry) {
            event(new IncomingInquiry($inquiry));
            // $generator = new QuotationGenerator($inquiry);
            // $generator->generate();
        });
    }
}
