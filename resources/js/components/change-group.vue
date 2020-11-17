<template>
    <div class="mb-6">
        <ul>
            <li v-for="group in groups" :key="group.id" class="inline float-left mr-4">
                <div class="max-w-xs rounded overflow-hidden shadow-lg mb-4">
                    <img class="w-full" src="/img/default-banner.png" :alt="group.name">
                    <div class="px-6 py-4">
                        <div class="font-bold text-xl mb-2">
                            {{ group.name }}
                        </div>
                        <p class="text-gray-700 text-base">
                            <ul>
                                <li v-for="user in gusers[group.id]" :key="user.name">
                                    {{ user.name }}
                                </li>
                            </ul>       
                        </p>
                    </div>
                    <div class="px-6 pt-4 pb-2 mb-6 float-right">
                        <a 
                            v-if="group.id != user.active_group"
                            :href="'/activate-group/' +  group.id"
                            class="rounded-full border border-gray-300 py-2 px-4 mr-2 text-black text-xs bg-green-200 hover:text-gray-500 hover:bg-green-100">
                            Watch group
                        </a>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
  props: ['user', 'groups', 'gusers'],
}
</script>
