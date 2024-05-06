<template>
    <div class="group-info">
        <p class="text-color fw-bold p-0 m-0">Add new groups</p>
        <div class="inputs">
            <div class="item">
                <div class="group">
                    <div>
                        <label for="" class="label">Group Name</label>
                    </div>
                    <input
                        type="text"
                        class="input-theme"
                        placeholder="Group name"
                        v-model="group.group"
                    />
                </div>
                <v-error :error="errors.group"></v-error>
            </div>
            <div class="item">
                <button
                    class="btn btn-primary"
                    v-show="!group_update"
                    @click="createGroup"
                >
                    Add Group
                    <i class="bi bi-collection"></i>
                </button>

                <button
                    v-show="group_update"
                    class="btn btn-primary btn-ternary"
                    @click="updateGroup"
                >
                    Change Group Name
                    <i class="bi bi-cloud-upload mx-2"></i>
                </button>
            </div>
        </div>

        <ul class="list">
            <li v-for="(item, index) in groups" :key="index">
                <button
                    class="btn btn-primary btn-sm mx-2"
                    @click="update(item, $event)"
                >
                    <i class="bi bi-pencil-square"></i>
                </button>
                {{ item.group }}
                <button
                    class="btn btn-secondary btn-sm mx-2"
                    @click="destroyGroup(item, $event)"
                >
                    <i class="bi bi-trash"></i>
                </button>
            </li>
        </ul>
    </div>
</template>
<script>
export default {
    data() {
        return {
            group: {},
            groups: {},
            errors: {},
            group_update: false,
            button: null,
        };
    },

    created() {
        this.getGroups();
    },

    mounted() {
        this.listenEvents();
    },

    methods: {
        createGroup(event) {
            this.button = event.target;
            this.button.disabled = true;

            this.$host
                .post("/api/groups", this.group)
                .then((res) => {
                    this.getGroups();
                    this.group = {};
                    this.button.disabled = false;
                })
                .catch((err) => {
                    this.button.disabled = false;
                    if (
                        err.response &&
                        err.response.status == 422 &&
                        err.response.data.errors
                    ) {
                        this.errors = err.response.data.errors;
                    } else {
                        this.errors.group = [err.response.data.message];
                    }
                });
        },

        getGroups() {
            this.$host
                .get("/api/groups", {
                    params: {
                        per_page: 100,
                        order_by: "nombre",
                    },
                })
                .then((res) => {
                    this.groups = res.data.data;
                })
                .catch((err) => {
                    if (err.response) {
                        console.log(err.response);
                    }
                });
        },

        update(item) {
            this.group_update = true;
            this.group = item;
        },

        updateGroup(event) {
            this.button = event.target;
            this.button.disabled = true;

            this.$host
                .put(this.group.links.update, this.group)
                .then((res) => {
                    this.group = {};
                    this.errors = {};
                    this.group_update = false;
                    this.getGroups();
                    this.button.disabled = false;
                })
                .catch((err) => {
                    this.button.disabled = false;
                    if (err.response && err.response.status == 422) {
                        this.errors = err.response.data.errors;
                    }
                });
        },

        destroyGroup(item, event) {
            this.button = event.target;
            this.button.disabled = true;

            this.$host
                .delete(item.links.destroy)
                .then((res) => {
                    this.errors = {};
                    this.group_update = false;
                    this.getGroups();
                    this.button.disabled = false;
                })
                .catch((err) => {
                    this.button.disabled = false;

                    if (err.response && err.response.status == 422) {
                        this.errors = err.response.data.errors;
                    }
                });
        },

        listenEvents() {
            this.$echo
                .private(this.$channels.ch_1(this.$id))
                .listen("StoreGroupEvent", (res) => {
                    this.getGroups();
                });

            this.$echo
                .private(this.$channels.ch_1(this.$id))
                .listen("UpdateGroupEvent", (res) => {
                    this.getGroups();
                });

            this.$echo
                .private(this.$channels.ch_1(this.$id))
                .listen("DestroyGroupEvent", (res) => {
                    this.getGroups();
                });
        },
    },
};
</script>
<style lang="scss" scoped>
.group-info {
    color: var(--code);
    .inputs {
        display: flex;
        flex-wrap: wrap;
        .item {
            flex: 1 1 auto;
            padding: 0.5em 0.2em;
            .btn {
                font-size: 0.8em;
            }
        }
    }

    .list {
        list-style: none;
        padding: 0;
        display: flex !important;
        flex-wrap: wrap !important;

        li {
            flex: 0 1 auto;
            font-size: 0.9em;
            border: 1px solid var(--light);
            margin-right: 1em;
            margin-bottom: 1em;
            padding: 0.4em;
            border-radius: 0.5em;

            .btn {
                font-size: 0.8em;
            }
        }
    }
}
</style>
