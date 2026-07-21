import { AppIcon, type AppIconName } from '@/Components/common/app-icon';
import { AppLogo } from '@/Components/common/app-logo';
import { ThemeSwitcher } from '@/Components/common/theme-switcher';
import type { PageProps } from '@/types';
import { Link, usePage } from '@inertiajs/react';
import type { PropsWithChildren } from 'react';

type NavigationItem = { label: string; route: string; active: string; icon: AppIconName };

const navigation: NavigationItem[] = [
    { label: 'Dashboard', route: 'admin.dashboard', active: 'admin.dashboard', icon: 'home' },
    { label: 'Profil', route: 'admin.profile.edit', active: 'admin.profile.*', icon: 'user' },
    { label: 'Proyek', route: 'admin.projects.index', active: 'admin.projects.*', icon: 'folder' },
    { label: 'Kategori Proyek', route: 'admin.project-categories.index', active: 'admin.project-categories.*', icon: 'folder' },
    { label: 'Teknologi', route: 'admin.technologies.index', active: 'admin.technologies.*', icon: 'settings' },
    { label: 'Skills', route: 'admin.skills.index', active: 'admin.skills.*', icon: 'settings' },
    { label: 'Kategori Skill', route: 'admin.skill-categories.index', active: 'admin.skill-categories.*', icon: 'settings' },
    { label: 'Pengalaman', route: 'admin.experiences.index', active: 'admin.experiences.*', icon: 'briefcase' },
    { label: 'Pendidikan', route: 'admin.educations.index', active: 'admin.educations.*', icon: 'file' },
    { label: 'Sertifikat', route: 'admin.certificates.index', active: 'admin.certificates.*', icon: 'file' },
    { label: 'Kategori Sertifikat', route: 'admin.certificate-categories.index', active: 'admin.certificate-categories.*', icon: 'file' },
    { label: 'Pesan Masuk', route: 'admin.messages.index', active: 'admin.messages.*', icon: 'file' },
    { label: 'FAQ Chatbot', route: 'admin.chatbot-faqs.index', active: 'admin.chatbot-faqs.*', icon: 'settings' },
    { label: 'Pengaturan', route: 'admin.settings.edit', active: 'admin.settings.*', icon: 'settings' },
];

export default function AdminLayout({ children }: PropsWithChildren) {
    const { auth, flash } = usePage<PageProps>().props;

    return (
        <div className="min-h-screen bg-background text-foreground lg:grid lg:grid-cols-[270px_1fr]">
            <aside className="hidden border-r bg-surface lg:flex lg:h-screen lg:flex-col lg:sticky lg:top-0">
                <div className="flex h-16 shrink-0 items-center border-b px-5"><AppLogo /></div>
                <nav className="flex-1 space-y-1 overflow-y-auto p-3" aria-label="Navigasi dashboard">
                    {navigation.map((item) => {
                        const active = route().current(item.active);
                        return (
                            <Link key={item.route} href={route(item.route)} className={`flex min-h-10 items-center gap-3 rounded-md px-3 text-sm transition-colors ${active ? 'bg-primary/15 text-foreground' : 'text-muted hover:bg-surface-secondary hover:text-foreground'}`}>
                                <AppIcon name={item.icon} className="size-4" />{item.label}
                            </Link>
                        );
                    })}
                </nav>
            </aside>

            <div className="min-w-0">
                <header className="sticky top-0 z-30 flex min-h-16 items-center justify-between border-b bg-surface/95 px-4 backdrop-blur sm:px-6">
                    <div className="lg:hidden"><AppLogo compact /></div>
                    <div className="hidden lg:block"><p className="text-xs text-muted">Administrator</p><p className="text-sm font-semibold">{auth.user.name}</p></div>
                    <div className="flex items-center gap-2">
                        <ThemeSwitcher />
                        <Link href={route('logout')} method="post" as="button" className="inline-flex size-10 items-center justify-center rounded-sm text-muted hover:bg-surface-secondary hover:text-foreground" aria-label="Keluar">
                            <AppIcon name="logout" className="size-5" />
                        </Link>
                    </div>
                </header>

                <nav className="flex gap-2 overflow-x-auto border-b bg-surface px-4 py-2 lg:hidden" aria-label="Navigasi dashboard mobile">
                    {navigation.map((item) => <Link key={item.route} href={route(item.route)} className="shrink-0 rounded-md border px-3 py-2 text-xs">{item.label}</Link>)}
                </nav>

                <main className="p-4 sm:p-6 lg:p-8">
                    {flash.success && <div role="status" className="mb-5 rounded-md border border-success/30 bg-success/10 p-3 text-sm text-success">{flash.success}</div>}
                    {flash.error && <div role="alert" className="mb-5 rounded-md border border-destructive/30 bg-destructive/10 p-3 text-sm text-destructive">{flash.error}</div>}
                    {children}
                </main>
            </div>
        </div>
    );
}
