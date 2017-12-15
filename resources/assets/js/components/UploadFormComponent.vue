<template>
    <div class="upload-form">
        <h2 class="title is-6">Upload</h2>
        <div class="image-preview" :class="{'is-hidden': fileSource === null}">
            <img :src="fileSource" :alt="fileName"/>
        </div>
        <div class="file is-boxed is-fullwidth">
            <label class="file-label">
                <input type="file" class="file-input" @change="updateFile"/>
                <span class="file-cta">
                    <span class="file-icon">
                        <i class="fa fa-upload"></i>
                    </span>
                    <span class="file-label has-text-centered">
                        {{ fileName }}
                    </span>
                </span>
            </label>
        </div>
        <div class="control is-padded-top">
            <label class="radio">
                <input type="radio" name="privacy" value="0" :checked="selectedType === '0'" @change="updateSelectedType" />
                Public
            </label>
            <label class="radio">
                <input type="radio" name="privacy" value="1" :checked="selectedType === '1'" @change="updateSelectedType" />
                Followers Only
            </label>
            <label class="radio">
                <input type="radio" name="privacy" value="2" :checked="selectedType === '2'" @change="updateSelectedType" />
                Private
            </label>
        </div>
        <div class="submit-button">
            <button class="button is-link is-pulled-right" @click="upload">Upload</button>
        </div>
    </div>
</template>

<script>
const axios = require('axios');

import { mapState } from 'vuex';

export default {
    computed: {
        ...mapState({
            fileName: state => state.upload.fileName,
            fileSource: state => state.upload.fileSource,
            selectedType: state => state.upload.selectedType
        })
    },

    methods: {
        updateSelectedType: function(e) {
            this.$store.commit('setSelectedType', e.target.value);
        },

        updateFile: function(e) {
            const files = e.target.files || e.dataTransfer.files;
            const file = files[0];
            const reader = new FileReader();

            reader.addEventListener('load', () => {
                this.$store.commit('setFileName', file.name);
                this.$store.commit('setFileSource', reader.result);
                this.$store.commit('setFullFile', file);
            }, false);

            reader.readAsDataURL(file);
        },

        upload: function() {
            const formData = new FormData();
            formData.append('image', this.$store.state.upload.fullFile);
            formData.append('privacy', this.$store.state.upload.selectedType);

            axios.post('/upload', formData)
                .then(response => {
                    const result = response.data;

                    if(result.errors) {
                        // display toast of error message
                        return;
                    }

                    // if response was unsuccessful without a specific error
                    if(!result.success) {
                        // display toast of error message
                        return;
                    }

                    this.$store.commit('setFileName', 'Upload an image');
                    this.$store.commit('setFileSource', null);
                    this.$store.commit('setFullFile', {});

                    // update the feed with new image
                    axios.get(`/graphql?query=query+getFeed{images{id, path, name, likes, dislikes, user{ username }}}`)
                        .then(response => {
                            const result = response.data;

                            this.$store.commit('setImages', result.data.images);
                        })
                })
        }
    }
}
</script>
