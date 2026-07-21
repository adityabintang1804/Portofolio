import { Button } from '@/Components/ui/button';
import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link, router } from '@inertiajs/react';
import { useState, type FormEvent } from 'react';

type Column = { key: string; label: string };
type RecordItem = { id: number; [key: string]: unknown };
type Pagination = { data: RecordItem[]; links: { url: string | null; label: string; active: boolean }[] };

function valueAt(record: RecordItem, path: string): unknown {
    return path.split('.').reduce<unknown>((value, key) => value && typeof value === 'object' ? (value as Record<string, unknown>)[key] : null, record);
}

function displayValue(value: unknown) {
    if (typeof value === 'boolean' || value === 0 || value === 1) return Boolean(value) ? 'Ya' : 'Tidak';
    return value === null || value === undefined || value === '' ? '—' : String(value);
}

export default function ResourceIndex({ resource, title, singular, columns, records, filters }: { resource: string; title: string; singular: string; columns: Column[]; records: Pagination; filters: { search?: string } }) {
    const [search, setSearch] = useState(filters.search ?? '');
    const submit = (event: FormEvent) => { event.preventDefault(); router.get(route(`admin.${resource}.index`), search ? { search } : {}, { preserveState: true, replace: true }); };
    const remove = (id: number) => { if (window.confirm(`Hapus ${singular.toLowerCase()} ini?`)) router.delete(route(`admin.${resource}.destroy`, id), { preserveScroll: true }); };

    return <AdminLayout>
        <Head title={title} />
        <div className="flex flex-wrap items-end justify-between gap-4"><div><p className="text-sm text-accent">Manajemen konten</p><h1 className="mt-1 font-heading text-3xl font-semibold">{title}</h1></div><Button asChild><Link href={route(`admin.${resource}.create`)}>Tambah {singular}</Link></Button></div>
        <form onSubmit={submit} className="mt-6 flex max-w-lg gap-2"><input value={search} onChange={(event) => setSearch(event.target.value)} placeholder={`Cari ${singular.toLowerCase()}...`} className="min-h-10 flex-1 rounded-md border bg-surface px-3" /><Button type="submit" variant="outline">Cari</Button></form>
        <div className="mt-5 overflow-hidden rounded-lg border bg-surface"><div className="overflow-x-auto"><table className="w-full text-left text-sm"><thead className="bg-surface-secondary text-muted"><tr>{columns.map((column) => <th key={column.key} className="px-4 py-3 font-medium">{column.label}</th>)}<th className="px-4 py-3 text-right">Aksi</th></tr></thead><tbody className="divide-y">{records.data.map((record) => <tr key={record.id}>{columns.map((column) => <td key={column.key} className="max-w-xs truncate px-4 py-3">{displayValue(valueAt(record, column.key))}</td>)}<td className="whitespace-nowrap px-4 py-3 text-right"><Link href={route(`admin.${resource}.edit`, record.id)} className="text-accent hover:underline">Edit</Link><button type="button" onClick={() => remove(record.id)} className="ms-4 text-destructive hover:underline">Hapus</button></td></tr>)}</tbody></table></div>{records.data.length === 0 && <p className="p-8 text-center text-sm text-muted">Belum ada data.</p>}</div>
        <div className="mt-5 flex flex-wrap gap-2">{records.links.map((link, index) => link.url ? <Link key={index} href={link.url} preserveScroll className={`rounded-sm border px-3 py-2 text-sm ${link.active ? 'bg-primary text-primary-foreground' : ''}`} dangerouslySetInnerHTML={{ __html: link.label }} /> : <span key={index} className="rounded-sm border px-3 py-2 text-sm opacity-40" dangerouslySetInnerHTML={{ __html: link.label }} />)}</div>
    </AdminLayout>;
}
