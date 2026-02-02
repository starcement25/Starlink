<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ASMLiftingAction extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(private array $data)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Mason Lifting',
    //     );
    // }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'mails.lifting_action_asm',
    //         with: [
    //                 'liftingInfos' => [
    //                     'Mason Name' => $this->data['mason_name'],
    //                     'Mason Mobile no' => $this->data['mason_mobile'],
    //                     'TE Name' => $this->data['te_name'],
    //                     'TE Code' => $this->data['te_code'],
    //                     'Dealer/RSSD Name' => $this->data['dealer_rssd_name'],
    //                     'Dealer/ RSSD Code' => $this->data['dealer_rssd_code'],
    //                     'Product Name' => $this->data['product_name'],
    //                     'QTY :' => $this->data['qty'],
    //                     'Lifting Date' => $this->data['lifting_date'],
    //                     'Remarks' => $this->data['remarks'],
    //                     'Approve Link' => $this->data['approve_link'],
    //                     'Reject Link' => $this->data['reject_link'],
    //                 ]
    //             ],
    //     );
    // }

    public function build()
    {
        return $this->subject('Mason Lifting Approval Request_StarLink')
                    ->view('mails.lifting_action_asm')->with([
                        'liftingInfos' => [
                            'Mason Name' => $this->data['mason_name'],
                            'Mason Mobile no' => $this->data['mason_mobile'],
                            'BD Name' => $this->data['te_name'],
                            'BD Code' => $this->data['te_code'],
                            'Dealer/RSSD Name' => $this->data['dealer_rssd_name'],
                            'Dealer/ RSSD Code' => $this->data['dealer_rssd_code'],
                            'Product Name' => $this->data['product_name'],
                            'QTY' => $this->data['qty'],
                            'Lifting Date' => $this->data['lifting_date'],
                            'Remarks' => $this->data['remarks'],
                            'Approve Link' => $this->data['approve_link'],
                            'Reject Link' => $this->data['reject_link'],
                        ]
                    ]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
