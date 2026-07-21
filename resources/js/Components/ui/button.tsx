import { Slot } from '@radix-ui/react-slot';
import { cva, type VariantProps } from 'class-variance-authority';
import { forwardRef, type ButtonHTMLAttributes } from 'react';
import { cn } from '@/lib/utils';

const buttonVariants = cva(
    'inline-flex min-h-10 items-center justify-center gap-2 rounded-sm px-4 text-sm font-semibold transition-colors disabled:pointer-events-none disabled:opacity-50',
    {
        variants: {
            variant: {
                default: 'bg-primary text-primary-foreground hover:brightness-110',
                outline: 'border bg-transparent text-foreground hover:bg-surface-secondary',
                ghost: 'text-muted hover:bg-surface-secondary hover:text-foreground',
            },
            size: {
                default: 'h-10',
                sm: 'h-9 px-3',
                icon: 'size-10 px-0',
            },
        },
        defaultVariants: { variant: 'default', size: 'default' },
    },
);

export interface ButtonProps
    extends ButtonHTMLAttributes<HTMLButtonElement>,
        VariantProps<typeof buttonVariants> {
    asChild?: boolean;
}

export const Button = forwardRef<HTMLButtonElement, ButtonProps>(
    ({ asChild = false, className, variant, size, ...props }, ref) => {
        const Component = asChild ? Slot : 'button';
        return <Component ref={ref} className={cn(buttonVariants({ variant, size }), className)} {...props} />;
    },
);

Button.displayName = 'Button';

export { buttonVariants };
