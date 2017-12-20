<template>
    <div class="group-options-all">
        <div class="control">
            <label class="checkbox">
                <input type="checkbox" name="privacy" :checked="privacy === 0" @change="updatePrivacy" />
                Invite-Only
            </label>
        </div>
    </div>
</template>

<script>
export default {
    props: ['privacy', 'updatePrivacy']
};
</script>
