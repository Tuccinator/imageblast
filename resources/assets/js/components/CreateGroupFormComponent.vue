<template>
    <div class="create-group-form">
        <h2 class="title is-6">Create Group</h2>

        <div class="field">
            <label class="label">Name</label>
            <input type="text" class="input is-fullwidth" :class="{ 'is-danger': !nameValid }" placeholder="Enter group name" @input="updateName" :value="name" />
        </div>
        <div class="field">
            <label class="label">Description</label>
            <textarea class="textarea is-fullwidth" placeholder="Enter group description (optional)" @input="updateDescription" :value="description"></textarea>
        </div>
        <div class="control">
            <label class="checkbox">
                <input type="checkbox" :checked="privacy === true" @change="updatePrivacy" />
                Private
            </label>
        </div>
        <div class="submit-button" style="margin-top: 0.5rem;">
            <button class="button is-fullwidth is-warning" @click="createGroup">Create</button>
        </div>
    </div>
</template>

<script>
const axios = require('axios');

import { mapState } from 'vuex';

export default {
    computed: {
        ...mapState({
            name: state => state.group.name,
            nameValid: state => state.group.nameValid,
            description: state => state.group.description,
            privacy: state => state.group.privacy
        })
    },

    methods: {
        updateName: function(e) {
            this.$store.commit('setGroupName', e.target.value);
        },

        updateDescription: function(e) {
            this.$store.commit('setGroupDescription', e.target.value);
        },

        updatePrivacy: function(e) {
            this.$store.commit('setGroupPrivacy', e.target.value);
        },

        createGroup: function() {
            if(this.name.length == 0) {
                this.$store.commit('setGroupNameValid', false);
                return;
            }

            const privacy = this.privacy ? 0 : 1;

            axios.post(`/graphql?query=mutation+groups{createGroup(name: "${this.name}", description: "${this.description}", privacy: ${privacy}){id}}`)
                .then(response => {
                    const result = response.data;

                    if(result.errors) {
                        // display errors in toast
                        return;
                    }

                    if(result.data == null) {
                        return;
                    }

                    this.$store.commit('setGroupName', '');
                    this.$store.commit('setGroupNameValid', true);
                    this.$store.commit('setGroupDescription', '');

                    window.location.href = '/group/' + result.data.createGroup.id;
                })
        }
    }
}
</script>
