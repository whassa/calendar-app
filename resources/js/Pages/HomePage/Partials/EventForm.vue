
<script setup>
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import InputError from '@/Components/InputError.vue';
    import InputLabel from '@/Components/InputLabel.vue';
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import TextInput from '@/Components/TextInput.vue';
    import { Head, Link, useForm } from '@inertiajs/vue3';
    import { ref, defineProps, defineEmits } from 'vue';
    import { DatePicker } from "qalendar";
    import { useNotification } from "@kyvg/vue3-notification";

    const { formValue, formType } = defineProps(['formValue', 'formType' ]);
    const emit = defineEmits(['toggleModal', 'forceQueryFetch' ]);

    const errors = ref({});
    const { notify } = useNotification()
    const form = useForm({
        id: formValue.id,
        title: formValue.title,
        description: formValue.description,
        startingDate: formValue.startingDate,
        endingDate: formValue.endingDate,
    });;
    const loading = ref(false);
    const setLoading = (value) => {
      loading.value = value;
    };

    const setError = (value) => {
        errors.value = value;
    }

    const submit = () => {
        setLoading(true);
        fetch((formType === 'create' ? route('events.create') :
        route('events.update'))
        , {
            method: (formType === 'create' ? 'POST' : 'PATCH'),
            headers: {
            'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
            },
            body: JSON.stringify(form),
        })
        .then((res) => {
            if(res.status === 200) {
                emit('toggleModal');
                emit('forceQueryFetch');
                if (formType === 'create' ) {
                    notify({
                        text: "Event Created Successfully!",
                        type: 'success',
                    });
                } else {
                    notify({
                        text: "Event Updated Successfully!",
                        type: 'success',
                    });
                }
                
            }
            return res.json();
        })
        .then((res) => {
            if(res.errors) {
                setError(res.errors);
            }
        })
        .catch((err) => {
            setError({});
        }).finally(() => {
            setLoading(false)
        }
        );
    };

</script>

<template>
    <div @click="emit('toggleModal');" class="absolute top-0 left-0 w-full h-full z-10 opacity-70 bg-black" />
    <div class="absolute z-20 top-10 left-[calc(50%-200px)] top-[20%] w-[400px] bg-dots-darker opacity-100 bg-white dark:bg-gray-800 p-5 border-rounded" >
        <form @submit.prevent="submit">
             <div>
                <InputLabel for="title" value="Title" />

                <TextInput
                    id="title"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.title"
                    required
                    autofocus
                />
            </div>

            <div class="mt-4">
                <InputLabel for="description" value="Description" />

                <TextInput
                    id="description"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.description"
                    required
                />
            </div>

            <div class="mt-4">
                <InputLabel for="startingDate" value="Starting Date" />

                <TextInput
                    id="startingDate"
                    type="datetime-local"
                    class="mt-1 block w-full"
                    v-model="form.startingDate"
                    required
                />
            </div>

             <div class="mt-4">
                <InputLabel for="endingDate" value="Ending Date" />

                <TextInput
                    id="endingDate"
                    type="datetime-local"
                    class="mt-1 block w-full"
                    v-model="form.endingDate"
                    required
                />
            </div>
            <div v-for="(value, key) in errors" :key="key">
                <InputError v-for="(error, index) in value" class="mt-2" :key="index" :message="error" />
            </div>



            <div class="flex items-center justify-end mt-4">
                <PrimaryButton class="ms-4" :class="{ 'opacity-25': loading }" :disabled="loading">
                    <span v-if="!loading && formType === 'create'">Create Event</span>
                    <span v-else-if="!loading && formType === 'update'">Update Event</span>
                    <span v-else>Loading...</span>
                </PrimaryButton>
            </div>
        </form>
    </div>

</template>