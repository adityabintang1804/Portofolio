import { AppLogo } from '@/Components/common/app-logo';
import { ThemeSwitcher } from '@/Components/common/theme-switcher';
import type { PropsWithChildren } from 'react';

export default function GuestLayout({ children }: PropsWithChildren) {
    return (
        <div className="grid min-h-screen place-items-center bg-background px-4 py-10">
            <div className="w-full max-w-md">
                <div className="mb-8 flex items-center justify-between">
                    <AppLogo />
                    <ThemeSwitcher />
                </div>
                <div className="rounded-xl border bg-surface p-6 shadow-2xl shadow-black/10 sm:p-8">
                    {children}
                </div>
            </div>
        </div>
    );
}
