<?php

namespace App\DataFetcher;

use App\ContentProvider\ContentProviderInterface;
use DOMElement;
use Symfony\Component\DomCrawler\Crawler;

class ComesconnectedDataFetcher implements DataFetcherInterface
{
    private const OFFER_CSS_CLASS = 'package';
    private const NAME_CSS_CLASS = 'header dark-bg';
    private const DESCRIPTION_CSS_CLASS = 'package-name';
    private const PRICE_CSS_CLASS = 'package-price';
    private const MONTHLY_PAYMENT = 'Per Month';
    private const ANNUAL_PAYMENT = 'Per Year';
    private const CURRENCY_SYMBOL = '£';

    private ContentProviderInterface $contentProvider;

    /**
     * ComesConnectedDataFetcher constructor.
     *
     * @param ContentProviderInterface $contentProvider
     */
    public function __construct(ContentProviderInterface $contentProvider)
    {
        $this->contentProvider = $contentProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function getData(): array
    {
        $content = $this->contentProvider->getContent();
        $crawler = new Crawler($content);
        $offers = $crawler->filterXPath(sprintf('//div[contains(concat(" ", normalize-space(@class), " "), " %s ")]', self::OFFER_CSS_CLASS));
        $result = [];

        /** @var DOMElement $offer */
        foreach ($offers as $offerNr => $offer) {
            /** @var DOMElement $section */
            foreach ($offer->getElementsByTagName('div') as $section) {
                switch ($section->getAttribute('class')) {
                    case self::NAME_CSS_CLASS:
                        $result[$offerNr]['title'] = $section->firstElementChild->nodeValue;

                        break;
                    case self::DESCRIPTION_CSS_CLASS:
                        $result[$offerNr]['description'] = $this->getDescription($section);

                        break;
                    case self::PRICE_CSS_CLASS:
                        $result[$offerNr]['price'] = $this->getFullPriceInfo($section);
                        $result[$offerNr]['discount'] = $section->lastElementChild->nodeValue;
                        $result[$offerNr]['annual_price'] = $this->calculateAnnualPrice($section);
                }
            }
        }

        // sorts results by highest annual price
        usort($result, static function ($offer1, $offer2) {
            return $offer2['annual_price'] <=> $offer1['annual_price'];
        });

        // removes sorting column
        array_walk($result, static function(&$offer) {
            unset($offer['annual_price']);
        });

        return $result;
    }

    /**
     * @param DOMElement $element
     * @return string
     */
    private function getFullPriceInfo(DOMElement $element): string
    {
        return sprintf('%s %s', $element->firstElementChild->nodeValue,
            $this->isPriceMonthly($element->nodeValue) ? self::MONTHLY_PAYMENT : self::ANNUAL_PAYMENT);
    }

    /**
     * @param string $nodeValue
     * @return bool
     */
    private function isPriceMonthly(string $nodeValue): bool
    {
        return str_contains($nodeValue, self::MONTHLY_PAYMENT);
    }

    /**
     * @param DOMElement $element
     * @return float
     */
    private function calculateAnnualPrice(DOMElement $element): float
    {
        $price = $this->getPriceAsNumber($element->firstElementChild->nodeValue);

        return $this->isPriceMonthly($element->nodeValue) ? $price * 12: $price;
    }

    /**
     * @param string $nodeValue
     * @return float
     */
    private function getPriceAsNumber(string $nodeValue): float
    {
        return (float) str_replace(self::CURRENCY_SYMBOL, '', $nodeValue);
    }

    /**
     * @param DOMElement $element
     * @return string
     */
    private function getDescription(DOMElement $element): string
    {
        $description = strip_tags($element->ownerDocument->saveHTML($element), '<br>');

        return str_replace('<br>', PHP_EOL, $description);
    }
}
