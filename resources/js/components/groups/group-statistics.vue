<template>
    <div>
        <div>
            <h3 class="text-center font-bold text-lg mb-3">Team Workload</h3>
             <div class="inline-block relative text-gray-600 w-1/3">
                <input class="rounded-lg bg-white border border-gray-300 text-gray-500 w-full h-8 px-5 pr-10 rounded-lg text-sm focus:outline-none focus:border-l focus:border-r focus:bg-white focus:border-gray-500"
                    id="searchBar" type="search" name="searchBar" placeholder="Search by name">
                <button id="searchButton" type="submit" class="absolute right-0 top-2 mr-4 bg-transparent focus:outline-none">
                    <img src="/img/search.png" width="20" height="20" alt="submit" />
                </button>
            </div>
            <div class="clear-both mb-3"></div>
            <p class="text-sm font-bold float-left ml-2 mr-2">%</p>
            <p class="text-xs font-bold text-blue-500 float-left">
                = users assignments
            </p>
            <p class="text-sm font-bold float-left ml-2 mr-2">/</p>
            <p class="float-left text-xs font-bold text-red-600 mb-4 mr-8">
                all assignments
            </p>
            <div class="clear-both mb-6"></div>
            <div
                v-for="statUser in pageOfItems"
                :key="statUser.id"
                class="relative float-left w-32 h-44 py-2 mr-5 text-center shadow-md border border-gray-200"
            >
                <img
                    v-if="!statUser.user_to_all"
                    class="rounded-full shadow-sm mx-auto mb-1 w-20 h-20 object-cover border-8 border-green-400 border-opacity-30"
                    :src="avatarPath(statUser.avatar)"
                    alt="userAvatar"
                />
                <img
                    v-else
                    class="rounded-full shadow-sm mx-auto mb-1 w-20 h-20 object-cover border-8"
                    :src="avatarPath(statUser.avatar)"
                    alt="userAvatar"
                    :style="
                        'border-color: rgba(0, ' +
                        (220 - Math.ceil(130 * statUser.user_to_all)) +
                        ' , 0, 0.3)'
                    "
                />
                <div class="max-h-14 overflow-y-auto">
                    <a
                        class="hover:underline"
                        :href="'/profile/' + statUser.username"
                    >
                        {{ statUser.name }}
                    </a>
                </div>
                <p
                    v-if="statUser.user_to_all"
                    class="text-sm font-medium absolute bottom-0 right-8"
                >
                    {{ (statUser.user_to_all * 100).toFixed(2) }} %
                </p>
                <p v-else class="text-sm font-medium absolute bottom-0 right-9">
                    0.00 %
                </p>
            </div>
        </div>
        <div class="clear-both h-10"></div>
        <div class="mt-5 clear-both w-full text-center text-sm">
            <jw-pagination :items="shownUsers" @changePage="onChangePage" :pageSize="20"></jw-pagination>
        </div>
    </div>
</template>

<script>
export default {
    props: ["user", "group", "stats", "free_users"],

    data() {
        return {
            newMessage: false,
            howMany: 0,
            chatrooms: [],
            usersInfo: null,
            pageOfItems: [],
            allUsers: this.allUsers = this.free_users.concat(this.stats),
            shownUsers: this.allUsers = this.free_users.concat(this.stats),
            lettersCounter: 0,
        };
    },

    mounted() {
        document.getElementById("searchButton").addEventListener("click", this.findUserByName);
        this.searchBar = document.getElementById("searchBar");
        this.searchBar.addEventListener("keypress", this.searchOnEnter);
        this.autocomplete(document.getElementById("searchBar"), this.allUsers, this.lettersCounter);
    },

    methods: {
        searchOnEnter(event){
            if(event.which === 13){
                this.shownUsers = this.allUsers;
                this.findUserByName();
                event.preventDefault();     
            }
        },

        findUserByName(){
            if(this.searchBar.value == ""){
                this.shownUsers = this.allUsers;
                return;
            }
            var findBy = this.searchBar.value;
            this.shownUsers = this.shownUsers.filter(function(e) {
                return e.name.toLowerCase().includes(findBy.toLowerCase());
            });
        },

        onChangePage(pageOfItems) {
            this.pageOfItems = pageOfItems;
        },

        avatarPath(oldPath) {
            if(!oldPath){
                return '/img/default.jpg';
            }
            var index = oldPath.lastIndexOf("/") + 1;
            var newPath = '/storage/users/avatars/'.concat(oldPath.substring(index));
            return newPath;
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
                        b.innerHTML += "<a class='block border-none w-full' href='/profile/" + members[i].username + "'>" + members[i].name + "</a>";
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
    },
};
</script>

<style>
</style>