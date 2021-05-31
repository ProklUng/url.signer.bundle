<?php

namespace Prokl\UrlSignedBundle\Tests\Cases\UrlSigner;

use PHPUnit\Framework\TestCase;
use Prokl\UrlSignedBundle\UrlSigner\Md5UrlSigner;

/**
 * Class Md5UrlSignerTest
 * @package Prokl\UrlSignedBundle\Tests\Cases\UrlSigner
 */
class Md5UrlSignerTest extends TestCase
{
    private $signer;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->signer = new Md5UrlSigner('secret', 5, 'exp', 'sign');
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
        static::assertSame('md5', Md5UrlSigner::getName());
    }
}