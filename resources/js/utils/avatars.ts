export type AvatarGroup = {
    id: string;
    label: string;
    items: Array<{ id: string; url: string; seed: string }>;
};

const AVATAR_STYLES = [
    'adventurer',
    'avataaars',
    'big-smile',
    'bottts',
    'fun-emoji',
    'lorelei',
    'micah',
    'notionists',
    'open-peeps',
    'pixel-art',
    'thumbs',
];

const AVATAR_STYLE_LABELS: Record<string, string> = {
    adventurer: 'Adventurer',
    avataaars: 'Cartoon',
    'big-smile': 'Big Smile',
    bottts: 'Robots',
    'fun-emoji': 'Emoji',
    lorelei: 'Illustrated',
    micah: 'Micah',
    notionists: 'Notion',
    'open-peeps': 'Open Peeps',
    'pixel-art': 'Pixel Art',
    thumbs: 'Thumbs',
};

const AVATAR_SEEDS = [
    'alex',
    'bailey',
    'casey',
    'drew',
    'emerson',
    'finley',
    'gray',
    'harper',
    'indigo',
    'jordan',
    'kai',
    'logan',
    'morgan',
    'noah',
    'oakley',
    'parker',
    'quinn',
    'riley',
    'sage',
    'taylor',
    'uma',
    'val',
    'winter',
    'zion',
];

function parseAvatar(avatar: string | null | undefined) {
    if (!avatar) {
        return null;
    }

    if (String(avatar).includes(':')) {
        const [style, seed] = String(avatar).split(':', 2);
        return { style, seed };
    }

    return { style: 'lorelei', seed: String(avatar) };
}

export function avatarUrl(avatar: string | null | undefined): string | null {
    const parsed = parseAvatar(avatar);

    if (!parsed) {
        return null;
    }

    if (
        !AVATAR_STYLES.includes(parsed.style) ||
        !AVATAR_SEEDS.includes(parsed.seed)
    ) {
        return null;
    }

    return `/avatars/${encodeURIComponent(parsed.style)}/${encodeURIComponent(parsed.seed)}.svg`;
}

export function avatarCatalog(): AvatarGroup[] {
    return AVATAR_STYLES.map((style) => ({
        id: style,
        label: AVATAR_STYLE_LABELS[style] ?? style,
        items: AVATAR_SEEDS.map((seed) => {
            const id = `${style}:${seed}`;
            return {
                id,
                seed,
                url: avatarUrl(id) ?? '',
            };
        }),
    }));
}
