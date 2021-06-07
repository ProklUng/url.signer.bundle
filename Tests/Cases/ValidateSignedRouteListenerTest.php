<?php

namespace Prokl\UrlSignedBundle\Tests\Cases;

use Prokl\TestingTools\Base\BaseTestCase;
use Prokl\UrlSignedBundle\EventListener\ValidateSignedRouteListener;
use Prokl\UrlSignedBundle\UrlSigner\UrlSignerInterface;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class ValidateSignedRouteListenerTest
 * @package Prokl\UrlSignedBundle\Tests\Cases
 */
class ValidateSignedRouteListenerTest extends BaseTestCase
{
    use ProphecyTrait;

    /** @var UrlSignerInterface */
    private $signerProphecy;
    private $dispatcher;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->signerProphecy = $this->prophesize(UrlSignerInterface::class);
        $subscriber = new ValidateSignedRouteListener($this->signerProphecy->reveal());

        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addListener(RequestEvent::class, [$subscriber, 'validateSignedRoute']);
    }

    /**
     * @return void
     */
    public function testValidateSignedRoute(): void
    {
        $request = Request::create('http://test.org/valid-signature');
        $request->attributes->set('_route_params', ['_signed' => true]);
        $event = new RequestEvent($this->prophesize(HttpKernelInterface::class)->reveal(), $request, null);

        $this->signerProphecy->validate('/valid-signature')->willReturn(true);

        $this->dispatcher->dispatch($event);

        $this->signerProphecy->validate(Argument::any())->shouldHaveBeenCalledOnce();
    }

    /**
     * @return void
     */
    public function testValidateSignedRouteMissingRouteParamsAttribute(): void
    {
        $request = Request::create('http://test.org/valid-signature');
        $event = new RequestEvent($this->prophesize(HttpKernelInterface::class)->reveal(), $request, null);

        $this->dispatcher->dispatch($event);

        $this->signerProphecy->validate(Argument::any())->shouldNotHaveBeenCalled();
    }

    /**
     * @return void
     */
    public function testValidateSignedRouteMissingSignedRouteParam(): void
    {
        $request = Request::create('http://test.org/valid-signature');
        $request->attributes->set('_route_params', ['_locale' => 'fr']);
        $event = new RequestEvent($this->prophesize(HttpKernelInterface::class)->reveal(), $request, null);

        $this->dispatcher->dispatch($event);

        $this->signerProphecy->validate(Argument::any())->shouldNotHaveBeenCalled();
    }

    /**
     * @return void
     */
    public function testValidateSignedRouteFalseSignedRouteParam(): void
    {
        $request = Request::create('http://test.org/valid-signature');
        $request->attributes->set('_route_params', ['_signed' => false]);
        $event = new RequestEvent($this->prophesize(HttpKernelInterface::class)->reveal(), $request, null);

        $this->dispatcher->dispatch($event);

        $this->signerProphecy->validate(Argument::any())->shouldNotHaveBeenCalled();
    }

    /**
     * @return void
     */
    public function testValidateSignedRouteInvalidSignature(): void
    {
        $this->expectException(AccessDeniedHttpException::class);
        $this->expectExceptionMessage('URL is either missing a valid signature or have a bad signature.');

        $request = Request::create('http://test.org/invalid-signature');
        $request->attributes->set('_route_params', ['_signed' => true]);
        $event = new RequestEvent($this->prophesize(HttpKernelInterface::class)->reveal(), $request, HttpKernelInterface::MASTER_REQUEST);
        $this->signerProphecy->validate('/invalid-signature')->willReturn(false);

        $this->dispatcher->dispatch($event);

        $this->signerProphecy->validate(Argument::any())->shouldHaveBeenCalledOnce();
    }
}