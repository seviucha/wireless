<?php

namespace App\Tests\ContentProvider;

use App\ContentProvider\ComesconnectedContentProvider;
use PHPUnit\Framework\TestCase;

class ComesconnectedContentProviderTest extends TestCase
{
    private const TEST_SRC = __DIR__ . '/payload.html';

    public function testGetContent(): void
    {
        $sut = new ComesconnectedContentProvider(self::TEST_SRC);
        $content = $sut->getContent();

        $this->assertEquals(
            '<!doctype html>
<html lang="en">
<head>
<body>
<div>test body</div>
</body>
</html>
',
            $content
        );

        $this->assertNotEquals('zxc', $content);
    }
}
