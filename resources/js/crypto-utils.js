// Utilitários de criptografia AES-256-GCM usando @noble/ciphers (pure JS, funciona em HTTP e HTTPS).
import { gcm } from '@noble/ciphers/aes.js';

function b64ToBytes(b64) {
    return Uint8Array.from(atob(b64), function (c) { return c.charCodeAt(0); });
}

/**
 * Importa (decodifica) a chave AES-256 a partir de uma string base64.
 * Retorna Promise<Uint8Array> para manter a mesma interface async.
 * @param {string} b64Key
 * @returns {Promise<Uint8Array>}
 */
export function importKey(b64Key) {
    try {
        return Promise.resolve(b64ToBytes(b64Key));
    } catch (err) {
        return Promise.reject(err);
    }
}

/**
 * Descriptografa um envelope retornado pela API (AES-256-GCM).
 * @param {{ iv: string, tag: string, data: string }} envelope
 * @param {Uint8Array} keyBytes - Chave bruta de 32 bytes
 * @returns {Promise<object>}
 */
export function decryptResponse(envelope, keyBytes) {
    try {
        var iv   = b64ToBytes(envelope.iv);
        var tag  = b64ToBytes(envelope.tag);
        var data = b64ToBytes(envelope.data);

        // @noble/ciphers espera ciphertext + tag concatenados
        var combined = new Uint8Array(data.length + tag.length);
        combined.set(data);
        combined.set(tag, data.length);

        var plain = gcm(keyBytes, iv).decrypt(combined);
        return Promise.resolve(JSON.parse(new TextDecoder().decode(plain)));
    } catch (err) {
        return Promise.reject(err);
    }
}