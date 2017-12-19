<template>
    <div class="group-options-all">
        <h3 class="title is-7">Privacy</h3>
        <div class="control">
            <label class="checkbox">
                <input type="checkbox" name="privacy" :checked="privacy === 0" @change="updatePrivacy" />
                Private
            </label>
        </div>
    </div>
</template>

<script>
export default {
    props: ['privacy', 'updatePrivacy']
};
</script>
