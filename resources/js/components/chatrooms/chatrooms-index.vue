<template>
    <div class="border-bottom border-gray-300 rounded-lg mb-2">
        
        <div v-for="chat in chatrooms" :key="chat.id" >
            <a :href="'/chats/' + chat.id">
                <div class="flex p-4 border-b border-b-gray-400 hover:bg-gray-200">
                    <div class="mr-2">
                        <img 
                            :src="chat.users[0].avatar"
                            alt="avatar"
                            class="rounded-full object-cover h-15 w-15 mr-2"
                        >                    
                    </div>     
                    <div class="w-full">
                        <h5 class="font-bold mb-2 ml-4"> {{ chat.users[0].name }} </h5>
                        <div v-if="chat.latest_message.sender_id != user.id" class="text-sm mb-3 mx-4 float-left">
                            {{ chat.users[0].name }}:
                        </div>
                        <div v-else class="text-sm mb-3 mx-4 float-left">
                            You:
                        </div>
                        <p class="text-sm mb-3 ml-4 break-words" v-bind:class="{ 'font-bold': !chat.latest_message.read && chat.latest_message.sender_id != user.id}">
                            {{ chat.latest_message.text }}
                        </p>
                        <p class="text-xs float-right"> {{ new Date(chat.latest_message.created_at) | dateFormat('HH:mm , DD.MM.YYYY') }} </p>
                    </div>
                </div>
                
            </a>
        </div>
    </div>

</template>

<script>
export default {
    props: ['user', 'chatrooms'],

    methods: {
        
    }

}
</script>

<style>

</style>