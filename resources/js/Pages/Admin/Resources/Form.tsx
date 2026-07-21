import InputError from '@/Components/InputError';
import { Button } from '@/Components/ui/button';
import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';

type Option = { value: string; label: string };
type Field = { name: string; label: string; type: 'text' | 'url' | 'textarea' | 'number' | 'date' | 'checkbox' | 'select' | 'file'; default?: string | number | boolean; options?: Option[] };
type FormValue = string | number | boolean | File | null;

export default function ResourceForm({ resource, title, singular, fields, record }: { resource: string; title: string; singular: string; fields: Field[]; record: Record<string, unknown> | null }) {
    const initialValues = fields.reduce<Record<string, FormValue>>((values, field) => {
        const existing = record?.[field.name];
        values[field.name] = field.type === 'file' ? null : field.type === 'checkbox' ? Boolean(existing ?? field.default ?? false) : (existing as string | number | null) ?? field.default ?? '';
        return values;
    }, {});
    const formValues: Record<string, FormValue> = { ...initialValues, ...(record ? { _method: 'put' } : {}) };
    const { data, setData, post, processing, errors } = useForm<Record<string, FormValue>>(formValues);
    const submit = (event: FormEvent) => {
        event.preventDefault();
        const options = { forceFormData: true, preserveScroll: true };
        if (record) post(route(`admin.${resource}.update`, Number(record.id)), options);
        else post(route(`admin.${resource}.store`), options);
    };

    return <AdminLayout>
        <Head title={`${record ? 'Edit' : 'Tambah'} ${singular}`} />
        <div><Link href={route(`admin.${resource}.index`)} className="text-sm text-accent">← Kembali ke {title}</Link><h1 className="mt-3 font-heading text-3xl font-semibold">{record ? 'Edit' : 'Tambah'} {singular}</h1></div>
        <form onSubmit={submit} className="mt-7 max-w-3xl space-y-5 rounded-lg border bg-surface p-5 sm:p-7">
            {fields.map((field) => <div key={field.name} className={field.type === 'checkbox' ? '' : 'space-y-2'}>
                {field.type === 'checkbox' ? <label className="flex items-center gap-3"><input type="checkbox" checked={Boolean(data[field.name])} onChange={(event) => setData(field.name, event.target.checked)} className="rounded border-border text-primary" /><span className="text-sm font-medium">{field.label}</span></label> : <><label htmlFor={field.name} className="text-sm font-medium">{field.label}</label>{field.type === 'textarea' ? <textarea id={field.name} value={String(data[field.name] ?? '')} onChange={(event) => setData(field.name, event.target.value)} rows={5} className="block w-full rounded-md border bg-background px-3 py-2" /> : field.type === 'select' ? <select id={field.name} value={String(data[field.name] ?? '')} onChange={(event) => setData(field.name, event.target.value)} className="block min-h-11 w-full rounded-md border bg-background px-3"><option value="">Pilih...</option>{field.options?.map((option) => <option key={option.value} value={option.value}>{option.label}</option>)}</select> : field.type === 'file' ? <input id={field.name} type="file" accept="image/jpeg,image/png,image/webp" onChange={(event) => setData(field.name, event.target.files?.[0] ?? null)} className="block w-full rounded-md border bg-background p-2 text-sm" /> : <input id={field.name} type={field.type} value={String(data[field.name] ?? '')} onChange={(event) => setData(field.name, field.type === 'number' ? Number(event.target.value) : event.target.value)} className="block min-h-11 w-full rounded-md border bg-background px-3" />}</>}
                <InputError message={errors[field.name]} />
            </div>)}
            <div className="flex justify-end gap-3 border-t pt-5"><Button asChild variant="outline"><Link href={route(`admin.${resource}.index`)}>Batal</Link></Button><Button type="submit" disabled={processing}>{processing ? 'Menyimpan...' : 'Simpan'}</Button></div>
        </form>
    </AdminLayout>;
}
