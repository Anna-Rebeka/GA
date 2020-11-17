<template>
    <div>
        <ul id="example-1">
            <li v-for="group in groups" :key="group.id" class="inline float-left mr-4">
                <div class="max-w-xs rounded overflow-hidden shadow-lg mb-4">
                    <img class="w-full" src="/img/default-banner.png" alt="Sunset in the mountains">
                    <div class="px-6 py-4">
                        <div class="font-bold text-xl mb-2">
                            {{ group.name }}
                        </div>
                        <p class="text-gray-700 text-base">
                            <ul id="example-1">
                                <li v-for="user in gusers[group.id]" :key="user.name">
                                    {{ user.name }}
                                </li>
                            </ul>       
                        </p>
                    </div>
                    <div class="px-6 pt-4 pb-2">

                    </div>
                </div>
            </li>
        </ul>
        

        

<!--


        <ul class="px-2" id="example-1">
            <li class="flex -mx-2" v-for="g in groups" :key="g.name">
                <div class="w-1/3 px-2 mb-2">
                    <div class="bg-gray-500 h-12"></div>
                </div>
            </li>
        </ul>
-->
    </div>
</template>

<script>
export default {
  props: ['user', 'groups', 'gusers'],

  data(){
    return {
      users: null
    }
  },
  mounted(){
    console.log(this.groups);
    console.log(this.gusers);
  }

}
</script>

<style>

</style>