<?php

namespace App\Mail;

use App\Exports\ProductSerialExport;
use App\Models\ProductSerialGroup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ProductSerialMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $productSerialGroup;
    protected $emails;

    /**
     * Create a new message instance.
     *
     * @param ProductSerialGroup $productSerialGroup
     * @param array $emails
     * @return void
     */
    public function __construct(ProductSerialGroup $productSerialGroup, array $emails)
    {
        $this->productSerialGroup = $productSerialGroup;
        $this->emails = $emails;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $filename = 'product-serial-list-' . $this->productSerialGroup->uuid . '.xlsx';
        $path = 'excel/product-serial-group/' . $this->productSerialGroup->uuid . '.xlsx';
        Excel::store(new ProductSerialExport($this->productSerialGroup), $path, 'public');

        return $this
            ->subject('Product serial list')
            ->to($this->emails)
            ->markdown('mail.product-serial')
            ->attachFromStorageDisk('public', $path, $filename)
        ;
    }
}
