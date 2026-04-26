<?php

namespace App\Enums;

enum Season: string
{
    case Spring = 'spring';
    case Summer = 'summer';
    case Autumn = 'autumn';
    case Winter = 'winter';

    /**
     * Determine the current season based on the given date.
     * Uses Northern Hemisphere seasons.
     */
    public static function fromDate(\DateTime|string $date): self
    {
        if (is_string($date)) {
            $date = new \DateTime($date);
        }

        $month = (int) $date->format('m');

        return match ($month) {
            12, 1, 2 => self::Winter,
            3, 4, 5 => self::Spring,
            6, 7, 8 => self::Summer,
            9, 10, 11 => self::Autumn,
        };
    }

    /**
     * Get the current season.
     */
    public static function current(): self
    {
        return self::fromDate(now());
    }

    /**
     * Get the Unsplash collection ID for this season.
     */
    public function collectionId(): ?string
    {
        return config("services.unsplash.collections.{$this->value}");
    }
}
