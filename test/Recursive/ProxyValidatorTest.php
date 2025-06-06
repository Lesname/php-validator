<?php
declare(strict_types=1);

namespace LesValidatorTest\Recursive;

use RuntimeException;
use LesValidator\Validator;
use LesValidator\Recursive\ProxyValidator;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use LesValidator\ValidateResult\ValidateResult;

#[CoversClass(ProxyValidator::class)]
class ProxyValidatorTest extends TestCase
{
    public function testProxyCall(): void
    {
        $input = [];

        $validateResult = $this->createMock(ValidateResult::class);

        $validator = new ProxyValidator();

        $proxy = $this->createMock(Validator::class);
        $proxy
            ->expects($this->once())
            ->method('validate')
            ->with($input)
            ->willReturn($validateResult);

        self::assertSame($validateResult, $validator->setProxy($proxy)->validate($input));
    }

    public function testNoProxySetThrows(): void
    {
        $this->expectException(RuntimeException::class);

        $input = [];

        $validator = new ProxyValidator();

        $validator->validate($input);
    }
}
