import { Button } from '@/Components/ui/button';
import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link, router } from '@inertiajs/react';

type Message = { id: number; name: string; email: string; organization: string | null; subject: string; message: string; status: string; created_at: string };

export default function MessageShow({ message }: { message: Message }) {
    const status = (value: string) => router.patch(route('admin.messages.status', message.id), { status: value }, { preserveScroll: true });
    const remove = () => { if (window.confirm('Hapus pesan ini secara permanen?')) router.delete(route('admin.messages.destroy', message.id)); };
    return <AdminLayout><Head title={message.subject} /><Link href={route('admin.messages.index')} className="text-sm text-accent">← Kembali ke pesan</Link><article className="mt-5 max-w-4xl rounded-lg border bg-surface p-5 sm:p-8"><div className="border-b pb-5"><div className="flex flex-wrap justify-between gap-3"><div><h1 className="font-heading text-2xl font-semibold">{message.subject}</h1><p className="mt-2 text-sm text-muted">Dari {message.name} · <a href={`mailto:${message.email}`} className="text-accent">{message.email}</a>{message.organization ? ` · ${message.organization}` : ''}</p></div><span className="h-fit rounded-full bg-surface-secondary px-3 py-1 text-xs">{message.status}</span></div></div><div className="whitespace-pre-wrap py-7 leading-7">{message.message}</div><div className="flex flex-wrap gap-2 border-t pt-5"><Button asChild><a href={`mailto:${message.email}?subject=Re: ${encodeURIComponent(message.subject)}`}>Balas melalui email</a></Button><Button variant="outline" onClick={() => status('unread')}>Tandai belum dibaca</Button><Button variant="outline" onClick={() => status('archived')}>Arsipkan</Button><Button variant="ghost" onClick={remove} className="text-destructive">Hapus</Button></div></article></AdminLayout>;
}
