import type { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';

type SeoHeadProps = {
    title: string;
    description?: string | null;
    image?: string | null;
    type?: 'website' | 'article';
    noIndex?: boolean;
    structuredData?: Record<string, unknown>;
};

function absoluteUrl(value?: string | null): string | undefined {
    if (!value) return undefined;
    if (/^https?:\/\//i.test(value)) return value;

    return `${window.location.origin}${value.startsWith('/') ? '' : '/storage/'}${value}`;
}

export function SeoHead({ title, description, image, type = 'website', noIndex = false, structuredData }: SeoHeadProps) {
    const { site } = usePage<PageProps>().props;
    const canonical = `${window.location.origin}${window.location.pathname}`;
    const resolvedDescription = description || site.settings.default_meta_description || site.settings.site_description || '';
    const resolvedImage = absoluteUrl(image || site.settings.default_og_image);
    const fullTitle = title === site.settings.default_meta_title ? title : `${title} - ${site.settings.site_name || 'Portfolio'}`;

    return <Head title={title}>
        <meta head-key="description" name="description" content={resolvedDescription} />
        <link head-key="canonical" rel="canonical" href={canonical} />
        <meta head-key="robots" name="robots" content={noIndex ? 'noindex, nofollow' : 'index, follow'} />
        <meta head-key="og:type" property="og:type" content={type} />
        <meta head-key="og:title" property="og:title" content={fullTitle} />
        <meta head-key="og:description" property="og:description" content={resolvedDescription} />
        <meta head-key="og:url" property="og:url" content={canonical} />
        <meta head-key="og:site_name" property="og:site_name" content={site.settings.site_name || 'Portfolio'} />
        {resolvedImage && <meta head-key="og:image" property="og:image" content={resolvedImage} />}
        <meta head-key="twitter:card" name="twitter:card" content={resolvedImage ? 'summary_large_image' : 'summary'} />
        <meta head-key="twitter:title" name="twitter:title" content={fullTitle} />
        <meta head-key="twitter:description" name="twitter:description" content={resolvedDescription} />
        {resolvedImage && <meta head-key="twitter:image" name="twitter:image" content={resolvedImage} />}
        {structuredData && <script head-key="structured-data" type="application/ld+json">{JSON.stringify(structuredData)}</script>}
    </Head>;
}
