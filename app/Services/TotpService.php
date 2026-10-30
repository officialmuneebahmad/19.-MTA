<?php
/**
 * Multan Tech Accessories — Pure PHP RFC 6238 TOTP Two-Factor Authentication
 * Zero external dependencies | 100% standard compliant
 */

declare(strict_types=1);

namespace App\Services;

class TotpService
{
    private const BASE32_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a new random 16-character Base32 secret key.
     */
    public static function generateSecret(int $length = 16): string
    {
        $secret = '';
        $max = strlen(self::BASE32_CHARS) - 1;
        for ($i = 0; $i < $length; $i++) {
            $secret .= self::BASE32_CHARS[random_int(0, $max)];
        }
        return $secret;
    }

    /**
     * Generate 10 random 8-character alphanumeric backup codes.
     * Returns: ['plain' => string[], 'hashed' => string[]]
     */
    public static function generateBackupCodes(int $count = 10): array
    {
        $plain = [];
        $hashed = [];
        for ($i = 0; $i < $count; $i++) {
            $code = strtoupper(bin2hex(random_bytes(4))); // 8-char hex code
            $plain[] = substr($code, 0, 4) . '-' . substr($code, 4, 4);
            $hashed[] = hash('sha256', str_replace('-', '', $code));
        }
        return [
            'plain' => $plain,
            'hashed' => $hashed,
        ];
    }

    /**
     * Verify a 6-digit TOTP code against a Base32 secret.
     * Allows a +-1 time step window (90s tolerance) to compensate for clock drift.
     */
    public static function verifyCode(string $secret, string $code, int $discrepancy = 1): bool
    {
        $cleanCode = preg_replace('/\D/', '', $code);
        if (strlen($cleanCode) !== 6) {
            return false;
        }

        $timeSlice = (int)floor(time() / 30);

        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculated = self::calculateCode($secret, $timeSlice + $i);
            if (hash_equals($calculated, $cleanCode)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculate 6-digit TOTP code for a specific time slice.
     */
    public static function calculateCode(string $secret, ?int $timeSlice = null): string
    {
        if ($timeSlice === null) {
            $timeSlice = (int)floor(time() / 30);
        }

        $secretKey = self::base32Decode($secret);

        // Pack time slice into 8-byte big-endian binary string
        $timeBytes = pack('N*', 0) . pack('N*', $timeSlice);

        // HMAC-SHA1 hash
        $hmac = hash_hmac('sha1', $timeBytes, $secretKey, true);

        // Dynamic truncation (RFC 4226)
        $offset = ord(substr($hmac, -1)) & 0x0F;
        $hashPart = substr($hmac, $offset, 4);
        $value = unpack('N', $hashPart)[1] & 0x7FFFFFFF;

        $modulo = $value % 1000000;
        return str_pad((string)$modulo, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate otpauth:// URI for authenticator apps (Google Authenticator, Microsoft, 1Password, Authy).
     */
    public static function getOtpAuthUri(string $email, string $secret, string $issuer = 'Multan Tech Accessories'): string
    {
        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=6&period=30',
            rawurlencode($issuer),
            rawurlencode($email),
            rawurlencode($secret),
            rawurlencode($issuer)
        );
    }

    /**
     * Base32 decode helper
     */
    private static function base32Decode(string $b32): string
    {
        $b32 = strtoupper($b32);
        $b32 = preg_replace('/[^A-Z2-7]/', '', $b32);

        $buffer = 0;
        $bitsLeft = 0;
        $output = '';

        for ($i = 0; $i < strlen($b32); $i++) {
            $val = strpos(self::BASE32_CHARS, $b32[$i]);
            if ($val === false) continue;

            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $output;
    }
}

