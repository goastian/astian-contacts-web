<template>
    <div class="p-0 m-0">
        <v-contact @is-created="isRegistered" class="border"></v-contact>

        <v-phone v-show="registered" class="border"></v-phone>

        <v-email v-show="registered" class="border"></v-email>


        <v-sites v-show="registered" class="border"></v-sites>
    </div>
</template>
<script>
import VContact from "./contact.vue";
import VEmail from "./email.vue";
import VPhone from "./phone.vue";
import VSites from "./sites.vue";

export default {
    components: {
        VEmail,
        VPhone,
        VSites,
        VContact,
    },

    data() {
        return {
            registered: false,
        };
    },

    created() {
        this.registered = false;
        if (this.$route.params.id) {
            this.registered = true;
        }
    },

    watch: {
        $route(to, from) {
            if (!to.params.id) {
                this.registered = false;
            }
        },
    },

    methods: {
        isRegistered(event) {
            this.registered = event;
        },
    },
};
</script>
<style lang="scss" scoped>
.border {
    padding: 0.1em 0.2em;
    margin: 0.2em;
    border-radius: 0.2em;
}
</style>
