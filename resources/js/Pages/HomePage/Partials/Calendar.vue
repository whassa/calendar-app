<script setup>
    import PrimaryButton from '@/Components/PrimaryButton.vue';
    import { useForm } from '@inertiajs/vue3';
    import { ref, onMounted, watch } from 'vue';
    import { useQuery, useQueryClient } from "@tanstack/vue-query";
    import { Qalendar } from "qalendar";
    import { useNotification } from "@kyvg/vue3-notification";

    import EventForm from './EventForm.vue';
    const { notify } = useNotification()

    const showModal = ref(false);
    const eventsData = ref([]);
    const events = ref([]);

    const defaultForm = ref({
        id: '',
        title: '',
        description: '',
        startingDate: '',
        endingDate: '',
    });
    const formType = ref('create');

    const config = ref({
        defaultMode: 'month',
        locale: 'en',
    })

    const createForm = () => {
        defaultForm.value = {
            id: '',
            title: '',
            description: '',
            startingDate: '',
            endingDate: '',
        };
        formType.value = 'create';
        toggleModal();
    }

    const toggleModal = () => {
      showModal.value = !showModal.value;
    };

    const { isFetchedAfterMount, isSuccess, data, error } = useQuery({
        queryKey: ['events'],
        queryFn: async () => {
            const response = await fetch('/events')
            if (!response.ok) {
                throw new Error('Network response was not ok')
            }
            return response.json()
        },
        initialData: [],
    })

    const queryClient = useQueryClient();

    const forceQueryFetch = () => {
        queryClient.invalidateQueries({
            queryKey: ['events'],
            refetchType: 'all',
        });
    }

    watch(data, async() => {
        if( isFetchedAfterMount && isSuccess) {
            let val = JSON.parse(JSON.stringify(data.value));
            events.value = val;
        
        } 
    });


    const editEvent = (e) => {
        fetch(route('events.get', e), {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
            },
        }).then((res) => {
            if (res.status !== 200) {
                throw Error('Error fetching')
            }
            return res.json();
        }).then((res) => {
            defaultForm.value = res;
            formType.value = 'update'
            toggleModal();
        }).catch((err) => {
            notify({
                text: "Error getting the event to edit",
                type: 'error',
            });
        })
    }

    const deleteEvent = (e) => {
         fetch(route('events.delete'), {
            method: 'DELETE',
            headers: {
            'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
            },
            body: JSON.stringify({
                id: e
            }),
        }).then((res) => {
            if(res.status === 200) {
                notify({
                    text: "Event Deleted Successfully!",
                    type: 'success',
                });
                forceQueryFetch();
            }else  {
                notify({
                    text: "Error with Deleting the event",
                    type: 'error',
                });
            }
        })
        .catch((err) => {
            notify({
                text: "Error with Deleting the event",
                type: 'error',
            });
        });
    }
    

</script>
<template>
    <div class="flex-col">
      <div class="w-full h-10 justify-center">
        <PrimaryButton @click="createForm" >Add an event to the calendar</PrimaryButton>
      </div>
      <EventForm v-if="showModal" :formType="formType" :formValue="defaultForm" @toggleModal="toggleModal" @forceQueryFetch="forceQueryFetch"/>

      <div class="h-96 md:h-[40rem] w-full mt-2">
        <Qalendar 
            :events="events"
            :config="config"
            @edit-event="(e) => editEvent(e)"
            @delete-event="(e) => deleteEvent(e)"
        />
      </div>
    </div>
</template>

<style>
    @import "qalendar/dist/style.css";
</style>