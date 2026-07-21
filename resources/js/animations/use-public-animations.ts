import { useGSAP } from '@gsap/react';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import type { RefObject } from 'react';

gsap.registerPlugin(useGSAP, ScrollTrigger);

export function usePublicAnimations(scope: RefObject<HTMLElement | null>, pageKey: string) {
    useGSAP(() => {
        const media = gsap.matchMedia();
        media.add('(prefers-reduced-motion: no-preference)', () => {
            const heroLines = gsap.utils.toArray<HTMLElement>('[data-hero-line]');
            const intro = gsap.timeline({ defaults: { ease: 'power3.out' } });
            intro.fromTo('[data-hero-kicker]', { y: 18, opacity: 0 }, { y: 0, opacity: 1, duration: 0.45 })
                .fromTo(heroLines, { yPercent: 105, rotate: 1.5 }, { yPercent: 0, rotate: 0, duration: 0.85, stagger: 0.1 }, '-=.18')
                .fromTo('[data-hero-copy], [data-hero-actions]', { y: 25, opacity: 0 }, { y: 0, opacity: 1, duration: 0.55, stagger: 0.1 }, '-=.4')
                .fromTo('[data-hero-visual]', { clipPath: 'inset(0 100% 0 0)' }, { clipPath: 'inset(0 0% 0 0)', duration: 0.9 }, '-=.75');

            gsap.to('[data-hero-image]', { yPercent: 8, ease: 'none', scrollTrigger: { trigger: '[data-hero-visual]', start: 'top top+=64', end: 'bottom top', scrub: 0.8 } });

            gsap.utils.toArray<HTMLElement>('[data-reveal]').forEach((element) => {
                gsap.fromTo(element, { y: 42, opacity: 0 }, { y: 0, opacity: 1, duration: 0.72, ease: 'power3.out', scrollTrigger: { trigger: element, start: 'top 88%', once: true } });
            });
            ScrollTrigger.batch('[data-project-card], [data-list-item]', { start: 'top 90%', once: true, onEnter: (items) => gsap.fromTo(items, { y: 45, opacity: 0 }, { y: 0, opacity: 1, stagger: 0.12, duration: 0.65, ease: 'power3.out' }) });
        });
        return () => media.revert();
    }, { scope, dependencies: [pageKey], revertOnUpdate: true });
}

export function PublicGsapAnimations({ scope, pageKey }: { scope: RefObject<HTMLElement | null>; pageKey: string }) {
    usePublicAnimations(scope, pageKey);
    return null;
}
