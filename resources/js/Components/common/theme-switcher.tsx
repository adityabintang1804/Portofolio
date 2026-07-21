import { AppIcon } from '@/Components/common/app-icon';
import { cn } from '@/lib/utils';
import { type Theme, useTheme } from './theme-provider';

const themes: { value: Theme; label: string; icon: 'sun' | 'moon' | 'monitor' }[] = [
    { value: 'light', label: 'Terang', icon: 'sun' },
    { value: 'dark', label: 'Gelap', icon: 'moon' },
    { value: 'system', label: 'Sistem', icon: 'monitor' },
];

export function ThemeSwitcher() {
    const { theme, setTheme } = useTheme();

    return (
        <div className="inline-flex rounded-md border bg-surface-secondary p-1" aria-label="Pilihan tema">
            {themes.map((item) => (
                <button
                    key={item.value}
                    type="button"
                    onClick={() => setTheme(item.value)}
                    className={cn(
                        'inline-flex min-h-9 items-center gap-2 rounded-sm px-1.5 text-sm text-muted transition-colors sm:px-2.5',
                        theme === item.value && 'bg-surface text-foreground shadow-sm',
                    )}
                    aria-pressed={theme === item.value}
                    title={`Tema ${item.label}`}
                >
                    <AppIcon name={item.icon} className="size-4" />
                    <span className="sr-only sm:not-sr-only">{item.label}</span>
                </button>
            ))}
        </div>
    );
}
