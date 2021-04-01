<template>
    <div class="mb-6">
        <div class="relative inline-block w-56 h-10 mb-10">
            <input
                id="memberInput"
                type="text"
                name="memberInput"
                placeholder="Start typing a name.."
                class="rounded-lg bg-white border border-gray-300 text-gray-500 w-full h-full px-5 pr-10 rounded-lg text-sm focus:outline-none focus:border-l focus:border-r focus:bg-white focus:border-gray-500"
            />
        </div>
        <ul>
            <li
                v-for="user in pageOfItems"
                :key="user.id"
                class="inline float-left mr-4"
            >
                <div class="relative">
                    <button
                        @click="checkWithUser(user)"
                        class="absolute top-0 right-0 rounded-full border"
                    >
                        <img
                            class="object-cover rounded-full h-8 w-8"
                            src="/img/cancel.png"
                            alt="exclude"
                        />
                    </button>
                    <a :href="'/profile/' + user.username">
                        <div
                            class="max-w-xs lg:w-40 lg:h-60 w-20 h-30 rounded overflow-hidden shadow-lg mb-4"
                        >
                            <img
                                class="lg:w-40 lg:h-40 w-20 h-20 object-cover"
                                :src="user.avatar"
                                :alt="user.username"
                            />
                            <div class="px-6 py-2 text-center">
                                <div class="font-bold text-sm mb-2">
                                    {{ user.name }}
                                </div>
                            </div>
                            <p
                                class="invisible text-gray-700 text-center mb-1 text-xs lg:visible xl:visible"
                            >
                                {{ user.email }}
                            </p>
                        </div>
                    </a>
                </div>
            </li>
        </ul>

        <div class="mt-10 clear-both w-full text-center text-sm">
            <jw-pagination
                :items="savedUsers"
                @changePage="onChangePage"
                :pageSize="6"
            ></jw-pagination>
        </div>
    </div>
</template>

<script>
export default {
    props: ["users", "group"],
    data() {
        return {
            pageOfItems: [],
            savedUsers: this.users,
            lettersCounter: 0,
        };
    },

    mounted() {
        this.autocomplete(
            document.getElementById("memberInput"),
            this.savedUsers,
            this.lettersCounter
        );
    },

    methods: {
        checkWithUser(user) {
            if (
                confirm(
                    "Are you sure you want " +
                        user.name +
                        " to leave this group?"
                )
            ) {
                this.excludeUserFromGroup(user);
            }
        },

        excludeUserFromGroup(user) {
            axios
                .delete(
                    "/groups/" + this.group.id + "/exclude-member/" + user.id
                )
                .then((response) => {
                    this.savedUsers = this.savedUsers.filter(function (u) {
                        return u.id !== response.data.id;
                    });
                });
        },

        onChangePage(pageOfItems) {
            this.pageOfItems = pageOfItems;
        },

        autocomplete(inp, members, counter) {
            inp.addEventListener("input", function (e) {
                if (counter < 2) {
                    counter += 1;
                    return false;
                }
                counter = 0;
                var a,
                    b,
                    i,
                    val = this.value;
                closeAllLists();
                if (!val) {
                    return false;
                }
                a = document.createElement("DIV");
                a.setAttribute("id", this.id + "autocomplete-list");
                a.setAttribute("class", "autocomplete-items");
                this.parentNode.appendChild(a);
                for (i = 0; i < members.length; i++) {
                    if (
                        members[i].name
                            .toLowerCase()
                            .includes(val.toLowerCase())
                    ) {
                        b = document.createElement("DIV");
                        b.setAttribute("class", "w-full h-full");
                        b.innerHTML +=
                            "<a class='block border-none w-full' href='/profile/" +
                            members[i].username +
                            "'>" +
                            members[i].name +
                            "</a>";
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
        },
    },
};
</script>
