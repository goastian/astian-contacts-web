<script setup>
import { ref } from "vue";
const props = defineProps(["error"]);
</script>

<template>
    <div
        v-show="!error.example"
        class="errors"
        v-for="(item, index) in error"
        :key="index"
    >
        <span v-text="item"></span>
    </div>
</template>
<script></script>
<style lang="css" scoped>
.errors {
    color: rgb(233, 104, 104);
}
</style>
