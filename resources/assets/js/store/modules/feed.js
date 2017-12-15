const _ = require('lodash');

const feed = {
    state: {
        images: []
    },
    mutations: {
        setImages: (state, images) => {
            state.images = images;
        },

        updateImageInfo: (state, image) => {
            const images = state.images;
            const index =_.findIndex(images, imageObj => { return imageObj.id === image.id });

            images[index].likes = image.likes;
            images[index].dislikes = image.dislikes;

            state.images = images;
        }
    }
};

module.exports = feed;
