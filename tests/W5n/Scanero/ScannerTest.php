<?php

namespace Tests\W5n\Scanero;

use PHPUnit\Framework\TestCase as FrameworkTestCase;
use W5n\Scanero\Scanner;
use W5n\Scanero\ScannerException;

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

        $this->expectException(ScannerException::class);
        $this->assertNull($scanner->consume());
    }

    public function testConsumeAny()
    {
        $scanner = $this->createScanner('consumes');

        $scanner->consumeAny('foobar', 'con');
        $this->assertTrue($scanner->matches('sumes'));
    }

    public function testConsumeAnyShouldThrow()
    {
        $scanner = $this->createScanner('consumes');

        $this->expectException(ScannerException::class);
        $scanner->consumeAny('foo', 'bar');
    }


    public function testClearSavedPositions()
    {
        $scanner = $this->createScanner('consumes');
        $scanner->savePosition();
        $scanner->clearSavedPositions();

        $this->expectException(ScannerException::class);

        $scanner->popSavedPosition();
    }

    public function testEmptySavedPositionShouldThrowWhenPop()
    {
        $scanner = $this->createScanner('consumes');
        $this->expectException(ScannerException::class);

        $scanner->popSavedPosition();
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

    public function testMatchesAny()
    {
        $scanner = $this->createScanner('matches');

        $this->assertTrue($scanner->matchesAny('other', 'matches'));
        $this->assertFalse($scanner->matchesAny('one', 'another'));
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
        $this->assertNull($scanner->consumeWhile('#[0-9]#'));

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

        $this->assertNull($scanner->consumeUnless('#[a-z]#'));

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

    public function testConsumeWhileCallbackShouldReturnNullWhenPredicateFailsOnFirstTry()
    {
        $scanner = $this->createScanner('0123456789');

        $scanner->consumeWhileCallback(function ($char) {
            return intval($char) < -1;
        });

        $this->assertNull(null);
    }

    public function testConsumeUnlessCallback()
    {
        $scanner = $this->createScanner('0123456789');

        $result = $scanner->consumeUnlessCallback(function ($char) {
            return intval($char) >= 5;
        });

        $this->assertEquals('01234', $result);
    }

    public function testConsumeUnlessCallbackShouldReturnNullWhenPredicateFailsOnFirstTry()
    {
        $scanner = $this->createScanner('0123456789');

        $scanner->consumeUnlessCallback(function ($char) {
            return intval($char) == 0;
        });

        $this->assertNull(null);
    }


    public function testGetLastPosition()
    {
        $scanner = $this->createScanner('0123456789');

        $this->assertEquals(10, $scanner->getLastPosition());
    }

    public function testSaveAndLoad()
    {
        $scanner = $this->createScanner('0123456789');
        $scanner->savePosition();
        $scanner->consume('0123456');

        $this->assertTrue($scanner->matches('78'));
        $scanner->loadPosition();
        $this->assertTrue($scanner->matches('0123456'));
    }


    private function createScanner($text): Scanner
    {
        return new Scanner($text);
    }
}
