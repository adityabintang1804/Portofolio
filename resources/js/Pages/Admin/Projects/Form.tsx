import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link, router, useForm } from '@inertiajs/react';
import type { FormEvent, ReactNode } from 'react';

type Option = { id: number; name: string };
type ProjectImage = { id: number; image: string; alt_text: string };
type Project = Record<string, unknown> & { id: number; technology_ids: number[]; images: ProjectImage[] };
type FormField = 'title' | 'slug' | 'short_description' | 'overview' | 'background' | 'problem' | 'goal' | 'role' | 'process' | 'challenges' | 'solution' | 'result' | 'github_url' | 'demo_url' | 'seo_title' | 'seo_description';

const studyCaseFields: { name: FormField; label: string; rows: number }[] = [
    { name: 'background', label: 'Latar belakang', rows: 5 },
    { name: 'problem', label: 'Permasalahan', rows: 5 },
    { name: 'goal', label: 'Tujuan', rows: 4 },
    { name: 'process', label: 'Proses pengembangan', rows: 6 },
    { name: 'challenges', label: 'Tantangan', rows: 5 },
    { name: 'solution', label: 'Solusi', rows: 5 },
    { name: 'result', label: 'Hasil', rows: 5 },
];

export default function ProjectForm({ project, categories, technologies }: { project: Project | null; categories: Option[]; technologies: Option[] }) {
    const { data, setData, post, processing, errors } = useForm({
        project_category_id: Number(project?.project_category_id ?? ''),
        title: String(project?.title ?? ''), slug: String(project?.slug ?? ''),
        short_description: String(project?.short_description ?? ''), overview: String(project?.overview ?? ''),
        background: String(project?.background ?? ''), problem: String(project?.problem ?? ''), goal: String(project?.goal ?? ''),
        role: String(project?.role ?? ''), process: String(project?.process ?? ''), challenges: String(project?.challenges ?? ''),
        solution: String(project?.solution ?? ''), result: String(project?.result ?? ''),
        github_url: String(project?.github_url ?? ''), demo_url: String(project?.demo_url ?? ''),
        status: String(project?.status ?? 'draft'), is_featured: Boolean(project?.is_featured ?? false),
        display_order: Number(project?.display_order ?? 0),
        published_at: project?.published_at ? String(project.published_at).slice(0, 16) : '',
        seo_title: String(project?.seo_title ?? ''), seo_description: String(project?.seo_description ?? ''),
        technology_ids: project?.technology_ids ?? [] as number[],
        thumbnail: null as File | null, og_image: null as File | null, gallery_images: [] as File[],
        ...(project ? { _method: 'put' } : {}),
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();
        post(project ? route('admin.projects.update', project.id) : route('admin.projects.store'), { forceFormData: true, preserveScroll: true });
    };
    const setTitle = (value: string) => {
        setData('title', value);
        if (!project) setData('slug', value.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, ''));
    };
    const toggleTechnology = (id: number) => setData('technology_ids', data.technology_ids.includes(id) ? data.technology_ids.filter((item) => item !== id) : [...data.technology_ids, id]);
    const removeImage = (image: ProjectImage) => {
        if (project && window.confirm('Hapus gambar galeri ini?')) router.delete(route('admin.projects.images.destroy', [project.id, image.id]), { preserveScroll: true });
    };

    return <AdminLayout>
        <Head title={`${project ? 'Edit' : 'Tambah'} Proyek`} />
        <Link href={route('admin.projects.index')} className="text-sm text-accent">← Kembali ke proyek</Link>
        <h1 className="mt-3 font-heading text-3xl font-semibold">{project ? 'Edit' : 'Tambah'} Proyek</h1>
        <form onSubmit={submit} className="mt-7 max-w-5xl space-y-6">
            <Section title="Informasi dasar">
                <Select label="Kategori" value={String(data.project_category_id)} onChange={(value) => setData('project_category_id', Number(value))} options={categories} error={errors.project_category_id} />
                <Field name="title" label="Judul" value={data.title} onChange={setTitle} error={errors.title} />
                <Field name="slug" label="Slug" value={data.slug} onChange={(value) => setData('slug', value)} error={errors.slug} />
                <Field name="role" label="Peran dan kontribusi" value={data.role} onChange={(value) => setData('role', value)} error={errors.role} />
                <Area name="short_description" label="Deskripsi singkat" rows={3} value={data.short_description} onChange={(value) => setData('short_description', value)} error={errors.short_description} />
                <Area name="overview" label="Overview" rows={6} value={data.overview} onChange={(value) => setData('overview', value)} error={errors.overview} />
            </Section>

            <Section title="Studi kasus">
                {studyCaseFields.map((field) => <Area key={field.name} {...field} value={data[field.name]} onChange={(value) => setData(field.name, value)} error={errors[field.name]} />)}
            </Section>

            <Section title="Teknologi">
                <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">{technologies.map((technology) => <label key={technology.id} className="flex items-center gap-3 rounded-md border p-3"><input type="checkbox" checked={data.technology_ids.includes(technology.id)} onChange={() => toggleTechnology(technology.id)} />{technology.name}</label>)}</div>
                <InputError message={errors.technology_ids} />
            </Section>

            <Section title="Media dan galeri">
                <div className="grid gap-5 sm:grid-cols-2"><FileInput label="Thumbnail" onChange={(file) => setData('thumbnail', file)} error={errors.thumbnail} /><FileInput label="Open Graph image" onChange={(file) => setData('og_image', file)} error={errors.og_image} /></div>
                <div className="space-y-2"><label className="text-sm font-medium">Tambah gambar galeri (maksimal 10)</label><input type="file" multiple accept="image/jpeg,image/png,image/webp" onChange={(event) => setData('gallery_images', Array.from(event.target.files ?? []))} className="block w-full rounded-md border bg-background p-2 text-sm" /><InputError message={errors.gallery_images} /></div>
                {project?.images?.length ? <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">{project.images.map((image) => <div key={image.id} className="overflow-hidden rounded-md border"><img src={`/storage/${image.image}`} alt={image.alt_text} className="aspect-[16/10] w-full object-cover" /><button type="button" onClick={() => removeImage(image)} className="w-full p-2 text-sm text-destructive">Hapus gambar</button></div>)}</div> : <p className="text-sm text-muted">Belum ada gambar galeri.</p>}
            </Section>

            <Section title="Link dan SEO">
                <Field name="github_url" label="GitHub URL" value={data.github_url} onChange={(value) => setData('github_url', value)} error={errors.github_url} />
                <Field name="demo_url" label="Demo URL" value={data.demo_url} onChange={(value) => setData('demo_url', value)} error={errors.demo_url} />
                <Field name="seo_title" label="SEO title" value={data.seo_title} onChange={(value) => setData('seo_title', value)} error={errors.seo_title} />
                <Area name="seo_description" label="SEO description" rows={3} value={data.seo_description} onChange={(value) => setData('seo_description', value)} error={errors.seo_description} />
            </Section>

            <Section title="Publikasi">
                <div className="grid gap-5 sm:grid-cols-3"><Select label="Status" value={data.status} onChange={(value) => setData('status', value)} options={[{ id: 'draft', name: 'Draft' }, { id: 'published', name: 'Published' }, { id: 'archived', name: 'Archived' }]} error={errors.status} /><Field name="display_order" label="Urutan" type="number" value={String(data.display_order)} onChange={(value) => setData('display_order', Number(value))} error={errors.display_order} /><Field name="published_at" label="Tanggal publikasi" type="datetime-local" value={data.published_at} onChange={(value) => setData('published_at', value)} error={errors.published_at} /></div>
                <label className="flex items-center gap-3"><input type="checkbox" checked={data.is_featured} onChange={(event) => setData('is_featured', event.target.checked)} />Proyek unggulan</label>
            </Section>
            <div className="flex justify-end"><Button type="submit" disabled={processing}>{processing ? 'Menyimpan...' : 'Simpan proyek'}</Button></div>
        </form>
    </AdminLayout>;
}

function Section({ title, children }: { title: string; children: ReactNode }) { return <section className="space-y-5 rounded-lg border bg-surface p-5 sm:p-7"><h2 className="font-heading text-xl font-semibold">{title}</h2>{children}</section>; }
function Field({ name, label, value, onChange, error, type = 'text' }: { name: string; label: string; value: string; onChange: (value: string) => void; error?: string; type?: string }) { return <div className="space-y-2"><label htmlFor={name} className="text-sm font-medium">{label}</label><input id={name} type={type} value={value} onChange={(event) => onChange(event.target.value)} className="block min-h-11 w-full rounded-md border bg-background px-3" /><InputError message={error} /></div>; }
function Area({ name, label, value, onChange, error, rows }: { name: string; label: string; value: string; onChange: (value: string) => void; error?: string; rows: number }) { return <div className="space-y-2"><label htmlFor={name} className="text-sm font-medium">{label}</label><textarea id={name} rows={rows} value={value} onChange={(event) => onChange(event.target.value)} className="block w-full rounded-md border bg-background px-3 py-2" /><InputError message={error} /></div>; }
function Select({ label, value, onChange, options, error }: { label: string; value: string; onChange: (value: string) => void; options: { id: number | string; name: string }[]; error?: string }) { return <div className="space-y-2"><label className="text-sm font-medium">{label}</label><select value={value} onChange={(event) => onChange(event.target.value)} className="block min-h-11 w-full rounded-md border bg-background px-3"><option value="">Pilih...</option>{options.map((option) => <option key={option.id} value={option.id}>{option.name}</option>)}</select><InputError message={error} /></div>; }
function FileInput({ label, onChange, error }: { label: string; onChange: (file: File | null) => void; error?: string }) { return <div className="space-y-2"><label className="text-sm font-medium">{label}</label><input type="file" accept="image/jpeg,image/png,image/webp" onChange={(event) => onChange(event.target.files?.[0] ?? null)} className="block w-full rounded-md border bg-background p-2 text-sm" /><InputError message={error} /></div>; }
