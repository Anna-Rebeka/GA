import Vue from 'vue';
import dropdown from './components/dropdown.vue';
import groupPanel from './components/group-panel.vue';
import membersPanel from './components/members-panel.vue';
import changeGroup from './components/groups/change-group.vue';
import userShow from './components/users/user-show.vue';
import userEdit from './components/users/user-edit.vue';
import groupUsers from './components/users/group-members.vue';
import userEvents from './components/users/user-events.vue';
import userAssignments from './components/users/user-assignments.vue';
import notes from './components/notes/note-index.vue';
import events from './components/events/group-events.vue';
import eventShow from './components/events/event-show.vue';
import eventsTable from './components/events/events-table.vue';
import eventComments from './components/comments/event-comments.vue';
import assignments from './components/assignments/group-assignments.vue';
import assignmentsTable from './components/assignments/assignments-table.vue';


import JwPagination from 'jw-vue-pagination';

import VueFilterDateFormat from 'vue-filter-date-format';

require('masonry-layout');
require('./bootstrap');

Vue.use(VueFilterDateFormat);

Vue.component('dropdown', dropdown);
Vue.component('group-panel', groupPanel);
Vue.component('members-panel', membersPanel);
Vue.component('change-group', changeGroup);
Vue.component('user-show', userShow);
Vue.component('user-edit', userEdit);
Vue.component('group-members', groupUsers);
Vue.component('user-events', userEvents);
Vue.component('user-assignments', userAssignments);
Vue.component('note-index', notes);
Vue.component('group-events', events);
Vue.component('event-show', eventShow);
Vue.component('event-comments', eventComments);
Vue.component('events-table', eventsTable);
Vue.component('group-assignments', assignments);
Vue.component('assignments-table', assignmentsTable);
Vue.component('jw-pagination', JwPagination);

var app = new Vue({
   el: '#app',
});

