<template>
    <div class="row">
        <div class="col">
            <input
                type="text"
                class="form-control form-control-sm text-color"
                placeholder="Name"
                v-model="site.app"
            />
            <v-error :error="errors.app"></v-error>
        </div>
        <div class="col">
            <input
                type="text"
                class="form-control form-control-sm text-color"
                placeholder="https://mysite.com"
                v-model="site.link"
            />
            <v-error :error="errors.link"></v-error>
        </div>
        <div class="col">
            <button
                class="btn btn-primary btn-sm"
                v-show="!site_update"
                @click="addSite"
            >
                Add Social network
                <i class="bi bi-globe-americas mx-2"></i>
            </button>

            <button
                class="btn btn-ternary"
                v-show="site_update"
                @click="updateSite()"
            >
                Update Social network
                <i class="bi bi-globe-americas btn-sm mx-2"></i>
            </button>
        </div>

        <div class="col-12 pt-2">
            <p v-for="(item, index) in sites" :key="index">
                <strong class="text-color">
                    {{ item.app }}
                </strong>
                <span class="text-color mx-4">
                    {{ item.link }}
                </span>
                <button
                    class="btn btn-ternary btn-sm mx-4"
                    @click="update(item)"
                >
                    <i class="bi bi-pencil-square text-color"></i>
                </button>
                <button
                    class="btn btn btn-sm btn-secondary"
                    @click="destroySite(item.links.destroy)"
                >
                    <i class="bi bi-trash text-color"></i>
                </button>
            </p>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            site: {},
            sites: {},
            errors: {},
            site_update: false,
            site_request: null,
        };
    },

    created() {
        this.getUri();
    },

    mounted() {
        if (this.$route.params.id) {
            this.getSites();
        }
        this.listenEvents();
    },

    watch: {
        $route(to, from) {
            if (to.params.id) {
                this.registered = true;
                this.getUri();
                this.getSites();
            }
        },
    },

    methods: {
        /**
         * get the actual uri
         */
        getUri() {
            if (this.$route.params.id) {
                this.site_request =
                    "/api/contacts/" + this.$route.params.id + "/apps";
            }
        },

        update(item) {
            this.site = item;
            this.errors = {};
            this.site_update = true;
        },

        getSites() {
            this.$host
                .get(this.site_request)
                .then((res) => {
                    this.sites = res.data.data;
                })
                .catch((err) => {
                    if (err.response && err.response.status) {
                        console.log(err.response);
                    }
                });
        },

        addSite() {
            this.getUri();
            this.$host
                .post(this.site_request, this.site)
                .then((res) => {
                    this.errors = {};
                    this.site = {};
                    this.getSites();
                })
                .catch((err) => {
                    if (err.response && err.response.status) {
                        this.errors = err.response.data.errors;
                    }
                });
        },

        showSite(link) {
            this.$host
                .get(link)
                .then((res) => {
                    this.site = res.data.data;
                })
                .catch((err) => {
                    if (err.response && err.response.status) {
                        console.log(err.response);
                    }
                });
        },

        updateSite() {
            this.$host
                .put(this.site.links.update, this.site)
                .then((res) => {
                    (this.site_update = false), (this.errors = {});
                    this.site = {};
                    this.getSites();
                })
                .catch((err) => {
                    if (err.response && err.response.status) {
                        this.errors = err.response.data.errors;
                    }
                });
        },

        destroySite(link) {
            this.$host
                .delete(link)
                .then((res) => {
                    this.site_update = false;
                    this.getSites();
                })
                .catch((err) => {
                    if (err.response && err.response.status) {
                        console.log(err.response);
                    }
                });
        },

        listenEvents() {
            this.$echo
                .private(this.$channels.ch_1(window.$id))
                .listen("StoreAppEvent", (res) => {
                    this.getPhones();
                });

            this.$echo
                .private(this.$channels.ch_1(window.$id))
                .listen("UpdateAppEvent", (res) => {
                    this.getPhones();
                });

            this.$echo
                .private(this.$channels.ch_1(window.$id))
                .listen("DestroyAppEvent", (res) => {
                    this.getPhones();
                });
        },
    },
};
</script>

<style lang="scss" scoped>
.col {
    flex: 0 0 auto;

    @media (min-width: 320px) {
        width: 98%;
        margin-top: 2%;
    }

    @media (min-width: 940px) {
        width: 30%;
    }
}
</style>
