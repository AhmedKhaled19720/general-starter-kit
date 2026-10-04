<?php

namespace App\Support;

class AvatarLibrary
{
    /**
     * DiceBear styles available in the picker.
     *
     * @var array<string, string>
     */
    private const STYLES = [
        'adventurer' => 'Adventurer',
        'avataaars' => 'Cartoon',
        'big-smile' => 'Big Smile',
        'bottts' => 'Robots',
        'fun-emoji' => 'Emoji',
        'lorelei' => 'Illustrated',
        'micah' => 'Micah',
        'notionists' => 'Notion',
        'open-peeps' => 'Open Peeps',
        'pixel-art' => 'Pixel Art',
        'thumbs' => 'Thumbs',
    ];

    /**
     * Shared seeds reused across every style.
     *
     * @var list<string>
     */
    private const SEEDS = [
        'alex', 'bailey', 'casey', 'drew', 'emerson', 'finley',
        'gray', 'harper', 'indigo', 'jordan', 'kai', 'logan',
        'morgan', 'noah', 'oakley', 'parker', 'quinn', 'riley',
        'sage', 'taylor', 'uma', 'val', 'winter', 'zion',
    ];

    /**
     * @return list<string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (array_keys(self::STYLES) as $style) {
            foreach (self::SEEDS as $seed) {
                $options[] = self::compose($style, $seed);
            }
        }

        return $options;
    }

    public static function isValid(?string $avatar): bool
    {
        if ($avatar === null || $avatar === '') {
            return false;
        }

        [$style, $seed] = self::parse($avatar);

        return isset(self::STYLES[$style]) && in_array($seed, self::SEEDS, true);
    }

    public static function url(?string $avatar): ?string
    {
        $avatar = self::normalize($avatar);

        if ($avatar === null) {
            return null;
        }

        [$style, $seed] = self::parse($avatar);

        return sprintf(
            '/avatars/%s/%s.svg',
            rawurlencode($style),
            rawurlencode($seed),
        );
    }

    public static function remoteUrl(string $avatar): string
    {
        [$style, $seed] = self::parse($avatar);

        return sprintf(
            'https://api.dicebear.com/9.x/%s/svg?seed=%s',
            rawurlencode($style),
            rawurlencode($seed),
        );
    }

    /**
     * @return list<array{id: string, label: string, items: list<array{id: string, url: string, seed: string}>}>
     */
    public static function catalog(): array
    {
        $groups = [];

        foreach (self::STYLES as $style => $label) {
            $items = [];

            foreach (self::SEEDS as $seed) {
                $id = self::compose($style, $seed);
                $items[] = [
                    'id' => $id,
                    'seed' => $seed,
                    'url' => self::url($id),
                ];
            }

            $groups[] = [
                'id' => $style,
                'label' => $label,
                'items' => $items,
            ];
        }

        return $groups;
    }

    /**
     * Normalize legacy values like "alex" into "lorelei:alex".
     */
    public static function normalize(?string $avatar): ?string
    {
        if ($avatar === null || $avatar === '') {
            return null;
        }

        if (str_contains($avatar, ':')) {
            return self::isValid($avatar) ? $avatar : null;
        }

        $legacy = self::compose('lorelei', $avatar);

        return self::isValid($legacy) ? $legacy : null;
    }

    /**
     * @return array{0: string, 1: string}
     */
    private static function parse(string $avatar): array
    {
        if (! str_contains($avatar, ':')) {
            return ['lorelei', $avatar];
        }

        [$style, $seed] = explode(':', $avatar, 2);

        return [$style, $seed];
    }

    private static function compose(string $style, string $seed): string
    {
        return "{$style}:{$seed}";
    }
}
