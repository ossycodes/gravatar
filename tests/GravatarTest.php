<?php

use Creativeorange\Gravatar\Exceptions\InvalidEmailException;
use Creativeorange\Gravatar\Gravatar;
use PHPUnit\Framework\TestCase;

class GravatarTest extends TestCase
{
    public $gravatar;

    public function setUp(): void
    {
        $this->gravatar  = new Gravatar;
    }

    /** @test **/
    public function throwsinvalidEmailExceptionIfEmailIsInvalid()
    {
        $this->expectException(InvalidEmailException::class);
        $this->expectExceptionMessage('Please specify a valid email address');
        $this->invokeMethod($this->gravatar, 'checkEmail', ['invalidemailaddress']);
    }

    /** @test */
    public function returnValidHashForEmail()
    {
        $hash = $this->invokeMethod($this->gravatar, 'hashEmail', []);
        $this->assertSame('d41d8cd98f00b204e9800998ecf8427e', $hash);
    }

    /** @test */
    public function assertPublicBaseUrlIsCorrect()
    {
        $this->assertSame('https://www.gravatar.com/avatar/', $this->gravatar::PUBLIC_BASEURL);
    }

    /** @test */
    public function assertSecureBaseUrlIsCorrect()
    {
        $this->assertSame('https://secure.gravatar.com/avatar/', $this->gravatar::SECURE_BASEURL);
    }

    function generateLink($email)
    {
        $hashedEmail =  md5(strtolower(trim($email)));
        return "https://www.gravatar.com/avatar/{$hashedEmail}?s=200&r=pg&d=g";
    }

    /** @test */
    public function assertThatCorrectGravatarImageLinkIsReturn()
    {
        $stubGravatarObject = $this->createMock(Gravatar::class);
        $stubGravatarObject->method('get')
            ->with($this->equalTo('chris@gmail.com'))
            ->willReturn('https://www.gravatar.com/avatar/d41d8cd98f00b204e9800998ecf8427e?s=200&r=pg&d=g');

        $this->assertSame('https://www.gravatar.com/avatar/d41d8cd98f00b204e9800998ecf8427e?s=200&r=pg&d=g', $stubGravatarObject->get('chris@gmail.com'));
    }

    /** @test **/
    public function assertThatEmailExistsOnGravatar()
    {
        $stubGravatarObject = $this->createMock(Gravatar::class);

        $stubGravatarObject->method('exists')
            ->with($this->equalTo('chris@gmail.com'))
            ->willReturn(true);

        $this->assertTrue($stubGravatarObject->exists('chris@gmail.com'));
    }

    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}
