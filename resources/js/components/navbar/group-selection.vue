<template>
    <div class="relative">
        <button
            @click="isOpen = !isOpen"
            class="shadow relative z-10 inline-flex justify-center w-full rounded-full border border-gray-300 px-4 py-2 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 hover:bg-gray-100 focus:outline-none"
        >
            Options
        </button>
        <button
            v-if="isOpen"
            @click="isOpen = false"
            tabindex="-1"
            class="fixed inset-0 h-full w-full cursor-default z-20 outline-none border-none"
        ></button>
        <div
            v-if="isOpen"
            class="absolute right-0 mt-1 py-2 w-48 bg-gray-300 rounded-lg z-20"
        >
            <a
                href="/change-group"
                class="block px-4 py-2 text-gray-800 hover:bg-indigo-500 hover:text-white"
                >Change group</a
            >
            <a
                href="/create-group"
                class="block px-4 py-2 text-gray-800 hover:bg-indigo-500 hover:text-white"
                >Create a new group</a
            >
            
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            isOpen: false,
            csrf: document.head.querySelector('meta[name="csrf-token"]')
                .content,
        };
    },

    created() {
        const handleEscape = (e) => {
            if (e.key === "Esc" || e.key === "Escape") {
                this.isOpen = false;
            }
        };
        document.addEventListener("keydown", handleEscape);
        this.$once("hook:beforeDestroy", () => {
            document.removeEventListener("keydown", handleEscape);
        });
    },
};
</script>