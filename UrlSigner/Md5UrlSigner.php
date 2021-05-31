<?php

declare(strict_types=1);

namespace Prokl\UrlSignedBundle\UrlSigner;

use Psr\Http\Message\UriInterface;

/**
 * Class Md5UrlSigner
 * @package Prokl\UrlSignedBundle\UrlSigner
 */
final class Md5UrlSigner extends AbstractUrlSigner
{
    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'md5';
    }

    /**
     * Generate a token to identify the secure action.
     *
     * @param UriInterface|string $url        URL.
     * @param string              $expiration
     *
     * @return string
     */
    protected function createSignature($url, string $expiration): string
    {
        return hash_hmac('md5', "{$url}::{$expiration}", $this->signatureKey);
    }
}
