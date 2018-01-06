<template>
    <div class="groups-list">
        <div class="group-row columns" v-for="group in groups">
            <div class="group-name column is-one-third">
                <span>{{ group.name }}</span>
            </div>
            <div class="group-join column is-two-thirds">
                <button class="button is-small is-info" @click="joinGroup(group.id, group.public)" v-if="auth && !isUserOfGroup(group.id)">Join Group</button>
                <button class="button is-small is-danger" @click="joinGroup(group.id)" v-else-if="auth">Leave Group</button>
                <a :href="'/groups/' + group.id" class="button is-small is-warning" v-if="group.public || isUserOfGroup(group.id)">View Group</a>
            </div>
        </div>
        <div class="modal" :class="{'is-active': this.codeModalOpen}">
            <div class="modal-background" @click="closeCodeModal"></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">Enter Invite Code</p>
                    <button class="delete" aria-label="close" @click="closeCodeModal"></button>
                </header>
                <section class="modal-card-body">
                    <input type="text" class="input" :value="code" @input="updateCode" />
                </section>
                <footer class="modal-card-foot is-pulled-right">
                    <button class="button is-warning is-pulled-right" @click="joinGroupWithCode">Join</button>
                </footer>
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
            userGroups: [],
            code: '',
            codeSubmitted: false,
            codeModalOpen: false,
            previousGroupId: 0,
            previousGroupPrivacy: 0
        };
    },

    methods: {
        isUserOfGroup: function(groupId) {
            // check if the user belongs to the specific group
            return _.findIndex(this.userGroups, group => { return group.id === groupId }) >= 0;
        },

        closeCodeModal: function() {
            this.codeModalOpen = false;
        },

        updateCode: function(e) {
            this.code = e.target.value;
        },

        joinGroupWithCode: function() {
            if(this.code.length <= 0) {
                return;
            }

            this.codeSubmitted = true;

            this.joinGroup(this.previousGroupId, this.previousGroupPrivacy);
        },

        joinGroup: function(groupId, privacy) {
            // join the group
            if(privacy === 0 && !this.codeSubmitted) {
                this.codeModalOpen = true;
                this.previousGroupId = groupId;
                this.previousGroupPrivacy = privacy;
                return;
            }

            axios.post(`/graphql?query=mutation+groups{ joinGroup(id: ${groupId}, code: "${this.code}"){ id }}`)
                .then(response => {
                    const result = response.data;

                    this.codeModalOpen = false;
                    this.code = '';
                    this.codeSubmitted = false;

                    // check if it joined correctly
                    if(result.data.joinGroup === null) {
                        // display toast
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
        axios.get(`/graphql?query=query+groups{ groups(order: "desc"){ id, name, public }, users(id: ${this.authId}) {id, groups{id}}}`)
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
