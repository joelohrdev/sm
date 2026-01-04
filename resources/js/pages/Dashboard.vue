<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem, type Organization } from '@/types';
import { Head } from '@inertiajs/vue3';
import OrganizationForm from '@/components/OrganizationForm.vue';

defineProps<{
    organization: Organization | null;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="mx-auto max-w-4xl">
                <OrganizationForm :organization="organization" />
            </div>
        </div>
    </AppLayout>
</template>
