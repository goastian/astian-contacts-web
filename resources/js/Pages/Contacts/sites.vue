<template>
    <div class="sites-info">
        <p class="fw-bold text-color m-0">Add new web site</p>
        <div class="inputs">
            <div class="items">
                <input
                    type="text"
                    class="input-theme"
                    placeholder="Name"
                    v-model="site.app"
                />
                <v-error :error="errors.app"></v-error>
            </div>
            <div class="items">
                <input
                    type="text"
                    class="input-theme"
                    placeholder="https://mysite.com"
                    v-model="site.link"
                />
                <v-error :error="errors.link"></v-error>
            </div>
            <div class="items">
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
        </div>

        <ul class="list" v-for="(item, index) in sites" :key="index">
            <li>{{ item.app }}</li>
            <li>
                <a :href="item.uri" target="_blank">
                    {{ item.uri }}
                </a>
            </li>
            <li>
                <button
                    class="btn btn-ternary btn-sm mx-4"
                    @click="update(item)"
                >
                    <i class="bi bi-pencil-square text-color"></i>
                </button>
            </li>
            <li>
                <button
                    class="btn btn btn-sm btn-secondary"
                    @click="destroySite(item.links.destroy, $event)"
                >
                    <i class="bi bi-trash text-color"></i>
                </button>
            </li>
        </ul>
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
            button: null,
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

        addSite(event) {
            this.button = event.target;
            this.button.disabled = true;

            this.getUri();
            this.$host
                .post(this.site_request, this.site)
                .then((res) => {
                    this.errors = {};
                    this.site = {};
                    this.getSites();
                    this.button.disabled = false;
                })
                .catch((err) => {
                    this.button.disabled = false;
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

        updateSite(event) {
            this.button = event.target;
            this.button.disabled = true;

            this.$host
                .put(this.site.links.update, this.site)
                .then((res) => {
                    (this.site_update = false), (this.errors = {});
                    this.site = {};
                    this.getSites();
                    this.button.disabled = false;
                })
                .catch((err) => {
                    this.button.disabled = false;
                    if (err.response && err.response.status) {
                        this.errors = err.response.data.errors;
                    }
                });
        },

        destroySite(link, event) {
            this.button = event.target;
            this.button.disabled = true;

            this.$host
                .delete(link)
                .then((res) => {
                    this.site_update = false;
                    this.getSites();
                    this.button.disabled = false;
                })
                .catch((err) => {
                    this.button.disabled = false;
                    if (err.response && err.response.status) {
                    }
                });
        },

        listenEvents() {
            this.$echo
                .private(this.$channels.ch_1(this.$id))
                .listen("StoreAppEvent", (res) => {
                    this.getPhones();
                });

            this.$echo
                .private(this.$channels.ch_1(this.$id))
                .listen("UpdateAppEvent", (res) => {
                    this.getPhones();
                });

            this.$echo
                .private(this.$channels.ch_1(this.$id))
                .listen("DestroyAppEvent", (res) => {
                    this.getPhones();
                });
        },
    },
};
</script>

<style lang="scss" scoped>
.sites-info {
    padding: 0.5em;
    font-size: 0.8em;
    background-color: var(--light);
    border-radius: 1em;

    @media (min-width: 800px) {
        font-size: 0.9em;
    }
    .inputs {
        display: flex;
        flex-wrap: wrap;
        .items {
            flex: 1 0 100%;
            margin: 0.3em 0;
            @media (min-width: 800px) {
                flex: 1 1 calc(100% / 3);
                padding-right: 0.2em;
            }
        }
    }

    .list {
        display: flex;
        list-style: none;
        padding: 0;
        li {
            flex: 0 1 auto;
            padding: 0.1em;
            @media (min-width: 800px) {
                padding-right: 2em;
                padding-top: 0.5em;
            }
        }
    }
}
</style>
