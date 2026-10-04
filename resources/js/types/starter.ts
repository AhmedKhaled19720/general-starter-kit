export type Preferences = {
    layout: 'navbar' | 'sidebar';
    locale: 'ar' | 'en';
    appearance: 'light' | 'dark' | 'system';
};
export type Palette = {
    primary: string;
    primary_foreground: string;
    background: string;
    foreground: string;
    card: string;
};
export type ProjectTheme = { light: Palette; dark: Palette };
export type NavigationItem = {
    label: string;
    href: string;
    icon: string;
    permission?: string;
};
