<?php

namespace Prokl\UrlSignedBundle\Tests\Cases\UrlSigner;

use PHPUnit\Framework\TestCase;
use Prokl\UrlSignedBundle\UrlSigner\AbstractUrlSigner;
use Psr\Http\Message\UriInterface;

/**
 * Class AbstractUrlSigner
 * @package Prokl\UrlSignedBundle\Tests\Cases\UrlSigner
 */
class AbstractUrlSignerTest extends TestCase
{
    private $signer;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->signer = new class('secret', 5, 'exp', 'sign') extends AbstractUrlSigner {
            public static function getName(): string
            {
                return 'abstract';
            }

            /**
             * @param UriInterface|string $url
             */
            protected function createSignature($url, string $expiration): string
            {
                $url = (string) $url;

                return "{$url}::{$expiration}::{$this->signatureKey}";
            }

            protected function getExpirationTimestamp($expiration): string
            {
                return $expiration instanceof \DateTime ? 'datetime' : (string) $expiration;
            }
        };
    }

    public function testSignDefaultExpiration(): void
    {
        $signedUrl = $this->signer->sign('http://test.org/valid-signature');

        static::assertSame('http://test.org/valid-signature?exp=5&sign=http://test.org/valid-signature::5::secret', $signedUrl);
    }

    public function testSignWithExpiration(): void
    {
        $signedUrl = $this->signer->sign('http://test.org/valid-signature', 7);

        static::assertSame('http://test.org/valid-signature?exp=7&sign=http://test.org/valid-signature::7::secret', $signedUrl);
    }
}