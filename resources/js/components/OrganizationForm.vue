<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
</script>

<template>
    <div class="p-3">
        <Form class="space-y-6">
            <div class="grid gap-2">
                <Label for="name">Organization Name</Label>
                <Input id="name" type="text" />
                <InputError />
            </div>

            <div class="grid gap-2">
                <Label for="logo">Logo</Label>
                <Input id="logo" type="file" />
                <InputError />
            </div>

            <div class="grid gap-2">
                <Label for="name">Primary Color</Label>
                <Input id="name" type="text" />
                <InputError />
            </div>
        </Form>
    </div>
</template>
