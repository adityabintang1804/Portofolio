import { AppIcon } from '@/Components/common/app-icon';
import { ProjectCard, type PublicProject } from '@/Components/public/ProjectCard';
import { SectionHeader } from '@/Components/public/SectionHeader';
import { Button } from '@/Components/ui/button';
import PublicLayout from '@/Layouts/PublicLayout';
import { mediaUrl } from '@/lib/media';
import { Head, Link } from '@inertiajs/react';

type Profile = { name: string; headline: string; hero_badge: string; hero_description: string; about_description: string; profile_image: string | null; availability_status: string; availability_text: string; github_url?: string; linkedin_url?: string; email: string };
type Technology = { id: number; name: string };
type SkillCategory = { id: number; name: string; skills: { id: number; name: string; description?: string }[] };
type Experience = { id: number; position: string; organization: string; start_date: string; end_date?: string; is_current: boolean; description: string };
type Certificate = { id: number; title: string; issuer: string; issued_at: string; category: { name: string }; certificate_image?: string };

export default function Home({ profile, settings, technologies, featuredProjects, skillCategories, experiences, certificates }: { profile: Profile; settings: Record<string, string>; technologies: Technology[]; featuredProjects: PublicProject[]; skillCategories: SkillCategory[]; experiences: Experience[]; certificates: Certificate[] }) {
    const ticker = [...technologies, ...technologies];

    return <PublicLayout><Head title={settings.default_meta_title ?? profile.name} />
        <section className="public-grid relative border-b">
            <div className="mx-auto grid min-h-[calc(100svh-4rem)] max-w-[1440px] lg:grid-cols-[minmax(0,1.55fr)_minmax(320px,.45fr)]">
                <div className="flex flex-col justify-between px-5 py-12 sm:px-8 lg:px-12 lg:py-16">
                    <div className="flex items-center justify-between font-mono text-[10px] uppercase tracking-[.18em] text-muted" data-hero-kicker><span>Portfolio / Selected work</span><span className="hidden sm:block">Jakarta · Indonesia</span></div>
                    <div className="my-16 lg:my-12">
                        <div className="overflow-hidden"><p className="font-mono text-xs uppercase tracking-[.16em] text-accent" data-hero-line>{profile.hero_badge}</p></div>
                        <h1 className="mt-5 font-heading text-[clamp(3.2rem,8vw,8.5rem)] font-semibold leading-[.82] tracking-[-.075em]">
                            <span className="block overflow-hidden pb-[.08em]"><span className="block" data-hero-line>{profile.name}</span></span>
                            <span className="block overflow-hidden pb-[.1em]"><span className="block text-muted" data-hero-line>{profile.headline}</span></span>
                        </h1>
                    </div>
                    <div className="grid gap-8 border-t pt-6 md:grid-cols-[1fr_auto] md:items-end">
                        <div data-hero-copy><p className="max-w-xl text-base leading-7 text-muted sm:text-lg">{profile.hero_description}</p><p className="mt-4 flex items-center gap-2 font-mono text-[10px] uppercase tracking-[.16em] text-foreground"><span className="size-2 animate-pulse bg-success" />{profile.availability_text}</p></div>
                        <div className="flex flex-wrap gap-3" data-hero-actions><Button asChild className="rounded-none"><Link href={route('projects.index')}>Eksplor proyek <AppIcon name="chevronRight" className="size-4" /></Link></Button><Button asChild variant="outline" className="rounded-none"><Link href={route('cv.show')}>Unduh CV</Link></Button></div>
                    </div>
                </div>
                <aside className="relative border-t bg-surface lg:border-l lg:border-t-0" data-hero-visual>
                    <div className="corner-mark absolute inset-5 z-10" />
                    <div className="h-full min-h-[540px] overflow-hidden"><img src={mediaUrl(profile.profile_image)} alt={`Foto profil ${profile.name}`} className="h-[110%] w-full object-cover grayscale contrast-[1.04]" data-hero-image /></div>
                    <div className="absolute bottom-5 left-5 right-5 flex justify-between border-t border-white/40 pt-3 font-mono text-[9px] uppercase tracking-[.18em] text-white"><span>Profile / 001</span><span>{profile.availability_status}</span></div>
                </aside>
            </div>
        </section>

        {technologies.length > 0 && <section className="overflow-hidden border-b bg-foreground py-4 text-background" aria-label="Teknologi yang digunakan"><div className="ticker-track flex items-center">{ticker.map((technology, index) => <div key={`${technology.id}-${index}`} className="flex shrink-0 items-center"><span className="px-6 font-mono text-[11px] uppercase tracking-[.18em]">{technology.name}</span><span className="text-accent">✦</span></div>)}</div></section>}

        <section className="public-grid mx-auto grid max-w-[1440px] gap-12 border-x px-5 py-24 sm:px-8 lg:grid-cols-[.55fr_1.45fr] lg:px-12 lg:py-32">
            <SectionHeader eyebrow="Profile" index="01" title="Tentang saya" />
            <div data-reveal><p className="max-w-4xl whitespace-pre-line font-heading text-2xl font-medium leading-[1.45] tracking-[-.025em] text-muted sm:text-4xl">{profile.about_description}</p><div className="mt-9 flex gap-7"><Link href={route('about')} className="editorial-link">Profil lengkap <AppIcon name="chevronRight" className="size-4" /></Link>{profile.linkedin_url && <a href={profile.linkedin_url} target="_blank" rel="noreferrer" className="editorial-link">LinkedIn ↗</a>}</div></div>
        </section>

        <section className="border-y"><div className="mx-auto max-w-[1440px] border-x px-5 py-24 sm:px-8 lg:px-12 lg:py-32"><div className="flex flex-wrap items-end justify-between gap-8"><SectionHeader eyebrow="Selected work" index="02" title={settings.projects_title ?? 'Proyek pilihan'} description={settings.projects_description} /><Link href={route('projects.index')} className="editorial-link">Semua proyek <AppIcon name="chevronRight" className="size-4" /></Link></div>{featuredProjects.length ? <div className="mt-16 grid gap-x-7 gap-y-16 md:grid-cols-2">{featuredProjects.map((project, index) => <ProjectCard key={project.id} project={project} index={index} lead={index === 0} />)}</div> : <Empty text="Belum ada proyek unggulan yang dipublikasikan." />}</div></section>

        <section className="public-grid mx-auto max-w-[1440px] border-x px-5 py-24 sm:px-8 lg:px-12 lg:py-32"><SectionHeader eyebrow="Capabilities" index="03" title="Keahlian yang saya bawa ke setiap proyek" /><div className="mt-16 border-t">{skillCategories.map((category, index) => <article key={category.id} className="grid gap-5 border-b py-7 md:grid-cols-[80px_1fr_1.5fr]" data-list-item><span className="font-mono text-xs text-accent">{String(index + 1).padStart(2, '0')}</span><h3 className="font-heading text-xl font-semibold">{category.name}</h3><div className="flex flex-wrap gap-x-6 gap-y-3 font-mono text-[11px] uppercase tracking-wider text-muted">{category.skills.map((skill) => <span key={skill.id}>{skill.name}</span>)}</div></article>)}</div></section>

        <section className="border-y bg-surface"><div className="mx-auto grid max-w-[1440px] gap-20 border-x px-5 py-24 sm:px-8 lg:grid-cols-2 lg:px-12 lg:py-32">
            <div><SectionHeader eyebrow="Experience" index="04" title="Jejak profesional" /><div className="mt-12 border-t">{experiences.map((experience) => <article key={experience.id} className="grid grid-cols-[84px_1fr] gap-5 border-b py-6" data-list-item><p className="font-mono text-[10px] uppercase leading-5 text-muted">{new Date(experience.start_date).getFullYear()}—<br />{experience.is_current ? 'Kini' : experience.end_date ? new Date(experience.end_date).getFullYear() : ''}</p><div><h3 className="font-heading text-lg font-semibold">{experience.position}</h3><p className="mt-1 font-mono text-[10px] uppercase tracking-wider text-accent">{experience.organization}</p><p className="mt-3 line-clamp-2 text-sm leading-6 text-muted">{experience.description}</p></div></article>)}</div><Link href={route('experience')} className="editorial-link mt-7">Riwayat lengkap <AppIcon name="chevronRight" className="size-4" /></Link></div>
            <div><SectionHeader eyebrow="Credentials" index="05" title="Sertifikasi pilihan" /><div className="mt-12 border-t">{certificates.map((certificate, index) => <article key={certificate.id} className="group grid grid-cols-[40px_1fr_auto] items-center gap-4 border-b py-5" data-list-item><span className="font-mono text-[10px] text-muted">{String(index + 1).padStart(2, '0')}</span><div><p className="font-heading font-semibold">{certificate.title}</p><p className="mt-1 text-xs text-muted">{certificate.issuer}</p></div><span className="font-mono text-[9px] uppercase tracking-wider text-accent">{certificate.category.name}</span></article>)}</div><Link href={route('certificates')} className="editorial-link mt-7">Semua sertifikat <AppIcon name="chevronRight" className="size-4" /></Link></div>
        </div></section>

        <section className="mx-auto max-w-[1440px] border-x px-5 py-24 sm:px-8 lg:px-12 lg:py-32"><div className="grid gap-10 border-y py-12 lg:grid-cols-[1fr_auto] lg:items-end" data-reveal><SectionHeader eyebrow="Start a conversation" index="06" title={settings.contact_title ?? 'Mari membuat sesuatu yang berguna.'} description={settings.contact_description} /><div className="flex flex-wrap gap-3"><Button asChild className="rounded-none"><Link href={route('contact.create')}>Mulai percakapan</Link></Button><Button asChild variant="outline" className="rounded-none"><a href={`mailto:${profile.email}`}>{profile.email}</a></Button></div></div></section>
    </PublicLayout>;
}

function Empty({ text }: { text: string }) { return <div className="mt-12 border border-dashed p-10 text-center font-mono text-xs uppercase tracking-wider text-muted">{text}</div>; }
