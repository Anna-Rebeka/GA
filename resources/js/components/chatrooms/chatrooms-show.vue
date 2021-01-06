<template>
    <div>
        <div class="container flow-root mb-2">
                <div v-for="message in messages" 
                    :key="message.id" 
                    class="bg-white relative clear-both w-1/2 px-4 pt-4 mb-2 border border-gray-300 rounded-lg"
                    v-bind:class="{'float-right bg-purple-100': message.sender.id == user.id, 'pb-6' : message.sender.id != user.id}"
                >
                    <div>
                        <h5 class="text-xs text-gray-500 absolute bottom-0">{{ message.sender.name }}</h5>
                        <div class="float-left mr-2">
                            <img 
                                :src="message.sender.avatar" 
                                alt="avatar"
                                class="rounded-full object-cover h-15 w-15 mr-2"
                            >                    
                        </div>     
                        <div>
                            <p class="text-sm mb-3 break-words p-4">
                                {{ message.text }}
                            </p>
                            <p class="text-xs float-right mb-2"> {{ new Date(message.created_at) | dateFormat('HH:mm , DD.MM.YYYY') }} </p>
                        </div>
                    </div>  
                </div>
            </div>
    </div>
</template>

<script>
export default {
    props: ['user', 'chatroom'],
    data() {
        return {
            showedChatroom: this.chatroom,
            messages: [],
        };
    },

    mounted(){
        console.log(this.chatroom)
        this.getMessages();
        console.log(this.messages);
    },

    //make message text a long text type
    methods: {
        getMessages() {
            axios.get('/chats/' + this.chatroom.id + '/messages').then(response => {
                this.messages = response.data;
                console.log(this.messages);
            }).catch(error => {
                if (error.response.status == 422){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                console.log(error.message);
            });
        },
    }
}

</script>

<style>

</style>