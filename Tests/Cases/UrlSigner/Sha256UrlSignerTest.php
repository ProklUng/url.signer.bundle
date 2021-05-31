<?php

namespace Prokl\UrlSignedBundle\Tests\Cases\UrlSigner;

use PHPUnit\Framework\TestCase;
use Prokl\UrlSignedBundle\UrlSigner\Sha256UrlSigner;

/**
 * Class Sha256UrlSignerTest
 * @package Prokl\UrlSignedBundle\Tests\Cases\UrlSigner
 */
class Sha256UrlSignerTest extends TestCase
{
    private $signer;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->signer = new Sha256UrlSigner('secret', 5, 'exp', 'sign');
    }

    public function testCreateSignature(): void
    {
        $url = 'http://test.org/valid-signature';
        $signedUrl = $this->signer->sign($url);

        static::assertFalse($this->signer->validate($url));
        static::assertTrue($this->signer->validate($signedUrl));
    }

    public function testGetName(): void
    {
        static::assertSame('sha256', Sha256UrlSigner::getName());
    }
}