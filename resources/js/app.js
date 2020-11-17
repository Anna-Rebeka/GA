import Vue from 'vue';
import dropdown from './components/dropdown.vue';
import groupPanel from './components/group-panel.vue';
import membersPanel from './components/members-panel.vue';
import changeGroup from './components/change-group.vue';
import groupUsers from './components/group-users.vue';

require('./bootstrap');

Vue.component('dropdown', dropdown);
Vue.component('group-panel', groupPanel);
Vue.component('members-panel', membersPanel);
Vue.component('change-group', changeGroup);
Vue.component('group-users', groupUsers);

var app = new Vue({
   el: '#app',
});

