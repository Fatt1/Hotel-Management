<?php

namespace App\Mail;

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

    /**
     * @param array<string, mixed> $bookingData
     */
    public function __construct(
        public string $bookingRef,
        public array $bookingData,
    ) {
        $this->onQueue('emails');
    }

    public function build(): self
    {
        return $this
            ->subject('Xac nhan dat phong #' . $this->bookingRef)
            ->view('emails.booking-success');
    }

    public function failed(Throwable $exception): void
    {
        Log::error('Queued booking success email failed', [
            'booking_ref' => $this->bookingRef,
            'guest_email' => $this->bookingData['guest_email'] ?? null,
            'message' => $exception->getMessage(),
        ]);
    }
}
