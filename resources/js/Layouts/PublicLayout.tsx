import { AppIcon } from '@/Components/common/app-icon';
import { AppLogo } from '@/Components/common/app-logo';
import { ThemeSwitcher } from '@/Components/common/theme-switcher';
import { ChatbotLauncher } from '@/Components/chatbot/ChatbotLauncher';
import { SeoHead } from '@/Components/public/SeoHead';
import type { PageProps } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import { AnimatePresence, motion, useReducedMotion } from 'motion/react';
import { lazy, Suspense, useRef, useState, type PropsWithChildren } from 'react';

const GsapAnimations = lazy(() => import('@/animations/use-public-animations').then((module) => ({ default: module.PublicGsapAnimations })));

const navigation = [
    ['Beranda', 'home'], ['Tentang', 'about'], ['Proyek', 'projects.index'],
    ['Pengalaman', 'experience'], ['Sertifikat', 'certificates'], ['Kontak', 'contact.create'],
] as const;

export default function PublicLayout({ children }: PropsWithChildren) {
    const page = usePage<PageProps>();
    const { site, flash } = page.props;
    const [menuOpen, setMenuOpen] = useState(false);
    const contentRef = useRef<HTMLElement>(null);
    const reduceMotion = useReducedMotion();

    const siteName = site.settings.site_name ?? site.profile?.name ?? 'Portfolio';
    const footerText = site.settings.footer_text ?? '';
    const project = (page.props as Record<string, unknown>).project as { title?: string; short_description?: string; seo_title?: string; seo_description?: string; og_image?: string; thumbnail?: string } | undefined;
    const pageTitles: Record<string, string> = { '/about': 'Tentang', '/projects': 'Proyek', '/experience': 'Pengalaman', '/certificates': 'Sertifikat', '/contact': 'Kontak', '/cv': 'CV' };
    const seoTitle = project?.seo_title ?? project?.title ?? pageTitles[page.url.split('?')[0]] ?? site.settings.default_meta_title ?? siteName;
    const seoDescription = project?.seo_description ?? project?.short_description ?? site.settings.default_meta_description;

    return <div className="min-h-screen overflow-x-hidden bg-background text-foreground">
        <SeoHead title={seoTitle} description={seoDescription} image={project?.og_image ?? project?.thumbnail} type={project ? 'article' : 'website'} noIndex={page.component === 'Public/Error'} />
        <a href="#main-content" className="sr-only z-[100] rounded-md bg-primary px-4 py-3 text-primary-foreground focus:not-sr-only focus:fixed focus:left-4 focus:top-4">Lewati ke konten utama</a>
        <header className="sticky top-0 z-40 border-b bg-background/95 backdrop-blur-lg">
            <div className="mx-auto flex min-h-16 max-w-[1440px] items-center justify-between border-x px-4 sm:px-6 lg:px-8">
                <AppLogo name={siteName} />
                <nav className="hidden h-16 items-stretch lg:flex" aria-label="Navigasi utama">
                    {navigation.map(([label, routeName], index) => <Link key={routeName} href={route(routeName)} className={`relative flex items-center border-l px-4 font-mono text-[10px] uppercase tracking-[.12em] transition-colors last:border-r ${route().current(routeName) ? 'bg-foreground text-background' : 'text-muted hover:bg-surface hover:text-foreground'}`}><span className="mr-2 text-[8px] opacity-60">0{index + 1}</span>{label}</Link>)}
                </nav>
                <div className="flex items-center gap-2">
                    <Link href={route('cv.show')} className="hidden border px-3 py-2 font-mono text-[10px] uppercase tracking-wider transition-colors hover:bg-foreground hover:text-background sm:block">CV / PDF</Link>
                    <ThemeSwitcher />
                    <button type="button" onClick={() => setMenuOpen(!menuOpen)} className="grid size-10 place-items-center rounded-sm lg:hidden" aria-label="Buka menu" aria-expanded={menuOpen} aria-controls="mobile-navigation"><AppIcon name={menuOpen ? 'x' : 'menu'} className="size-5" /></button>
                </div>
            </div>
            <AnimatePresence initial={false}>
                {menuOpen && <motion.nav id="mobile-navigation" initial={reduceMotion ? false : { clipPath: 'inset(0 0 100% 0)' }} animate={{ clipPath: 'inset(0 0 0% 0)' }} exit={{ clipPath: 'inset(0 0 100% 0)' }} transition={{ duration: reduceMotion ? 0 : 0.38, ease: [0.22, 1, 0.36, 1] }} className="overflow-hidden border-t bg-surface lg:hidden" aria-label="Navigasi mobile"><div className="mx-auto grid max-w-[1440px] border-x">{navigation.map(([label, routeName], index) => <Link key={routeName} href={route(routeName)} onClick={() => setMenuOpen(false)} className="flex items-center justify-between border-b px-5 py-4 font-heading text-xl"><span>{label}</span><span className="font-mono text-[10px] text-muted">0{index + 1}</span></Link>)}<Link href={route('cv.show')} onClick={() => setMenuOpen(false)} className="px-5 py-4 font-mono text-xs uppercase text-accent">Unduh CV ↗</Link></div></motion.nav>}
            </AnimatePresence>
        </header>

        {flash.success && <div role="status" className="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8"><div className="rounded-md border border-success/30 bg-success/10 p-3 text-sm text-success">{flash.success}</div></div>}
        {flash.error && <div role="alert" className="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8"><div className="rounded-md border border-destructive/30 bg-destructive/10 p-3 text-sm text-destructive">{flash.error}</div></div>}

        <Suspense fallback={null}><GsapAnimations scope={contentRef} pageKey={page.url} /></Suspense>
        <motion.main id="main-content" ref={contentRef} key={page.url} initial={reduceMotion ? false : { opacity: 0 }} animate={{ opacity: 1 }} transition={{ duration: reduceMotion ? 0 : 0.25 }}>{children}</motion.main>

        <footer className="border-t bg-foreground text-background" data-reveal><div className="mx-auto grid max-w-[1440px] gap-10 px-5 py-12 sm:px-8 md:grid-cols-[1fr_auto] lg:px-12"><div><p className="font-heading text-2xl font-bold tracking-[-.04em]">{siteName}</p><p className="mt-4 max-w-md text-sm leading-6 opacity-60">{footerText}</p></div><div className="md:text-right"><p className="font-mono text-[10px] uppercase tracking-wider opacity-60">{site.profile?.email}</p><div className="mt-5 flex gap-6 md:justify-end">{site.profile?.github_url && <a href={site.profile.github_url} target="_blank" rel="noreferrer" className="font-mono text-[10px] uppercase tracking-wider hover:text-accent">GitHub ↗</a>}{site.profile?.linkedin_url && <a href={site.profile.linkedin_url} target="_blank" rel="noreferrer" className="font-mono text-[10px] uppercase tracking-wider hover:text-accent">LinkedIn ↗</a>}</div></div><div className="border-t border-background/20 pt-5 font-mono text-[9px] uppercase tracking-[.16em] opacity-50 md:col-span-2">Designed with intent · Built for the web</div></div></footer>
        {site.settings.chatbot_enabled === 'true' && <ChatbotLauncher welcomeMessage={site.settings.chatbot_welcome_message ?? 'Halo! Saya dapat membantu Anda mengenal portfolio ini.'} />}
    </div>;
}
