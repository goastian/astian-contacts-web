<template>
    <div class="table-box">
        <ul class="head">
            <li class="item" v-for="(item, index) in head" :key="index">
                {{ item }}
            </li>
        </ul>
        <ul class="body">
            <li class="item" v-for="(item, index) in body" :key="index">
                <slot name="body" :item="item"></slot>
            </li>
        </ul>
    </div>
</template>
<script>
export default {
    props: {
        head: {
            required: true,
            type: Array,
        },
        body: {
            required: true,
            type: [Array, Object],
        },
    },
};
</script>
<style lang="scss" scoped>
.table-box {
    .head {
        display: flex;
        list-style: none;
        padding: 0;

        .item {
            flex: 1 0 auto;
        }
    }
    .body {
        display: flex;
        flex-wrap: wrap;
        list-style: none;
        padding: 0;

        .item {
            flex: 1 1 auto;
        }
    }
}
</style>
