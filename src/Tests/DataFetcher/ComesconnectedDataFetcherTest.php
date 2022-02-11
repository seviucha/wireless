<?php

namespace App\Tests\DataFetcher;

use App\ContentProvider\ComesconnectedContentProvider;
use App\DataFetcher\ComesconnectedDataFetcher;
use PHPUnit\Framework\TestCase;

class ComesconnectedDataFetcherTest extends TestCase
{
    /**
     * @dataProvider getTestHtml
     */
    public function testGetData($content, $expected): void
    {
        $contentProvider = $this->createMock(ComesconnectedContentProvider::class);
        $sut = new ComesconnectedDataFetcher($contentProvider);

        $contentProvider
            ->expects($this->once())
            ->method('getContent')
            ->willReturn($content);

        $result = $sut->getData();

        $this->assertEquals([$expected], $result);
    }

    /**
     * @return array[]
     */
    public function getTestHtml(): array
    {
        return [
            [
                '<div class="col-xs-4">
                    <div class="package featured-right" style="margin-top:0px; margin-right:0px; margin-bottom:0px; margin-left:25px">
                        <div class="header dark-bg">
                            <h3>Some title</h3>
                        </div>
                        <div class="package-features">
                            <ul>
                                <li>
                                    <div class="package-name">Some description</div>
                                </li>
                                <li>
                                    <div class="package-price"><span class="price-big">£16.00</span><br>(inc. VAT)<br>Per Month</div>
                                </li>
                                <li>
                                    <div class="package-data">12 Months - Voice &amp; SMS Service Only</div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>',
                [
                    "title" => "Some title",
                    "description" => "Some description",
                    "price" => "£16.00 Per Month",
                    "discount" => "",
                ],
            ],
        ];
    }
}
