<template>
    <div>
        <h5 v-for="chatUser in users"
                    :key="chatUser.id"
                    class="text-lg font-bold text-center h-14 w-full -mt-4 mb-6"
        >
            {{ chatUser.name }}

        </h5>
        <div id="chatWindow" ref="chat" class="container mb-2 pr-4 h-80 overflow-y-scroll">                
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
            <form class="mb-4" @submit.prevent="submit">
                <div class="bg-white relative h-24 w-full px-4 pt-4 mt-4 mb-2 border border-gray-300 rounded-lg">
                    <!--<input type="hidden" name="_token" :value="csrf" /> -->
                    <textarea id="messageArea" name="text" placeholder="New message..."
                        class="w-full resize-none focus:outline-none"
                    >
                    </textarea>
                </div>    
            </form> 
    </div>
</template>

<script>
export default {
    props: ['user', 'chatroom'],
    data() {
        return {
            showedChatroom: this.chatroom,
            messages: [],
            users: [],
            newMessage: '',
        };
    },

    mounted(){
        this.getMessages();
        this.getUsers();
        document.getElementById("messageArea").addEventListener("keypress", this.submitOnEnter);
        
        window.Echo.private('chatrooms.' + this.chatroom.id)
        .listen('MessageSent', (e) => {
            e.message.sender = e.sender;
            this.messages.push(e.message);
            this.scrollChat();
        });
    },

    //TODO : make message text a long text type
    methods: {
        scrollChat() {
            this.$nextTick(
                () => {
                    this.$refs.chat.scrollTop=9999;
                }
            )
        },

        getUsers() {
            axios.get('/chats/' + this.chatroom.id + '/users/' + this.user.id).then(response => {
                this.users = response.data;
            }).catch(error => {
                if (error.response.status == 422){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                console.log(error.message);
            });
        },

        getMessages() {
            var div = this.chatWindow;
            axios.get('/chats/' + this.chatroom.id + '/messages').then(response => {
                this.messages = response.data;
                this.scrollChat();
            }).catch(error => {
                if (error.response.status == 422){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                console.log(error.message);
            });
        },

        submitOnEnter(event){
            if(event.which === 13){
                event.target.form.dispatchEvent(new Event("submit", {cancelable: true}));
                event.preventDefault();     
            }
        },

        submit(){
            var messageArea = document.getElementById("messageArea").value;
            document.getElementById("messageArea").value = "";
            axios.post('/chats/' + this.chatroom.id + '/send', {chatroom_id: this.chatroom.id, text: messageArea, chatroom: this.chatroom}).then(response => {
                response.data['sender'] = this.user;    
                this.messages.push(response.data);
                this.$nextTick(
                    () => {
                        this.$refs.chat.scrollTop=9999;
                    }
                )
            }).catch(error => {
                if (error.response.status == 422){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                console.log(error.message);
            });
        }
    }
}

</script>

<style>

</style>