<template>
    <div class="image-feed">
        <div class="feed-section" v-for="image in images">
            <h2 class="title is-6">{{ image.user.username }}</h2>
            <div class="image-preview">
                <img :src="image.path" :alt="image.name" />
            </div>
            <div class="like-section is-pulled-right">
                <span>{{ image.likes }} <i class="fa fa-thumbs-up like" @click="likeImage(image.id, '1')"></i></span>
                <span>{{ image.dislikes }} <i class="fa fa-thumbs-down dislike" @click="likeImage(image.id, '2')"></i></span>
            </div>
        </div>
    </div>
</template>

<script>
const axios = require('axios');

import { mapState } from 'vuex';

export default {
    computed: {
        ...mapState({
            images: state => state.feed.images
        })
    },

    mounted: function() {
        axios.get(`/graphql?query=query+getFeed{images{id, path, name, likes, dislikes, user{ username }}}`)
            .then(response => {
                const result = response.data;

                this.$store.commit('setImages', result.data.images);
            })
    },

    methods: {
        likeImage: function(id, type) {
            axios.get(`/graphql?query=mutation+images{likeImage(id: ${id}, type: "${type}"){id, likes, dislikes}}`)
                .then(response => {
                    const result = response.data;

                    this.$store.commit('updateImageInfo', result.data.likeImage);
                })
        },
    }
}
</script>
