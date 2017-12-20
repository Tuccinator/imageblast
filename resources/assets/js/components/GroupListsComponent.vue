<template>
    <div class="groups-list">
        <div class="group-row columns" v-for="group in groups">
            <div class="group-name column is-one-third">
                <span>{{ group.name }}</span>
            </div>
            <div class="group-join column is-two-thirds">
                <button class="button is-small is-info" @click="joinGroup(group.id)" v-if="auth && !isUserOfGroup(group.id)">Join Group</button>
                <button class="button is-small is-danger" @click="joinGroup(group.id)" v-else-if="auth">Leave Group</button>
                <a :href="'/groups/' + group.id" class="button is-small is-warning">View Group</a>
            </div>
        </div>
    </div>
</template>

<script>

const axios = require('axios');
const _ = require('lodash');

export default {
    props: ['auth', 'authId'],

    data: function() {
        return {
            groups: [],
            userGroups: []
        };
    },

    methods: {
        isUserOfGroup: function(groupId) {

            // check if the user belongs to the specific group
            return _.findIndex(this.userGroups, group => { return group.id === groupId }) >= 0;
        },

        joinGroup: function(groupId) {

            // join the group
            axios.post(`/graphql?query=mutation+groups{ joinGroup(id: ${groupId}){ id }}`)
                .then(response => {
                    const result = response.data;

                    // check if it joined correctly
                    if(result.data.joinGroup === null) {
                        return;
                    }

                    const groupIndex = _.findIndex(this.userGroups, (group) => { return group.id === groupId });

                    // check if the user joined or left the group
                    if(groupIndex === -1) {
                        this.userGroups.push({ id: groupId });
                    } else {
                        this.userGroups = _.remove(this.userGroups, group => { return group.id !== groupId });
                    }
                })
        }
    },

    mounted: function() {
        axios.get(`/graphql?query=query+groups{ groups(order: "desc"){ id, name }, users(id: ${this.authId}) {id, groups{id}}}`)
            .then(response => {
                const result = response.data;

                this.groups = result.data.groups;

                // if the user is logged in, add their user groups
                if(result.data.users.length > 0) {
                    this.userGroups = result.data.users[0].groups;
                }
            })
    }
}

</script>
