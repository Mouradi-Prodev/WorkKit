<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import Rooms from './Rooms.vue';
import Messages from './Messages.vue';
import { useForm } from '@inertiajs/vue3';
import { onMounted, onUnmounted, onUpdated, ref } from 'vue'


const props = defineProps(['chatRooms', 'messages', 'selectedRoomId'])
const page = usePage(['user'])
const messages = ref(props.messages)

onUpdated(() => {
    messages.value = props.messages
})
onMounted(() => {
    if (props.selectedRoomId) {


        Echo.join(`chat.${props.selectedRoomId}`)
            .here((users) => {
                console.log("All users here : " + users.map((user) => user.name));
            })
            .joining((user) => {
                console.log(user.name + " joined");
            })
            .leaving((user) => {
                console.log(user.name + " left");
            })
            .listen('MessageCreated', (e) => {
                if (e.message.user.id != page.props.auth.user.id)
                    messages.value.push(e.message);
            });
    }



})
onUnmounted(() => {
    Echo.leave(`chat.${props.selectedRoomId}`);
})



const selectRoom = (chatroom) => {
    const form = useForm({
        selected_room: chatroom.id,
    })
    router.get(route('chat.getMessages', form.selected_room))
}



</script>

<template>

    <Head :title="$page.props.auth.team.name" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl fade-in text-gray-800 dark:text-gray-200 leading-tight">Chat</h2>
        </template>

        <div class="py-1">
            <div class="flex max-w-7xl mx-auto sm:px-1 2xl:px-8">
                <!-- Room List -->
                <div class="flex flex-row h-[400px] text-semibold dark:text-gray-100">
                    <div class="shadow-lg sm:rounded-lg  bg-white dark:bg-gray-800">
                        <Rooms :selectedRoomId="selectedRoomId" :chatRooms="chatRooms" @selectRoom="selectRoom" />
                    </div>
                   
                        <div 
                         class="ml-4 shadow-lg sm:rounded-lg fade-in  bg-white dark:bg-gray-800">
                            <Messages :messages="messages" :selectedRoomId="selectedRoomId" />
                        </div>
                        <div>
                            hello
                        </div>
                   

                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>

