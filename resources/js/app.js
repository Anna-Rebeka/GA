import Vue from 'vue';
import dropdown from './components/dropdown.vue';
import groupPanel from './components/group-panel.vue';
import membersPanel from './components/members-panel.vue';
import changeGroup from './components/change-group.vue';
import groupUsers from './components/group-users.vue';
import notes from './components/notes.vue';
import events from './components/group-events.vue';
import JwPagination from 'jw-vue-pagination';

require('./bootstrap');

Vue.component('dropdown', dropdown);
Vue.component('group-panel', groupPanel);
Vue.component('members-panel', membersPanel);
Vue.component('change-group', changeGroup);
Vue.component('group-users', groupUsers);
Vue.component('notes', notes);
Vue.component('events', events);
Vue.component('jw-pagination', JwPagination);

var app = new Vue({
   el: '#app',
});

