<?php

namespace App\Services;

class DisplayEncryption
{
    /**
     * Gera um token assinado (HMAC-SHA256) com expiração de 24 horas.
     * Payload: { type, rid, exp, jti }
     */
    public static function generateToken(string $resourceType, string $resourceId): string
    {
        $payload = base64_encode(json_encode([
            'type' => $resourceType,
            'rid'  => $resourceId,
            'exp'  => time() + 86400, // 24 horas
            'jti'  => bin2hex(random_bytes(8)),
        ]));

        $sig = hash_hmac('sha256', $payload, config('app.key'));

        return $payload . '.' . $sig;
    }

    /**
     * Valida o token: verifica assinatura e expiração.
     * Retorna o payload decodificado ou null se inválido.
     */
    public static function validateToken(string $token): ?array
    {
        $parts = explode('.', $token, 2);

        if (count($parts) !== 2) {
            return null;
        }

        [$encodedPayload, $sig] = $parts;

        $expectedSig = hash_hmac('sha256', $encodedPayload, config('app.key'));

        if (! hash_equals($expectedSig, $sig)) {
            return null;
        }

        $payload = json_decode(base64_decode($encodedPayload), true);

        if (! $payload || ($payload['exp'] ?? 0) < time()) {
            return null;
        }

        return $payload;
    }

    /**
     * Deriva uma chave AES-256 (32 bytes binários) a partir do token.
     * K = HMAC-SHA256(APP_KEY, token + "|aes-key")
     */
    public static function deriveKey(string $token): string
    {
        return hash_hmac('sha256', $token . '|aes-key', config('app.key'), true);
    }

    /**
     * Criptografa $data com AES-256-GCM usando a chave derivada do token.
     * Retorna envelope com iv, tag e data em base64.
     */
    public static function encrypt(array $data, string $token): array
    {
        $key = self::deriveKey($token);
        $iv  = random_bytes(12); // 96-bit IV recomendado para GCM
        $tag = '';

        $ciphertext = openssl_encrypt(
            json_encode($data),
            'aes-256-gcm',
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            16
        );

        return [
            'iv'   => base64_encode($iv),
            'tag'  => base64_encode($tag),
            'data' => base64_encode($ciphertext),
        ];
    }

    /**
     * Retorna a chave derivada em base64, para embedar no atributo data-key da view.
     */
    public static function getPageKey(string $token): string
    {
        return base64_encode(self::deriveKey($token));
    }
}
