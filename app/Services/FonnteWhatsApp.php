<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteWhatsApp
{
    public function send(string $target, string $message): bool
    {
        if (! (bool) config('services.fonnte.enabled')) {
            return false;
        }

        $token = (string) config('services.fonnte.device_token');
        $target = $this->normalizeTarget($target);

        if ($token === '' || $target === '' || trim($message) === '') {
            return false;
        }

        try {
            $response = Http::asForm()
                ->withHeaders(['Authorization' => $token])
                ->withOptions(['verify' => (bool) config('services.fonnte.verify_ssl', true)])
                ->timeout(15)
                ->post((string) config('services.fonnte.send_url'), [
                    'target' => $target,
                    'message' => $message,
                    'countryCode' => (string) config('services.fonnte.country_code', '62'),
                ]);

            if (! $response->successful() || $response->json('status') === false) {
                Log::warning('Fonnte WhatsApp notification failed.', [
                    'target' => $target,
                    'status' => $response->status(),
                    'response' => $response->json() ?? $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $exception) {
            Log::warning('Fonnte WhatsApp notification exception.', [
                'target' => $target,
                'message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    protected function normalizeTarget(string $target): string
    {
        $number = preg_replace('/\D+/', '', $target) ?? '';

        if ($number === '') {
            return '';
        }

        if (str_starts_with($number, '0')) {
            return (string) config('services.fonnte.country_code', '62') . substr($number, 1);
        }

        if (str_starts_with($number, '8')) {
            return (string) config('services.fonnte.country_code', '62') . $number;
        }

        return $number;
    }
}
