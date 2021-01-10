<template>
    <div class="border-bottom relative border-gray-300 rounded-lg mb-2">
        <div class="relative inline-block w-44 h-10 mb-10" >
            <input id="memberInput" type="text" name="memberInput" placeholder="New message to..."
                class="rounded-lg bg-white border border-gray-300 text-gray-500 w-full h-full px-5 pr-10 rounded-lg text-sm focus:outline-none focus:border-l focus:border-r focus:bg-white focus:border-gray-500"
            >
        </div>
        <div v-for="chat in chatrooms" :key="chat.id" >
            <a v-if="chat.latest_message" :href="'/chats/' + chat.id">
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
                        <p v-if="chat.latest_message.text.length > 30" class="text-sm mb-3 ml-4 break-words" v-bind:class="{ 'font-bold': !chat.latest_message.read && chat.latest_message.sender_id != user.id}">
                            {{ chat.latest_message.text.substring(0, 30) + "..." }}
                        </p>
                        <p v-else class="text-sm mb-3 ml-4 break-words" v-bind:class="{ 'font-bold': !chat.latest_message.read && chat.latest_message.sender_id != user.id}">
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
    
    data() {
        return {
            members: [],
            lettersCounter: 0,
            showedChatroom: this.chatrooms,
        };
    },

    mounted () {
        this.getGroupMembers();
        this.showedChatroom.forEach(chatroom => {
            window.Echo.private('chatrooms.' + chatroom.id)
            .listen('MessageSent', (e) => {
                this.getLatestMessage(chatroom);
            });
        });
    },

    methods: {
        getLatestMessage(chatroom){
            axios.get('/chats/' + chatroom.id + '/latestMessage').then(response => {
                chatroom.latest_message = response.data;
                chatroom.latest_message.sender_id = chatroom.latest_message.sender.id;
            }).catch(error => {
                if (error.response.status == 422){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                console.log(error.message);
            });
        },

        getGroupMembers(){
            axios.get('/group/' + this.user.active_group + '/members/get').then(response => {
                this.members = response.data;
                this.autocomplete(document.getElementById("memberInput"), this.members, this.lettersCounter);
            }).catch(error => {
                if (error.response.status == 422){
                    this.errors = error.response.data.errors;
                    console.log(this.errors);
                }
                console.log(error.message);
            });
        },

        autocomplete(inp, members, counter) {            
            inp.addEventListener("input", function(e) {
                if (counter < 2){
                    counter += 1;
                    return false;
                }
                counter = 0;
                var a, b, i, val = this.value;
                closeAllLists();
                if (!val) { return false;}
                a = document.createElement("DIV");
                a.setAttribute("id", this.id + "autocomplete-list");
                a.setAttribute("class", "autocomplete-items");
                this.parentNode.appendChild(a);
                for (i = 0; i < members.length; i++) {
                    if (members[i].name.toLowerCase().includes(val.toLowerCase())){
                        b = document.createElement("DIV");
                        b.setAttribute("class", "w-full h-full");
                        b.innerHTML += "<a class='block border-none w-full' href='/chats/find/" + members[i].id + "'>" + members[i].name + "</a>";
                        a.appendChild(b);
                    }
                }
            });
           
            function closeAllLists(elmnt) {
                var x = document.getElementsByClassName("autocomplete-items");
                for (var i = 0; i < x.length; i++) {
                    if (elmnt != x[i] && elmnt != inp) {
                        x[i].parentNode.removeChild(x[i]);
                    }
                }
            }
            document.addEventListener("click", function (e) {
                closeAllLists(e.target);
            });
        }   
    }

}
</script>

<style>

.autocomplete-items {
  position: absolute;
  z-index: 99;
  top: 100%;
  left: 0;
  right: 0;
  font-size: 14px;
  border: solid #d1d5db 0.5px;
  border-radius: 8px;
  background-color: #d1d5db
}
.autocomplete-items div {
  padding: 8px;
  cursor: pointer;
}
.autocomplete-items div:hover {
  background-color:#6366f1;
  color: white;
}
.autocomplete-active {
  background-color: #6366f1 !important;
  color: white;
}
</style>