<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class BookingSuccessMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 120;

    /**
     * @var array<int>
     */
    public array $backoff = [10, 60, 180];

    public function __construct(
        public Booking $booking,
    ) {
        $this->onQueue('emails');
    }

    public function build(): self
    {
        $bookingRef = 'UL-' . str_pad((string) $this->booking->id, 6, '0', STR_PAD_LEFT);

        return $this
            ->subject('Xac nhan dat phong #' . $bookingRef)
            ->view('emails.booking-success');
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Queued booking success email failed', [
            'booking_id' => $this->booking->id,
            'guest_email' => $this->booking->customer?->email,
            'message' => $exception->getMessage(),
        ]);
    }
}
