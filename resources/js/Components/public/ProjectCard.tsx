import { AppIcon } from '@/Components/common/app-icon';
import { mediaUrl } from '@/lib/media';
import { Link } from '@inertiajs/react';
import { motion, useReducedMotion } from 'motion/react';

export type PublicProject = { id: number; title: string; slug: string; short_description: string; role?: string; thumbnail?: string; is_featured?: boolean; category: { name: string; slug?: string }; technologies: { id: number; name: string; slug: string; icon_key?: string }[] };

export function ProjectCard({ project, index = 0, lead = false }: { project: PublicProject; index?: number; lead?: boolean }) {
    const reduceMotion = useReducedMotion();

    return <motion.article whileHover={reduceMotion ? undefined : { y: -4 }} transition={{ duration: 0.25 }} className={`group border-t border-border pt-4 ${lead ? 'md:col-span-2' : ''}`} data-project-card>
        <div className={`grid gap-5 ${lead ? 'md:grid-cols-[1.45fr_.55fr]' : ''}`}>
            <Link href={route('projects.show', project.slug)} className="relative block overflow-hidden bg-surface-secondary">
                <img src={mediaUrl(project.thumbnail)} alt={`Thumbnail ${project.title}`} loading="lazy" className={`w-full object-cover grayscale-[70%] transition duration-700 group-hover:scale-[1.025] group-hover:grayscale-0 ${lead ? 'aspect-[16/8]' : 'aspect-[16/10]'}`} />
                <span className="absolute left-3 top-3 bg-background px-2 py-1 font-mono text-[10px] uppercase tracking-widest">PRJ/{String(index + 1).padStart(2, '0')}</span>
            </Link>
            <div className={lead ? 'flex flex-col justify-between py-1' : 'pt-1'}>
                <div><p className="font-mono text-[10px] uppercase tracking-[0.16em] text-accent">{project.category.name}{project.role ? ` / ${project.role}` : ''}</p><h3 className="mt-3 font-heading text-2xl font-semibold tracking-[-0.035em] sm:text-3xl"><Link href={route('projects.show', project.slug)}>{project.title}</Link></h3><p className="mt-3 line-clamp-3 text-sm leading-6 text-muted">{project.short_description}</p></div>
                <div><div className="mt-5 flex flex-wrap gap-x-4 gap-y-2 font-mono text-[10px] uppercase tracking-wider text-muted">{project.technologies.slice(0, 4).map((technology) => <span key={technology.id}>{technology.name}</span>)}</div><Link href={route('projects.show', project.slug)} className="editorial-link mt-6">Buka studi kasus <AppIcon name="chevronRight" className="size-4 transition-transform group-hover:translate-x-1" /></Link></div>
            </div>
        </div>
    </motion.article>;
}
