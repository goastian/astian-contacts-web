<template>
    <div class="row p-0 mt-2 mx-0">
        <div class="col">
            <input
                type="text"
                class="form-control"
                placeholder="Group name"
                v-model="group.grupo"
            />
            <v-error :error="errors.grupo"></v-error>
        </div>
        <div class="col">
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
        <div class="col-12">
            <p class="list-title text-color border-bottom">List group</p>
            <span
                class="list-group bg-ternary text-color"
                v-for="(item, index) in groups"
                :key="index"
            >
                <button
                    class="btn btn-primary btn-sm mx-2"
                    @click="update(item, $event)"
                >
                    <i class="bi bi-pencil-square"></i>
                </button>
                {{ item.grupo }}
                <button
                    class="btn btn-secondary btn-sm mx-2"
                    @click="destroyGroup(item, $event)"
                >
                    <i class="bi bi-trash"></i>
                </button>
            </span>
        </div>
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
                    if (err.response && err.response.status == 422) {
                        this.errors = err.response.data.errors;
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
                .private(this.$channels.ch_1(window.$id))
                .listen("StoreGroupEvent", (res) => {
                    this.getGroups();
                });

            this.$echo
                .private(this.$channels.ch_1(window.$id))
                .listen("UpdateGroupEvent", (res) => {
                    this.getGroups();
                });

            this.$echo
                .private(this.$channels.ch_1(window.$id))
                .listen("DestroyGroupEvent", (res) => {
                    this.getGroups();
                });
        },
    },
};
</script>
<style lang="scss" scoped>
.col {
    flex: 0 0 auto;
    padding-top: 2%;

    @media (min-width: 320px) {
        width: 100%;
        margin: 0;
    }

    @media (min-width: 850px) {
        width: 48%;
        margin-left: 2%;
    }
}

.list-title {
    margin-left: 2%;
    margin-top: 2%;
}

.list-group {
    display: inline-block !important;
    margin-right: 2%;
    margin-top: 0.6%;
    padding: 0.5%;
    margin-left: 2%;

    @media (min-width: 320px) {
    }

    @media (min-width: 850px) {
    }
}
</style>
