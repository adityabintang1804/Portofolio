import { Button } from '@/Components/ui/button';
import PublicLayout from '@/Layouts/PublicLayout';
import { mediaUrl } from '@/lib/media';
import { Head, Link, router } from '@inertiajs/react';
import { AnimatePresence, motion, useReducedMotion } from 'motion/react';
import { useEffect, useRef, useState } from 'react';

type Certificate = { id: number; title: string; issuer: string; issued_at: string; credential_id?: string; credential_url?: string; certificate_image?: string; is_featured: boolean; category: { name: string; slug: string } };
type Page = { data: Certificate[]; links: { url: string | null; label: string; active: boolean }[] };
type Option = { name: string; slug: string };

export default function Certificates({ certificates, categories, issuers, filters }: { certificates: Page; categories: Option[]; issuers: string[]; filters: { category?: string; issuer?: string } }) {
    const [selected, setSelected] = useState<Certificate | null>(null);
    const closeButton = useRef<HTMLButtonElement>(null);
    const reduceMotion = useReducedMotion();
    const apply = (category: string, issuer: string) => router.get(route('certificates'), { category, issuer }, { preserveState: true, replace: true });

    useEffect(() => {
        if (!selected) return;
        const previousFocus = document.activeElement as HTMLElement | null;
        const close = () => setSelected(null);
        const handleKey = (event: KeyboardEvent) => {
            if (event.key === 'Escape') close();
            if (event.key !== 'Tab') return;

            const dialog = closeButton.current?.closest('[role="dialog"]');
            const focusable = Array.from(dialog?.querySelectorAll<HTMLElement>('a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])') ?? []);
            if (!focusable.length) return;
            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
            if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
        };
        document.addEventListener('keydown', handleKey);
        document.body.style.overflow = 'hidden';
        closeButton.current?.focus();
        return () => {
            document.removeEventListener('keydown', handleKey);
            document.body.style.overflow = '';
            previousFocus?.focus();
        };
    }, [selected]);

    return <PublicLayout>
        <Head title="Sertifikat" />
        <section className="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <p className="text-sm text-accent">Credentials</p><h1 className="mt-3 font-heading text-5xl font-semibold">Sertifikat</h1>
            <div className="mt-8 flex flex-wrap gap-3">
                <select aria-label="Filter kategori" value={filters.category ?? ''} onChange={(event) => apply(event.target.value, filters.issuer ?? '')} className="min-h-11 rounded-md border bg-surface px-3"><option value="">Semua kategori</option>{categories.map((item) => <option key={item.slug} value={item.slug}>{item.name}</option>)}</select>
                <select aria-label="Filter penerbit" value={filters.issuer ?? ''} onChange={(event) => apply(filters.category ?? '', event.target.value)} className="min-h-11 rounded-md border bg-surface px-3"><option value="">Semua penerbit</option>{issuers.map((issuer) => <option key={issuer} value={issuer}>{issuer}</option>)}</select>
            </div>
            {certificates.data.length ? <motion.div layout className="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">{certificates.data.map((certificate) => <motion.button layout key={certificate.id} type="button" onClick={() => setSelected(certificate)} whileHover={reduceMotion ? undefined : { y: -6 }} whileTap={reduceMotion ? undefined : { scale: 0.985 }} className="overflow-hidden rounded-xl border bg-surface text-left"><img src={mediaUrl(certificate.certificate_image)} alt={`Sertifikat ${certificate.title}`} loading="lazy" className="aspect-[4/3] w-full object-cover" /><div className="p-5"><p className="text-xs text-accent">{certificate.category.name}{certificate.is_featured ? ' · Unggulan' : ''}</p><h2 className="mt-2 font-heading text-lg font-semibold">{certificate.title}</h2><p className="mt-2 text-sm text-muted">{certificate.issuer} · {new Date(certificate.issued_at).getFullYear()}</p></div></motion.button>)}</motion.div> : <div className="mt-10 rounded-xl border border-dashed p-12 text-center text-muted">Belum ada sertifikat untuk filter ini.</div>}
            <div className="mt-8 flex gap-2">{certificates.links.map((link, index) => link.url ? <Link key={index} href={link.url} className={`rounded border px-3 py-2 text-sm ${link.active ? 'bg-primary text-primary-foreground' : ''}`} dangerouslySetInnerHTML={{ __html: link.label }} /> : null)}</div>
        </section>

        <AnimatePresence>
            {selected && <motion.div initial={reduceMotion ? false : { opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} transition={{ duration: reduceMotion ? 0 : 0.25 }} className="fixed inset-0 z-50 grid place-items-center bg-black/75 p-4" role="dialog" aria-modal="true" aria-labelledby="certificate-title" onMouseDown={(event) => event.target === event.currentTarget && setSelected(null)}>
                <motion.div initial={reduceMotion ? false : { opacity: 0, scale: 0.96, y: 20 }} animate={{ opacity: 1, scale: 1, y: 0 }} exit={{ opacity: 0, scale: 0.98 }} transition={{ duration: reduceMotion ? 0 : 0.3 }} className="max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-xl border bg-surface p-4 sm:p-6">
                    <img src={mediaUrl(selected.certificate_image)} alt={`Sertifikat ${selected.title}`} className="w-full rounded-lg" />
                    <div className="mt-5 flex flex-wrap items-end justify-between gap-4"><div><h2 id="certificate-title" className="font-heading text-2xl font-semibold">{selected.title}</h2><p className="mt-1 text-muted">{selected.issuer}{selected.credential_id ? ` · ${selected.credential_id}` : ''}</p></div><div className="flex gap-2">{selected.credential_url && <Button asChild><a href={selected.credential_url} target="_blank" rel="noreferrer">Lihat credential</a></Button>}<Button ref={closeButton} variant="outline" onClick={() => setSelected(null)}>Tutup</Button></div></div>
                </motion.div>
            </motion.div>}
        </AnimatePresence>
    </PublicLayout>;
}
