<?php
namespace Tests\W5n;

use PHPUnit\Framework\TestCase as FrameworkTestCase;
use W5n\Scanner;
use W5n\ScannerException;

class ScannerTest extends FrameworkTestCase
{
    public function testConsume()
    {
        $scanner = $this->createScanner('foobar');
        $this->assertEquals('f', $scanner->consume());
        $this->assertEquals('o', $scanner->consume());
        $this->assertEquals('o', $scanner->consume());
        $this->assertEquals('b', $scanner->consume());
        $this->assertEquals('a', $scanner->consume());
        $this->assertEquals('r', $scanner->consume());

        $this->assertNull($scanner->consume());
        $this->assertNull($scanner->consume());
    }

    public function testConsumed()
    {
        $scanner = $this->createScanner('foo');
        $this->assertFalse($scanner->consumed());
        $this->assertEquals('f', $scanner->consume());
        $this->assertFalse($scanner->consumed());
        $this->assertEquals('o', $scanner->consume());
        $this->assertFalse($scanner->consumed());
        $this->assertEquals('o', $scanner->consume());
        $this->assertTrue($scanner->consumed());
    }

    public function testPeek()
    {
        $string  = 'peek';
        $scanner = $this->createScanner($string);

        for ($i = 0; $i < strlen($string); $i++) {
            for ($j = 0; $j < 4; $j++) {
                $this->assertEquals($string[$i], $scanner->peek());
            }
            $this->assertEquals($string[$i], $scanner->consume());
        }
        $this->assertTrue($scanner->consumed());
    }

    public function testConsumeWithExpectedOutput()
    {
        $scanner = $this->createScanner('expected');

        $this->assertEquals(
            'expected',
            $scanner->consume('expected')
        );
    }

    public function testConsumeShouldThrowIfStringIsNotAsExpected()
    {
        $this->expectException(ScannerException::class);

        $scanner = $this->createScanner('expected');

        $scanner->consume('not expected');
    }

    public function testMatches()
    {
        $scanner = $this->createScanner('matches');

        $this->assertTrue($scanner->matches('matches'));
        $this->assertFalse($scanner->matches('foo'));
    }

    public function testConsumeWhitespaces()
    {
        //5 spaces
        $scanner = $this->createScanner('     spaces');

        $scanner->consumeWhitespaces();
        $this->assertEquals(5, $scanner->getCursorPosition());
    }

    public function testConsumeWhile()
    {
        $scanner = $this->createScanner('123456while');

        $this->assertEquals(
            '123456',
            $scanner->consumeWhile('#[0-9]#')
        );

        $this->assertEquals(
            'while',
            $scanner->consume('while')
        );
    }

    public function testConsumeUnless()
    {
        $scanner = $this->createScanner('123456while');

        $this->assertEquals(
            '123456',
            $scanner->consumeUnless('#[a-z]#')
        );

        $this->assertEquals(
            'while',
            $scanner->consume('while')
        );
    }

    public function testGetPosition()
    {
        $scanner = $this->createScanner('0123456789');

        for ($i = 0; $i <= 9; ++$i) {
            $this->assertEquals($i, $scanner->getCursorPosition());
            $scanner->consume();
        }
    }

    public function testConsumeWhileCallback()
    {
        $scanner = $this->createScanner('0123456789');

        $result = $scanner->consumeWhileCallback(function ($char) {
            return intval($char) < 5;
        });

        $this->assertEquals('01234', $result);
    }

    public function testConsumeUnlessCallback()
    {
        $scanner = $this->createScanner('0123456789');

        $result = $scanner->consumeUnlessCallback(function ($char) {
            return intval($char) >= 5;
        });

        $this->assertEquals('01234', $result);
    }

    public function testGetLastPosition()
    {
        $scanner = $this->createScanner('0123456789');

        $this->assertEquals(10, $scanner->getLastPosition());
    }


    private function createScanner($text): Scanner
    {
        return new Scanner($text);
    }
}
