<template>
    <div class="border-bottom border-gray-300 rounded-lg mb-2">
        
        <div v-for="chat in chats" :key="chat.user_id" >
            <a :href="'/chats/' + chat.id">
                <div class="flex p-4 border-b border-b-gray-400 hover:bg-gray-200">
                    <div class="mr-2">
                        <img 
                            :src="'/storage/' + chat.avatar"
                            alt="avatar"
                            class="rounded-full object-cover h-15 w-15 mr-2"
                        >                    
                    </div>     
                    <div class="w-full">
                        <h5 class="font-bold mb-2 ml-4"> {{ chat.name }} </h5>
                        <div v-if="chatsMessages[chat.chatroom_id][0].sender_id != authUser.id" class="text-sm mb-3 mx-4 float-left">
                            {{ chat.name }}:
                        </div>
                        <div v-else class="text-sm mb-3 mx-4 float-left">
                            You:
                        </div>
                        <p class="text-sm mb-3 ml-4 break-words" v-bind:class="{ 'font-bold': !chatsMessages[chat.chatroom_id][0].read && chatsMessages[chat.chatroom_id][0].sender_id != authUser.id}">
                            {{ chatsMessages[chat.chatroom_id][0].text }}
                        </p>
                        <p class="text-xs float-right"> {{ new Date(chatsMessages[chat.chatroom_id][0].created_at) | dateFormat('HH:mm , DD.MM.YYYY') }} </p>
                    </div>
                </div>
                
            </a>
        </div>
    </div>

</template>

<script>
export default {
    props: ['user', 'chatrooms', 'messages'],

     data() {
        return {
            authUser: JSON.parse(this.user),
            chats: JSON.parse(this.chatrooms),
            chatsMessages: JSON.parse(this.messages),
        };
    },

    mounted() {
    },

    methods: {
        
    }

}
</script>

<style>

</style>