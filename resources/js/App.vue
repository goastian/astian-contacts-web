<template>
    <div class="content">
        <div v-if="authenticated" class="body">
            <v-index> </v-index>
        </div>
        <div v-if="!authenticated">
            <router-view></router-view>
        </div>
    </div>
    <v-footer></v-footer>
</template>
<script>
import VIndex from "./dashboard/Index.vue";
import VFooter from "./dashboard/Footer.vue";
export default {
    components: {
        VIndex,
        VFooter,
    },

    computed: {
        authenticated() {
            return this.$route.meta.auth || false;
        },
    },
};
</script>
<style>
.content {
    flex: 1;
    margin: 0;
    min-height: 100vh;
}
</style>
