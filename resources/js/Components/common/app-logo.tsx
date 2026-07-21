import { Link } from '@inertiajs/react';

export function AppLogo({ compact = false, name = 'Portfolio' }: { compact?: boolean; name?: string }) {
    return (
        <Link href={route('home')} className="group inline-flex items-center gap-3" aria-label="Ke beranda">
            <span className="grid size-8 place-items-center border border-foreground font-mono text-xs font-medium transition-colors group-hover:bg-foreground group-hover:text-background">
                {name.charAt(0).toUpperCase()}
            </span>
            {!compact && <span className="hidden font-heading text-sm font-bold uppercase tracking-[-0.02em] min-[430px]:inline">{name}</span>}
        </Link>
    );
}
