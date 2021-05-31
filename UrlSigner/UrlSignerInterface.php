<?php

declare(strict_types=1);

namespace Prokl\UrlSignedBundle\UrlSigner;

use Spatie\UrlSigner\UrlSigner;

/**
 * Interface UrlSignerInterface
 * @package Prokl\UrlSignedBundle\UrlSigner
 */
interface UrlSignerInterface extends UrlSigner
{
    /**
     * @inheritDoc
     */
    public function sign($url, $expiration = null) : string;

    /**
     * @return string
     */
    public static function getName(): string;
}
