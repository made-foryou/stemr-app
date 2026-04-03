<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.home-head')
    </head>
    <body class="min-h-screen overflow-x-hidden antialiased"
          style="background-color: var(--color-vota-bg); color: var(--color-vota-text);"
          x-data x-init="
              const observer = new IntersectionObserver((entries) => {
                  entries.forEach(entry => {
                      if (entry.isIntersecting) {
                          entry.target.classList.add('revealed');
                          observer.unobserve(entry.target);
                      }
                  });
              }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
              document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
          ">

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- STICKY NAVIGATION                                          --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <nav x-data="{ scrolled: false }"
             @scroll.window="scrolled = window.scrollY > 60"
             :class="scrolled ? 'bg-[var(--color-vota-bg)]/90 backdrop-blur-lg shadow-sm shadow-[var(--color-vota-primary)]/5' : 'bg-transparent'"
             class="fixed inset-x-0 top-0 z-50 transition-all duration-500">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background-color: var(--color-vota-primary);">
                        <x-app-logo-icon class="h-4 w-4 fill-current text-white" />
                    </span>
                    <span class="text-lg font-semibold tracking-tight">{{ config('app.name', 'Vota') }}</span>
                </a>

                <div class="flex items-center gap-6">
                    <a href="#hoe-het-werkt" class="hidden text-sm font-medium opacity-60 transition-opacity hover:opacity-100 sm:inline">Hoe het werkt</a>
                    <a href="#kenmerken" class="hidden text-sm font-medium opacity-60 transition-opacity hover:opacity-100 sm:inline">Kenmerken</a>
                    <a href="{{ route('login') }}" class="text-sm font-medium opacity-60 transition-opacity hover:opacity-100">Inloggen</a>
                    <a href="{{ route('register') }}"
                       class="rounded-xl px-5 py-2.5 text-sm font-semibold text-white transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]"
                       style="background-color: var(--color-vota-primary);">
                        Gratis starten
                    </a>
                </div>
            </div>
        </nav>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- HERO SECTION                                               --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <section class="relative min-h-[100dvh] overflow-hidden pt-24 lg:pt-0">
            {{-- Background decorations --}}
            <div class="absolute inset-0">
                <div class="absolute -right-32 -top-32 h-[500px] w-[500px] rounded-full blur-[100px]" style="background-color: rgba(107, 53, 104, 0.08);"></div>
                <div class="absolute -bottom-48 -left-48 h-[600px] w-[600px] rounded-full blur-[120px]" style="background-color: rgba(196, 112, 63, 0.06);"></div>
                <div class="absolute right-1/4 top-1/3 h-72 w-72 rounded-full blur-[80px]" style="background-color: rgba(245, 232, 244, 0.5);"></div>
                {{-- Dot pattern --}}
                <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle at 1px 1px, var(--color-vota-primary) 1px, transparent 0); background-size: 40px 40px;"></div>
            </div>

            {{-- Floating geometric accents --}}
            <div class="absolute left-[8%] top-[20%] vota-drift" style="animation-delay: 0s;">
                <div class="h-10 w-10 rotate-45 rounded-xl border-2 opacity-20" style="border-color: var(--color-vota-primary); background-color: rgba(107, 53, 104, 0.04);"></div>
            </div>
            <div class="absolute right-[12%] top-[15%] vota-drift" style="animation-delay: 3s;">
                <div class="h-6 w-6 rounded-full border-2 opacity-15" style="border-color: var(--color-vota-accent); background-color: rgba(196, 112, 63, 0.04);"></div>
            </div>
            <div class="absolute bottom-[25%] left-[15%] vota-drift" style="animation-delay: 6s;">
                <div class="h-8 w-8 rotate-12 rounded-lg border-2 opacity-15" style="border-color: rgba(139, 78, 136, 0.4); background-color: rgba(139, 78, 136, 0.03);"></div>
            </div>

            <div class="relative z-10 mx-auto grid max-w-6xl items-center gap-12 px-6 lg:min-h-[100dvh] lg:grid-cols-[1.15fr_1fr] lg:gap-20">
                {{-- Text content --}}
                <div class="pt-12 lg:py-24">
                    <div class="animate-fade-in">
                        <div class="mb-6 inline-flex items-center gap-2 rounded-full border px-4 py-1.5 text-xs font-medium tracking-wide" style="border-color: rgba(107, 53, 104, 0.15); background-color: rgba(107, 53, 104, 0.04); color: var(--color-vota-primary);">
                            <span class="relative flex h-2 w-2">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full opacity-75" style="background-color: var(--color-vota-accent);"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full" style="background-color: var(--color-vota-accent);"></span>
                            </span>
                            Gratis voor iedereen
                        </div>

                        <h1 class="text-4xl font-bold leading-[1.1] tracking-tight sm:text-5xl lg:text-6xl">
                            Samen kiezen,<br>
                            <span style="color: var(--color-vota-primary);">altijd raak.</span>
                        </h1>

                        <p class="mt-6 max-w-lg text-lg font-light leading-relaxed opacity-70 lg:text-xl">
                            Organiseer je een weekendje weg? Zoek je het perfecte restaurant? Maak een poll, nodig je groep uit, en ontdek samen de favoriet.
                        </p>
                    </div>

                    <div class="mt-10 flex flex-wrap items-center gap-4 animate-slide-up" style="animation-delay: 0.2s;">
                        <a href="{{ route('register') }}"
                           class="group relative inline-flex items-center gap-2 overflow-hidden rounded-2xl px-8 py-4 text-base font-semibold text-white shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-[1.02] active:scale-[0.98]"
                           style="background-color: var(--color-vota-primary); box-shadow: 0 8px 30px rgba(107, 53, 104, 0.25);">
                            <span class="relative z-10">Maak een gratis poll</span>
                            <svg class="relative z-10 h-5 w-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                            <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/10 to-transparent transition-transform duration-700 group-hover:translate-x-full"></div>
                        </a>

                        {{-- TODO: Link naar echte read-only demo-poll wanneer beschikbaar --}}
                        <a href="#hoe-het-werkt"
                           class="inline-flex items-center gap-2 rounded-2xl border-2 px-8 py-4 text-base font-semibold transition-all duration-300 hover:scale-[1.02] active:scale-[0.98]"
                           style="border-color: rgba(196, 112, 63, 0.3); color: var(--color-vota-accent);">
                            Bekijk een voorbeeld
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3" />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Orbital graphic --}}
                <div class="relative mx-auto aspect-square w-full max-w-md animate-fade-in lg:max-w-lg" style="animation-delay: 0.3s;">
                    {{-- Ambient glow --}}
                    <div class="absolute left-1/2 top-1/2 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full blur-[80px] vota-pulse" style="background-color: rgba(107, 53, 104, 0.1);"></div>

                    {{-- Orbital rings --}}
                    <div class="absolute inset-4 rounded-full border border-dashed opacity-10" style="border-color: var(--color-vota-primary);"></div>
                    <div class="absolute inset-16 rounded-full border opacity-[0.07]" style="border-color: var(--color-vota-primary);"></div>
                    <div class="absolute inset-28 rounded-full border border-dashed opacity-[0.05]" style="border-color: var(--color-vota-accent);"></div>

                    {{-- Center piece — checkmark --}}
                    <div class="absolute left-1/2 top-1/2 flex h-20 w-20 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-2xl shadow-xl vota-breathe" style="background-color: var(--color-vota-primary); box-shadow: 0 15px 40px rgba(107, 53, 104, 0.3);">
                        <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>

                    {{-- Orbiting option cards --}}
                    <div class="absolute inset-0 vota-orbit" style="animation-duration: 28s;">
                        <div class="absolute left-1/2 top-0 -translate-x-1/2 rounded-xl border bg-white/90 px-3 py-2 shadow-md backdrop-blur-sm" style="border-color: rgba(107, 53, 104, 0.1);">
                            <span class="text-lg">🏖️</span>
                            <span class="ml-1.5 text-xs font-medium opacity-70">Weekendje weg</span>
                        </div>
                    </div>

                    <div class="absolute inset-0 vota-orbit" style="animation-duration: 32s; animation-direction: reverse;">
                        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 rounded-xl border bg-white/90 px-3 py-2 shadow-md backdrop-blur-sm" style="border-color: rgba(196, 112, 63, 0.1);">
                            <span class="text-lg">🍝</span>
                            <span class="ml-1.5 text-xs font-medium opacity-70">Restaurant</span>
                        </div>
                    </div>

                    <div class="absolute inset-0 vota-orbit" style="animation-duration: 24s;">
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 rounded-xl border bg-white/90 px-3 py-2 shadow-md backdrop-blur-sm" style="border-color: rgba(139, 78, 136, 0.1);">
                            <span class="text-lg">🎁</span>
                            <span class="ml-1.5 text-xs font-medium opacity-70">Cadeau</span>
                        </div>
                    </div>

                    {{-- Orbiting dots --}}
                    <div class="absolute inset-0 vota-orbit" style="animation-duration: 20s; animation-direction: reverse;">
                        <div class="absolute right-0 top-1/2 h-3 w-3 -translate-y-1/2 rounded-full shadow-md vota-breathe" style="background-color: var(--color-vota-accent); box-shadow: 0 4px 12px rgba(196, 112, 63, 0.3); animation-delay: 0.5s;"></div>
                    </div>
                    <div class="absolute inset-0 vota-orbit" style="animation-duration: 35s;">
                        <div class="absolute left-1/4 top-0 h-2.5 w-2.5 rounded-full shadow-md vota-breathe" style="background-color: var(--color-vota-primary); box-shadow: 0 4px 12px rgba(107, 53, 104, 0.3); animation-delay: 1.2s;"></div>
                    </div>

                    {{-- Floating accents --}}
                    <div class="absolute right-6 top-12 vota-float" style="animation-delay: 0.3s;">
                        <div class="h-7 w-7 rotate-45 rounded-lg border-2" style="border-color: rgba(107, 53, 104, 0.2); background-color: rgba(107, 53, 104, 0.06);"></div>
                    </div>
                    <div class="absolute bottom-14 left-8 vota-float" style="animation-delay: 1.5s;">
                        <div class="h-5 w-5 rounded-full border-2" style="border-color: rgba(196, 112, 63, 0.2); background-color: rgba(196, 112, 63, 0.05);"></div>
                    </div>
                </div>
            </div>

            {{-- Wave divider --}}
            <div class="absolute inset-x-0 -bottom-1">
                <svg viewBox="0 0 1440 100" class="block w-full" preserveAspectRatio="none" style="fill: var(--color-vota-primary-light);">
                    <path d="M0,60 C240,100 480,20 720,50 C960,80 1200,25 1440,60 L1440,100 L0,100 Z" />
                </svg>
            </div>
        </section>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- HOE HET WERKT                                              --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <section id="hoe-het-werkt" class="relative overflow-hidden py-24 lg:py-32" style="background-color: var(--color-vota-primary-light);">
            <div class="mx-auto max-w-5xl px-6">
                <div class="reveal text-center">
                    <span class="inline-block rounded-full px-4 py-1.5 text-xs font-semibold uppercase tracking-widest" style="background-color: rgba(107, 53, 104, 0.08); color: var(--color-vota-primary);">
                        Simpel als 1-2-3
                    </span>
                    <h2 class="mt-5 text-3xl font-bold tracking-tight sm:text-4xl">
                        Hoe het werkt
                    </h2>
                </div>

                <div class="relative mt-16 grid gap-8 sm:grid-cols-3 sm:gap-6 lg:gap-12">
                    {{-- Connecting line (desktop) --}}
                    <div class="absolute left-0 right-0 top-12 hidden h-px sm:block" style="background: linear-gradient(to right, transparent, var(--color-vota-primary), var(--color-vota-accent), var(--color-vota-primary), transparent); opacity: 0.15;"></div>

                    {{-- Step 1 --}}
                    <div class="reveal reveal-delay-1 group relative text-center">
                        <div class="relative mx-auto flex h-24 w-24 items-center justify-center">
                            <div class="absolute inset-0 rounded-2xl transition-transform duration-300 group-hover:scale-110" style="background-color: rgba(107, 53, 104, 0.06);"></div>
                            <div class="relative flex h-14 w-14 items-center justify-center rounded-xl text-white shadow-lg" style="background-color: var(--color-vota-primary); box-shadow: 0 8px 25px rgba(107, 53, 104, 0.2);">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2 inline-flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold text-white" style="background-color: var(--color-vota-primary);">1</div>
                        <h3 class="mt-3 text-lg font-semibold tracking-tight">Maak een poll</h3>
                        <p class="mt-2 text-sm font-light leading-relaxed opacity-60">Voeg opties toe met foto's, prijzen, voor- en nadelen.</p>
                    </div>

                    {{-- Step 2 --}}
                    <div class="reveal reveal-delay-2 group relative text-center">
                        <div class="relative mx-auto flex h-24 w-24 items-center justify-center">
                            <div class="absolute inset-0 rounded-2xl transition-transform duration-300 group-hover:scale-110" style="background-color: rgba(196, 112, 63, 0.06);"></div>
                            <div class="relative flex h-14 w-14 items-center justify-center rounded-xl text-white shadow-lg" style="background-color: var(--color-vota-accent); box-shadow: 0 8px 25px rgba(196, 112, 63, 0.2);">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2 inline-flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold text-white" style="background-color: var(--color-vota-accent);">2</div>
                        <h3 class="mt-3 text-lg font-semibold tracking-tight">Nodig je groep uit</h3>
                        <p class="mt-2 text-sm font-light leading-relaxed opacity-60">Deel via e-mail. Geen account nodig om te stemmen.</p>
                    </div>

                    {{-- Step 3 --}}
                    <div class="reveal reveal-delay-3 group relative text-center">
                        <div class="relative mx-auto flex h-24 w-24 items-center justify-center">
                            <div class="absolute inset-0 rounded-2xl transition-transform duration-300 group-hover:scale-110" style="background-color: rgba(107, 53, 104, 0.06);"></div>
                            <div class="relative flex h-14 w-14 items-center justify-center rounded-xl text-white shadow-lg" style="background: linear-gradient(135deg, var(--color-vota-primary), var(--color-vota-accent)); box-shadow: 0 8px 25px rgba(107, 53, 104, 0.2);">
                                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2 inline-flex h-7 w-7 items-center justify-center rounded-full text-xs font-bold text-white" style="background: linear-gradient(135deg, var(--color-vota-primary), var(--color-vota-accent));">3</div>
                        <h3 class="mt-3 text-lg font-semibold tracking-tight">Ontdek de favoriet</h3>
                        <p class="mt-2 text-sm font-light leading-relaxed opacity-60">Bekijk live resultaten terwijl iedereen stemt.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- KENMERKEN / VOORDELEN                                      --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <section id="kenmerken" class="relative py-24 lg:py-32">
            {{-- Background accent --}}
            <div class="absolute left-0 top-0 h-96 w-96 rounded-full blur-[120px] opacity-[0.04]" style="background-color: var(--color-vota-accent);"></div>

            <div class="mx-auto max-w-6xl px-6">
                <div class="reveal text-center">
                    <span class="inline-block rounded-full px-4 py-1.5 text-xs font-semibold uppercase tracking-widest" style="background-color: rgba(196, 112, 63, 0.08); color: var(--color-vota-accent);">
                        Alles wat je nodig hebt
                    </span>
                    <h2 class="mt-5 text-3xl font-bold tracking-tight sm:text-4xl">
                        Krachtig én simpel
                    </h2>
                </div>

                {{-- Asymmetric feature grid --}}
                <div class="mt-16 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    {{-- Feature 1 — Gratis (tall card) --}}
                    <div class="reveal reveal-delay-1 group rounded-3xl border p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl sm:row-span-2" style="border-color: rgba(107, 53, 104, 0.08); background: linear-gradient(145deg, white, var(--color-vota-primary-light));">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl" style="background-color: rgba(107, 53, 104, 0.08);">
                            <svg class="h-7 w-7" style="color: var(--color-vota-primary);" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                        </div>
                        <h3 class="mt-6 text-xl font-bold tracking-tight">Helemaal gratis</h3>
                        <p class="mt-3 font-light leading-relaxed opacity-60">Geen verborgen kosten, geen premium tier. Maak onbeperkt polls aan en nodig zoveel mensen uit als je wilt.</p>
                        <div class="mt-8 flex items-center gap-3">
                            <div class="flex -space-x-2">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-white text-xs font-bold text-white" style="background-color: var(--color-vota-primary);">A</div>
                                <div class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-white text-xs font-bold text-white" style="background-color: var(--color-vota-accent);">B</div>
                                <div class="flex h-8 w-8 items-center justify-center rounded-full border-2 border-white text-xs font-bold text-white" style="background-color: #8B4E88;">C</div>
                            </div>
                            <span class="text-xs font-medium opacity-50">Onbeperkt deelnemers</span>
                        </div>
                    </div>

                    {{-- Feature 2 — Geen app nodig --}}
                    <div class="reveal reveal-delay-2 group rounded-3xl border p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl" style="border-color: rgba(196, 112, 63, 0.08); background-color: white;">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl" style="background-color: rgba(196, 112, 63, 0.08);">
                            <svg class="h-7 w-7" style="color: var(--color-vota-accent);" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                            </svg>
                        </div>
                        <h3 class="mt-6 text-lg font-bold tracking-tight">Geen app nodig</h3>
                        <p class="mt-2 font-light leading-relaxed opacity-60">Stem via een link. Werkt in elke browser, op elk apparaat.</p>
                    </div>

                    {{-- Feature 3 — Rijke opties --}}
                    <div class="reveal reveal-delay-3 group rounded-3xl border p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl" style="border-color: rgba(107, 53, 104, 0.08); background-color: white;">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl" style="background-color: rgba(107, 53, 104, 0.08);">
                            <svg class="h-7 w-7" style="color: var(--color-vota-primary);" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z" />
                            </svg>
                        </div>
                        <h3 class="mt-6 text-lg font-bold tracking-tight">Rijke opties</h3>
                        <p class="mt-2 font-light leading-relaxed opacity-60">Voeg afbeeldingen, prijzen, afstanden, voor- en nadelen toe aan elke optie.</p>
                    </div>

                    {{-- Feature 4 — URL import --}}
                    <div class="reveal reveal-delay-3 group rounded-3xl border p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl" style="border-color: rgba(196, 112, 63, 0.08); background-color: white;">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl" style="background-color: rgba(196, 112, 63, 0.08);">
                            <svg class="h-7 w-7" style="color: var(--color-vota-accent);" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 003 12c0-1.605.42-3.113 1.157-4.418" />
                            </svg>
                        </div>
                        <h3 class="mt-6 text-lg font-bold tracking-tight">URL import</h3>
                        <p class="mt-2 font-light leading-relaxed opacity-60">Plak een link en Vota haalt automatisch de details op.</p>
                    </div>

                    {{-- Feature 5 — Live resultaten (wide card) --}}
                    <div class="reveal reveal-delay-4 group rounded-3xl border p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl sm:col-span-2 lg:col-span-1" style="border-color: rgba(107, 53, 104, 0.08); background: linear-gradient(145deg, white, rgba(196, 112, 63, 0.04));">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl" style="background: linear-gradient(135deg, rgba(107, 53, 104, 0.08), rgba(196, 112, 63, 0.08));">
                            <svg class="h-7 w-7" style="color: var(--color-vota-primary);" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />
                            </svg>
                        </div>
                        <h3 class="mt-6 text-lg font-bold tracking-tight">Live resultaten</h3>
                        <p class="mt-2 font-light leading-relaxed opacity-60">Bekijk in real-time hoe de stemmen binnenkomen. Spannend tot de laatste stem.</p>
                        {{-- Mini live bar chart visualization --}}
                        <div class="mt-6 space-y-2">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-medium opacity-50 w-20 truncate">Optie A</span>
                                <div class="h-3 flex-1 overflow-hidden rounded-full" style="background-color: rgba(107, 53, 104, 0.08);">
                                    <div class="h-full rounded-full animate-[grow_2s_ease-out_0.5s_both]" style="width: 72%; background-color: var(--color-vota-primary);"></div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-medium opacity-50 w-20 truncate">Optie B</span>
                                <div class="h-3 flex-1 overflow-hidden rounded-full" style="background-color: rgba(196, 112, 63, 0.08);">
                                    <div class="h-full rounded-full animate-[grow_2s_ease-out_0.7s_both]" style="width: 53%; background-color: var(--color-vota-accent);"></div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-medium opacity-50 w-20 truncate">Optie C</span>
                                <div class="h-3 flex-1 overflow-hidden rounded-full" style="background-color: rgba(139, 78, 136, 0.08);">
                                    <div class="h-full rounded-full animate-[grow_2s_ease-out_0.9s_both]" style="width: 38%; background-color: #8B4E88;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- USE CASES                                                  --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <section class="relative overflow-hidden py-24 lg:py-32" style="background-color: var(--color-vota-primary-dark);">
            {{-- Background pattern --}}
            <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 32px 32px;"></div>
            <div class="absolute -left-32 top-1/4 h-80 w-80 rounded-full blur-[100px]" style="background-color: rgba(107, 53, 104, 0.2);"></div>
            <div class="absolute -right-32 bottom-1/4 h-64 w-64 rounded-full blur-[80px]" style="background-color: rgba(196, 112, 63, 0.1);"></div>

            <div class="relative z-10 mx-auto max-w-5xl px-6">
                <div class="reveal text-center">
                    <span class="inline-block rounded-full border px-4 py-1.5 text-xs font-semibold uppercase tracking-widest" style="border-color: rgba(255, 255, 255, 0.1); color: rgba(255, 255, 255, 0.5);">
                        Voor elke gelegenheid
                    </span>
                    <h2 class="mt-5 text-3xl font-bold tracking-tight text-white sm:text-4xl">
                        Waarvoor gebruik je Vota?
                    </h2>
                </div>

                <div class="mt-16 grid grid-cols-2 gap-5 sm:gap-6 lg:grid-cols-4">
                    {{-- Use case 1 --}}
                    <div class="reveal reveal-delay-1 use-case-card group cursor-default rounded-3xl border p-6 text-center lg:-rotate-2 lg:translate-y-4" style="border-color: rgba(255, 255, 255, 0.06); background-color: rgba(255, 255, 255, 0.04); backdrop-filter: blur(8px);">
                        <div class="text-4xl transition-transform duration-300 group-hover:scale-110">🏖️</div>
                        <h3 class="mt-4 text-sm font-semibold tracking-tight text-white">Weekendje weg</h3>
                        <p class="mt-1 text-xs font-light text-white/40">Bestemming kiezen</p>
                    </div>

                    {{-- Use case 2 --}}
                    <div class="reveal reveal-delay-2 use-case-card group cursor-default rounded-3xl border p-6 text-center lg:rotate-1 lg:-translate-y-2" style="border-color: rgba(255, 255, 255, 0.06); background-color: rgba(255, 255, 255, 0.04); backdrop-filter: blur(8px);">
                        <div class="text-4xl transition-transform duration-300 group-hover:scale-110">🎳</div>
                        <h3 class="mt-4 text-sm font-semibold tracking-tight text-white">Teamuitje</h3>
                        <p class="mt-1 text-xs font-light text-white/40">Activiteit stemmen</p>
                    </div>

                    {{-- Use case 3 --}}
                    <div class="reveal reveal-delay-3 use-case-card group cursor-default rounded-3xl border p-6 text-center lg:-rotate-1 lg:translate-y-6" style="border-color: rgba(255, 255, 255, 0.06); background-color: rgba(255, 255, 255, 0.04); backdrop-filter: blur(8px);">
                        <div class="text-4xl transition-transform duration-300 group-hover:scale-110">🍝</div>
                        <h3 class="mt-4 text-sm font-semibold tracking-tight text-white">Restaurant</h3>
                        <p class="mt-1 text-xs font-light text-white/40">Waar gaan we eten?</p>
                    </div>

                    {{-- Use case 4 --}}
                    <div class="reveal reveal-delay-4 use-case-card group cursor-default rounded-3xl border p-6 text-center lg:rotate-2 lg:-translate-y-3" style="border-color: rgba(255, 255, 255, 0.06); background-color: rgba(255, 255, 255, 0.04); backdrop-filter: blur(8px);">
                        <div class="text-4xl transition-transform duration-300 group-hover:scale-110">🎁</div>
                        <h3 class="mt-4 text-sm font-semibold tracking-tight text-white">Cadeau</h3>
                        <p class="mt-1 text-xs font-light text-white/40">Samen uitkiezen</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- SOCIAL PROOF (PLACEHOLDER)                                 --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <section class="relative py-20 lg:py-24">
            <div class="mx-auto max-w-3xl px-6 text-center">
                <div class="reveal">
                    {{-- TODO: Dynamisch maken met Poll::count() wanneer beschikbaar --}}
                    <div class="inline-flex items-center gap-4 rounded-2xl border px-8 py-5" style="border-color: rgba(107, 53, 104, 0.08); background-color: rgba(107, 53, 104, 0.02);">
                        <div class="flex -space-x-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 text-sm font-bold text-white" style="border-color: var(--color-vota-bg); background-color: var(--color-vota-primary);">M</div>
                            <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 text-sm font-bold text-white" style="border-color: var(--color-vota-bg); background-color: var(--color-vota-accent);">J</div>
                            <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 text-sm font-bold text-white" style="border-color: var(--color-vota-bg); background-color: #8B4E88;">S</div>
                            <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 text-xs font-bold" style="border-color: var(--color-vota-bg); background-color: var(--color-vota-primary-light); color: var(--color-vota-primary);">+</div>
                        </div>
                        <div class="text-left">
                            <div class="text-lg font-bold tracking-tight">Word een van de eersten</div>
                            <div class="text-sm font-light opacity-50">Vota is net gelanceerd — begin vandaag nog gratis.</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- TWEEDE CTA-BLOK                                           --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <section class="relative overflow-hidden py-24 lg:py-32" style="background: linear-gradient(135deg, var(--color-vota-primary-dark), var(--color-vota-primary));">
            {{-- Decorative orbitals --}}
            <div class="absolute left-1/4 top-1/2 -translate-x-1/2 -translate-y-1/2">
                <div class="h-64 w-64 rounded-full border border-white/[0.05] vota-orbit" style="animation-duration: 30s;"></div>
            </div>
            <div class="absolute right-1/4 top-1/2 -translate-x-1/2 -translate-y-1/2">
                <div class="h-48 w-48 rounded-full border border-dashed border-white/[0.04] vota-orbit" style="animation-duration: 22s; animation-direction: reverse;"></div>
            </div>
            <div class="absolute left-[10%] top-[20%] vota-float">
                <div class="h-4 w-4 rotate-45 rounded-md border border-white/10 bg-white/[0.03]"></div>
            </div>
            <div class="absolute bottom-[30%] right-[15%] vota-float" style="animation-delay: 2s;">
                <div class="h-3 w-3 rounded-full border border-white/10 bg-white/[0.03]"></div>
            </div>

            <div class="relative z-10 mx-auto max-w-3xl px-6 text-center">
                <div class="reveal">
                    <h2 class="text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">
                        Klaar om samen<br>te kiezen?
                    </h2>
                    <p class="mx-auto mt-5 max-w-md text-lg font-light text-white/60">
                        Maak je eerste poll in minder dan een minuut. Helemaal gratis.
                    </p>
                    <div class="mt-10">
                        <a href="{{ route('register') }}"
                           class="group relative inline-flex items-center gap-2 overflow-hidden rounded-2xl px-10 py-5 text-lg font-semibold transition-all duration-300 hover:scale-[1.03] active:scale-[0.98]"
                           style="background-color: white; color: var(--color-vota-primary); box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);">
                            <span class="relative z-10">Maak nu je eerste poll</span>
                            <svg class="relative z-10 h-5 w-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                            <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-[var(--color-vota-primary-light)] to-transparent transition-transform duration-700 group-hover:translate-x-full"></div>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- FOOTER                                                     --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <footer style="background-color: var(--color-vota-text);">
            <div class="mx-auto max-w-6xl px-6 py-16">
                <div class="grid gap-12 sm:grid-cols-2 lg:grid-cols-3">
                    {{-- Logo + description --}}
                    <div>
                        <a href="{{ route('home') }}" class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background-color: var(--color-vota-primary);">
                                <x-app-logo-icon class="h-4 w-4 fill-current text-white" />
                            </span>
                            <span class="text-lg font-semibold tracking-tight text-white">{{ config('app.name', 'Vota') }}</span>
                        </a>
                        <p class="mt-4 max-w-xs text-sm font-light leading-relaxed text-white/40">
                            Samen de beste keuze maken. Snel, simpel en gratis.
                        </p>
                    </div>

                    {{-- Links --}}
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-widest text-white/30">Navigatie</h4>
                        <ul class="mt-4 space-y-3">
                            <li><a href="{{ route('login') }}" class="text-sm text-white/60 transition-colors hover:text-white">Inloggen</a></li>
                            <li><a href="{{ route('register') }}" class="text-sm text-white/60 transition-colors hover:text-white">Registreren</a></li>
                            {{-- TODO: Privacy en voorwaarden routes toevoegen --}}
                            <li><a href="#" class="text-sm text-white/60 transition-colors hover:text-white">Privacy</a></li>
                            <li><a href="#" class="text-sm text-white/60 transition-colors hover:text-white">Voorwaarden</a></li>
                        </ul>
                    </div>

                    {{-- Made by --}}
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-widest text-white/30">Ontwikkeld door</h4>
                        <a href="https://made-foryou.nl" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex items-center gap-2 text-sm text-white/60 transition-colors hover:text-white">
                            <span>Gemaakt door</span>
                            <span class="font-semibold text-white">Made</span>
                            <svg class="h-3.5 w-3.5 opacity-40" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="mt-16 border-t pt-8" style="border-color: rgba(255, 255, 255, 0.06);">
                    <p class="text-center text-xs text-white/30">
                        &copy; {{ date('Y') }} {{ config('app.name', 'Vota') }}. Alle rechten voorbehouden.
                    </p>
                </div>
            </div>
        </footer>

    </body>
</html>
