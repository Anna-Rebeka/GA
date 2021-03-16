<template>
    <div class="ml-8 mb-12">
        <div v-if="user.group">
            
            <p
                class="-ml-8 mt-4 block mb-7 font-bold text-lg text-center text-gray-700"
            >
                Settings for e-mail notifications (this group "{{
                    user.group.name
                }}"" )
            </p>

            <p
                class="mt-4 block mb-2 uppercase font-bold text-sm text-purple-800"
            >
                Notify me if there is a new:
            </p>
            <div class="mb-12">
                <div class="mb-2">
                    <input
                        type="checkbox"
                        id="assignments"
                        value="assignments"
                        v-model="checkedNotifications"
                    />
                    <label for="assignments">Assignment</label>
                </div>

                <div class="mb-2">
                    <input
                        type="checkbox"
                        id="events"
                        value="events"
                        v-model="checkedNotifications"
                    />
                    <label for="events">Event</label>
                </div>
                <div class="mb-2">
                    <input
                        type="checkbox"
                        id="whiteboard_posts"
                        value="whiteboard_posts"
                        v-model="checkedNotifications"
                    />
                    <label for="whiteboard_posts">Whiteboard post</label>
                </div>
            </div>
        </div>
        
        <hr class="w-full -ml-5 bg-gray-500 border-2 mb-12 rounded-full"/>

        <p
            class="-ml-8 mt-4 block mb-7 font-bold text-lg text-center text-gray-700"
        >
            Settings for e-mail notifications (all groups)
        </p>

        <div class="mb-4">
            <p class="block mb-2 uppercase font-bold text-sm text-orange-600">
                Notify me if I get an assignment:
            </p>
            <input
                type="radio"
                v-model="fields.getting_assignment"
                id="true"
                name="getting_assignment"
                :value="true"
                required
            />
            <label for="true">Yes</label><br />
            <input
                type="radio"
                v-model="fields.getting_assignment"
                id="false"
                name="getting_assignment"
                :value="false"
                required
            />
            <label for="false">No</label><br />
        </div>

        <div class="mb-4">
            <p
                class="mt-4 block mb-2 uppercase font-bold text-sm text-green-700"
            >
                Notify me about any updates on my assignments:
            </p>
            <input
                type="radio"
                v-model="fields.assignment_mine"
                id="true"
                name="assignment_mine"
                :value="true"
                required
            />
            <label for="true">Yes</label><br />
            <input
                type="radio"
                v-model="fields.assignment_mine"
                id="false"
                name="assignment_mine"
                :value="false"
                required
            />
            <label for="false">No</label><br />
        </div>

        <div class="mb-4">
            <p
                class="mt-4 block mb-2 uppercase font-bold text-sm text-green-700"
            >
                Notify me about any updates on events that I'm going to:
            </p>
            <input
                type="radio"
                v-model="fields.event_joined"
                id="true"
                name="event_joined"
                :value="true"
                required
            />
            <label for="true">Yes</label><br />
            <input
                type="radio"
                v-model="fields.event_joined"
                id="false"
                name="event_joined"
                :value="false"
                required
            />
            <label for="false">No</label><br />
        </div>

        <div class="mb-4">
            <p
                class="mt-4 block mb-2 uppercase font-bold text-sm text-blue-700"
            >
                Notify me about any updates on assignments that are created by
                me:
            </p>
            <input
                type="radio"
                v-model="fields.assignment_created_by_me"
                id="true"
                name="assignment_created_by_me"
                :value="true"
                required
            />
            <label for="true">Yes</label><br />
            <input
                type="radio"
                v-model="fields.assignment_created_by_me"
                id="false"
                name="assignment_created_by_me"
                :value="false"
                required
            />
            <label for="false">No</label><br />
        </div>

        <div class="mb-4">
            <p
                class="mt-4 block mb-2 uppercase font-bold text-sm text-blue-700"
            >
                Notify me about any updates on events that are created by me:
            </p>
            <input
                type="radio"
                v-model="fields.event_created_by_me"
                id="true"
                name="event_created_by_me"
                :value="true"
                required
            />
            <label for="true">Yes</label><br />
            <input
                type="radio"
                v-model="fields.event_created_by_me"
                id="false"
                name="event_created_by_me"
                :value="false"
                required
            />
            <label for="false">No</label><br />
        </div>



    </div>
</template>

<script>
export default {
    props: ["user"],
    data() {
        return {
            fields: {},
            checkedNotifications: [],
            errors: {},
            createNewAssignment: false,
            newAssignmentCreated: false,
        };
    },
};
</script>

<style>
</style>