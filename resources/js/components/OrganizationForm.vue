<script setup lang="ts">
import OrganizationController from '@/actions/App/Http/Controllers/OrganizationController';
import { Form } from '@inertiajs/vue3';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { computed, ref } from 'vue';
import { type Organization } from '@/types';
import { toast } from 'vue-sonner';

const props = defineProps<{
    organization: Organization | null;
}>();

const isOpen = ref(!props.organization);
const previewUrl = ref<string | null>(null);

const existingLogoUrl = computed(() => {
    if (!props.organization?.logo_path) {
        return null;
    }
    return `/storage/${props.organization.logo_path}`;
});

const displayedImageUrl = computed(() => {
    return previewUrl.value || existingLogoUrl.value;
});

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        if (previewUrl.value) {
            URL.revokeObjectURL(previewUrl.value);
        }
        previewUrl.value = URL.createObjectURL(file);
    }
};

const handleSuccess = () => {
    isOpen.value = false;
    toast.success('Organization created successfully!');
};
</script>

<template>
    <div class="p-3">
        <Dialog v-model:open="isOpen" :modal="true">
            <DialogContent
                :show-close-button="false"
                @escape-key-down="(e) => e.preventDefault()"
                @pointer-down-outside="(e) => e.preventDefault()"
                @interact-outside="(e) => e.preventDefault()"
            >
                <DialogHeader>
                    <DialogTitle>Organization Details</DialogTitle>
                    <DialogDescription
                        >Enter your organizations information to get
                        started.</DialogDescription
                    >
                </DialogHeader>
                <Form
                    v-bind="OrganizationController.store.form()"
                    class="space-y-6"
                    v-slot="{ errors, processing }"
                    @success="handleSuccess"
                >
                    <div class="grid gap-2">
                        <Label for="name">Organization Name <span class="text-red-500">*</span></Label>
                        <Input
                            id="name"
                            name="name"
                            :default-value="organization?.name"
                            type="text"
                            required
                        />
                        <InputError :message="errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="logo_path">Logo</Label>
                        <Input
                            id="logo_path"
                            name="logo_path"
                            type="file"
                            accept="image/*"
                            @change="handleFileChange"
                        />
                        <InputError :message="errors.logo_path" />

                        <div v-if="displayedImageUrl" class="mt-2">
                            <img
                                :src="displayedImageUrl"
                                alt="Logo preview"
                                class="h-32 w-32 rounded-lg border object-cover"
                            />
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="primary_color">Primary Color</Label>
                        <Input
                            id="primary_color"
                            name="primary_color"
                            :default-value="organization?.primary_color"
                            type="color"
                            class="block h-10 w-14 cursor-pointer rounded-lg border border-gray-300 bg-white p-1 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none disabled:pointer-events-none disabled:opacity-50"
                        />
                        <InputError :message="errors.primary_color" />
                    </div>

                    <Button :disabled="processing">Save</Button>
                </Form>
            </DialogContent>
        </Dialog>
    </div>
</template>
