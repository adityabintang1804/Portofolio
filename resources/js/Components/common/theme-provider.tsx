import {
    createContext,
    type PropsWithChildren,
    useContext,
    useEffect,
    useMemo,
    useState,
} from 'react';

export type Theme = 'dark' | 'light' | 'system';

type ThemeContextValue = {
    theme: Theme;
    setTheme: (theme: Theme) => void;
};

const ThemeContext = createContext<ThemeContextValue | null>(null);

function applyTheme(theme: Theme) {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const resolvedTheme = theme === 'system' ? (prefersDark ? 'dark' : 'light') : theme;

    document.documentElement.classList.remove('dark', 'light');
    document.documentElement.classList.add(resolvedTheme);
}

export function ThemeProvider({ children }: PropsWithChildren) {
    const [theme, setThemeState] = useState<Theme>(() => {
        const storedTheme = localStorage.getItem('portfolio-theme');
        return storedTheme === 'dark' || storedTheme === 'light' || storedTheme === 'system'
            ? storedTheme
            : 'system';
    });

    useEffect(() => {
        const media = window.matchMedia('(prefers-color-scheme: dark)');
        const handleSystemChange = () => theme === 'system' && applyTheme(theme);

        applyTheme(theme);
        media.addEventListener('change', handleSystemChange);
        return () => media.removeEventListener('change', handleSystemChange);
    }, [theme]);

    const value = useMemo(
        () => ({
            theme,
            setTheme: (nextTheme: Theme) => {
                localStorage.setItem('portfolio-theme', nextTheme);
                setThemeState(nextTheme);
            },
        }),
        [theme],
    );

    return <ThemeContext.Provider value={value}>{children}</ThemeContext.Provider>;
}

export function useTheme() {
    const context = useContext(ThemeContext);
    if (!context) throw new Error('useTheme harus digunakan di dalam ThemeProvider.');
    return context;
}
