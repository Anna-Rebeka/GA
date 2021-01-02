<template>
    <div class="border-bottom border-gray-300 rounded-lg mb-2">
        
        <div v-for="chat in chats" :key="chat.id" >
            <a :href="'/messages/' + chat.id">
                <div class="flex p-4 border-b border-b-gray-400 hover:bg-gray-200">
                    <div class="mr-2">
                        <img 
                            :src="chat.avatar" 
                            alt="avatar"
                            class="rounded-full object-cover h-15 w-15 mr-2"
                        >                    
                    </div>     
                    <div class="w-full">
                        <h5 class="font-bold mb-2"> {{ chat.name }} </h5>
                        <p class="text-sm mb-3 break-words">
                            {{ chat.text }}
                        </p>
                        <p class="text-xs float-right"> {{ new Date(chat.created_at) | dateFormat('HH:mm , DD.MM.YYYY') }} </p>
                    </div>
                </div>
                
            </a>
        </div>
    </div>

</template>

<script>
export default {
    props: ['user'],

     data() {
        return {
            chats: [],
        };
    },

    mounted() {
        this.getAllUserMessages();
    },

    methods: {
        getAllUserMessages() {
            axios.get('/messages/all').then(response => {
                this.chats = response.data;
                console.log(this.chats);

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