<template>
    <div class="padded-container container avatar-form">
        <h1 class="title is-5">Upload Avatar</h1>
        <div class="columns">
            <div class="column is-one-fifth">
                <img :src="avatar" />
            </div>
            <div class="column is-four-fifths">
                <div class="file is-boxed" v-bind:class="{ 'is-danger': avatarError }">
                    <label class="file-label">
                        <input type="file" class="file-input" @change="changeAvatar"/>
                        <span class="file-cta">
                            <span class="file-icon">
                                <i class="fa fa-upload"></i>
                            </span>
                            <span class="file-label">
                                Choose a file...
                            </span>
                        </span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
const axios = require('axios');

export default {
    props: ['currentAvatar'],

    // using data instead of Vuex because of initial prop
    data: function() {
        return {
            avatar: this.currentAvatar,
            avatarError: false
        }
    },

    methods: {
        changeAvatar: function(e) {
            this.avatarError = false;

            const files = e.target.files || e.dataTransfer.files;
            const file = files[0];

            const formData = new FormData();
            formData.append('avatar', file);

            axios.post('/avatar', formData)
                .then(response => {
                    const result = response.data;

                    if(result.errors) {
                        this.avatarError = true;
                        return;
                    }

                    // if response was unsuccessful without a specific error
                    if(!result.success) {
                        this.avatarError = true;
                        return;
                    }

                    this.avatar = result.path;
                })
        }
    }
}
</script>
