import InputError from '@/Components/InputError';
import GuestLayout from '@/Layouts/GuestLayout';
import { Button } from '@/Components/ui/button';
import { Head, Link, useForm } from '@inertiajs/react';
import type { FormEventHandler } from 'react';

export default function Login({
    status,
    canResetPassword,
}: {
    status?: string;
    canResetPassword: boolean;
}) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit: FormEventHandler = (event) => {
        event.preventDefault();
        post(route('login'), { onFinish: () => reset('password') });
    };

    return (
        <GuestLayout>
            <Head title="Login Admin" />
            <h1 className="font-heading text-2xl font-semibold">Login admin</h1>
            <p className="mt-2 text-sm text-muted">Masuk untuk mengelola konten portfolio.</p>

            {status && <div className="mt-5 rounded-md bg-success/10 p-3 text-sm text-success">{status}</div>}

            <form onSubmit={submit} className="mt-7 space-y-5">
                <div>
                    <label htmlFor="email" className="text-sm font-medium">Email</label>
                    <input
                        id="email"
                        type="email"
                        value={data.email}
                        onChange={(event) => setData('email', event.target.value)}
                        autoComplete="username"
                        autoFocus
                        required
                        className="mt-2 block min-h-11 w-full rounded-md border bg-background px-3 text-foreground placeholder:text-muted"
                    />
                    <InputError message={errors.email} className="mt-2" />
                </div>

                <div>
                    <div className="flex items-center justify-between">
                        <label htmlFor="password" className="text-sm font-medium">Kata sandi</label>
                        {canResetPassword && (
                            <Link href={route('password.request')} className="text-sm text-accent hover:underline">
                                Lupa kata sandi?
                            </Link>
                        )}
                    </div>
                    <input
                        id="password"
                        type="password"
                        value={data.password}
                        onChange={(event) => setData('password', event.target.value)}
                        autoComplete="current-password"
                        required
                        className="mt-2 block min-h-11 w-full rounded-md border bg-background px-3 text-foreground"
                    />
                    <InputError message={errors.password} className="mt-2" />
                </div>

                <label className="flex items-center gap-3 text-sm text-muted">
                    <input
                        type="checkbox"
                        checked={data.remember}
                        onChange={(event) => setData('remember', event.target.checked)}
                        className="rounded border-border bg-background text-primary focus:ring-primary"
                    />
                    Ingat saya
                </label>

                <Button type="submit" className="w-full" disabled={processing}>
                    {processing ? 'Memproses...' : 'Masuk'}
                </Button>
            </form>
        </GuestLayout>
    );
}
