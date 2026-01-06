<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import season from '@/routes/season';
import { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Seasons',
        href: season.url,
    },
];
</script>

<template>
    <Head title="Seasons" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div
                        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                    >
                        <div class="bg-navy-900 p-6 text-white">
                            <div class="mb-4 flex items-center justify-between">
                                <span
                                    class="rounded-full bg-white/20 px-3 py-1 text-xs font-bold tracking-widest uppercase"
                                    >Active Season</span
                                >
                                <span
                                    class="flex items-center gap-2 text-sm text-navy-300"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="14"
                                        height="14"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-calendar"
                                        aria-hidden="true"
                                    >
                                        <path d="M8 2v4"></path>
                                        <path d="M16 2v4"></path>
                                        <rect
                                            width="18"
                                            height="18"
                                            x="3"
                                            y="4"
                                            rx="2"
                                        ></rect>
                                        <path d="M3 10h18"></path>
                                    </svg>
                                    Ends in 32 days
                                </span>
                            </div>
                            <h3
                                class="font-display text-4xl font-bold uppercase"
                            >
                                Spring 2026
                            </h3>
                            <p class="mt-2 text-navy-300">
                                Started on 8/14/2025
                            </p>
                        </div>
                        <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-3">
                            <div class="space-y-1">
                                <span
                                    class="text-xs font-bold tracking-wider text-slate-400 uppercase"
                                    >Total Teams</span
                                >
                                <div class="flex items-center gap-2">
                                    <svg
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="text-navy-400"
                                    >
                                        <path
                                            d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"
                                        ></path>
                                    </svg>
                                    <span
                                        class="text-xl font-bold text-navy-900"
                                        >3</span
                                    >
                                </div>
                            </div>
                            <div class="space-y-1">
                                <span
                                    class="text-xs font-bold tracking-wider text-slate-400 uppercase"
                                >
                                    Total Players
                                </span>
                                <div class="flex items-center gap-2">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-users text-navy-400"
                                        aria-hidden="true"
                                    >
                                        <path
                                            d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"
                                        ></path>
                                        <path
                                            d="M16 3.128a4 4 0 0 1 0 7.744"
                                        ></path>
                                        <path
                                            d="M22 21v-2a4 4 0 0 0-3-3.87"
                                        ></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                    </svg>
                                    <span
                                        class="text-xl font-bold text-navy-900"
                                        >142</span
                                    >
                                </div>
                            </div>
                            <div class="space-y-1">
                                <span
                                    class="text-xs font-bold tracking-wider text-slate-400 uppercase"
                                    >Revenue to Date</span
                                >
                                <div class="flex items-center gap-2">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-dollar-sign text-green-500"
                                        aria-hidden="true"
                                    >
                                        <line
                                            x1="12"
                                            x2="12"
                                            y1="2"
                                            y2="22"
                                        ></line>
                                        <path
                                            d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"
                                        ></path>
                                    </svg>
                                    <span
                                        class="text-xl font-bold text-navy-900"
                                        >$412,000</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex flex-col gap-4 border-t border-slate-100 bg-slate-50 p-6 sm:flex-row"
                        >
                            <button
                                class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-navy-900 px-6 py-3 font-bold text-white shadow-lg shadow-navy-900/10 transition-all hover:bg-navy-800 disabled:opacity-50"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-archive"
                                    aria-hidden="true"
                                >
                                    <rect
                                        width="20"
                                        height="5"
                                        x="2"
                                        y="3"
                                        rx="1"
                                    ></rect>
                                    <path
                                        d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"
                                    ></path>
                                    <path d="M10 12h4"></path>
                                </svg>
                                Archive Season
                            </button>
                            <button
                                class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-6 py-3 font-bold text-slate-700 transition-all hover:bg-slate-50"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-circle-plus"
                                    aria-hidden="true"
                                >
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="M8 12h8"></path>
                                    <path d="M12 8v8"></path>
                                </svg>
                                Start New Season Prep
                            </button>
                        </div>
                    </div>
                    <div
                        class="flex gap-4 rounded-xl border border-orange-200 bg-orange-50 p-6"
                    >
                        <div
                            class="shrink-0 self-start rounded-full bg-orange-100 p-3 text-orange-600"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-triangle-alert"
                                aria-hidden="true"
                            >
                                <path
                                    d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"
                                ></path>
                                <path d="M12 9v4"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-orange-900">
                                Important Note on Archiving
                            </h4>
                            <p
                                class="mt-1 text-sm leading-relaxed text-orange-800"
                            >
                                Archiving a season will lock all current rosters
                                and statistics for Fall 2025. Player profiles
                                will persist, but their historical stats will be
                                moved to the archive. Financial records will be
                                snapshot for tax and reporting purposes.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="space-y-6">
                    <div
                        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                    >
                        <div
                            class="flex items-center gap-3 border-b border-slate-100 p-6"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-history text-navy-900"
                                aria-hidden="true"
                            >
                                <path
                                    d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"
                                ></path>
                                <path d="M3 3v5h5"></path>
                                <path d="M12 7v5l4 2"></path>
                            </svg>
                            <h3
                                class="font-display text-navy-900 text-lg font-bold"
                            >
                                Past Seasons
                            </h3>
                        </div>
                        <div class="divide-y divide-slate-100">
                            <div
                                class="group cursor-pointer p-5 transition-colors hover:bg-slate-50"
                            >
                                <div
                                    class="mb-2 flex items-start justify-between"
                                >
                                    <h4
                                        class="text-navy-900 group-hover:text-baseball-red font-bold transition-colors"
                                    >
                                        Fall 2025
                                    </h4>
                                    <span
                                        class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-500 uppercase"
                                        >Archived</span
                                    >
                                </div>
                                <div
                                    class="mt-4 grid grid-cols-2 gap-x-2 gap-y-3"
                                >
                                    <div
                                        class="flex items-center gap-2 text-xs text-slate-500"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="12"
                                            height="12"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-users"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"
                                            ></path>
                                            <path
                                                d="M16 3.128a4 4 0 0 1 0 7.744"
                                            ></path>
                                            <path
                                                d="M22 21v-2a4 4 0 0 0-3-3.87"
                                            ></path>
                                            <circle
                                                cx="9"
                                                cy="7"
                                                r="4"
                                            ></circle>
                                        </svg>
                                        112 players
                                    </div>
                                    <div
                                        class="flex items-center gap-2 text-xs text-slate-500"
                                    >
                                        <svg
                                            width="12"
                                            height="12"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path
                                                d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"
                                            ></path>
                                        </svg>
                                        8 Teams
                                    </div>
                                    <div
                                        class="flex items-center gap-2 text-xs text-slate-500"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="12"
                                            height="12"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-dollar-sign"
                                            aria-hidden="true"
                                        >
                                            <line
                                                x1="12"
                                                x2="12"
                                                y1="2"
                                                y2="22"
                                            ></line>
                                            <path
                                                d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"
                                            ></path>
                                        </svg>
                                        $245,000
                                    </div>
                                    <div
                                        class="flex items-center gap-2 text-xs text-slate-500"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="12"
                                            height="12"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-trophy text-orange-400"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M10 14.66v1.626a2 2 0 0 1-.976 1.696A5 5 0 0 0 7 21.978"
                                            ></path>
                                            <path
                                                d="M14 14.66v1.626a2 2 0 0 0 .976 1.696A5 5 0 0 1 17 21.978"
                                            ></path>
                                            <path
                                                d="M18 9h1.5a1 1 0 0 0 0-5H18"
                                            ></path>
                                            <path d="M4 22h16"></path>
                                            <path
                                                d="M6 9a6 6 0 0 0 12 0V3a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1z"
                                            ></path>
                                            <path
                                                d="M6 9H4.5a1 1 0 0 1 0-5H6"
                                            ></path>
                                        </svg>
                                        Top Squad
                                    </div>
                                </div>
                                <div
                                    class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4 opacity-0 transition-opacity group-hover:opacity-100"
                                >
                                    <button
                                        class="text-navy-600 flex items-center gap-1 text-xs font-bold"
                                    >
                                        View Stats
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="12"
                                            height="12"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-arrow-right"
                                            aria-hidden="true"
                                        >
                                            <path d="M5 12h14"></path>
                                            <path d="m12 5 7 7-7 7"></path>
                                        </svg>
                                    </button>
                                    <button
                                        class="hover:text-navy-900 rounded border border-slate-200 bg-white p-1.5 text-slate-400"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="14"
                                            height="14"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-download"
                                            aria-hidden="true"
                                        >
                                            <path d="M12 15V3"></path>
                                            <path
                                                d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"
                                            ></path>
                                            <path d="m7 10 5 5 5-5"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped></style>
