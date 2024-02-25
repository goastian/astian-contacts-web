<template>
    <aside class="side">
        <ul>
            <li>
                <router-link :to="{ name: 'home' }" @click="getContacts">
                    <i class="bi bi-people-fill mx-2"></i> Home
                </router-link>
            </li>

            <li>
                <router-link :to="{ name: 'contacts' }" @click="getContacts">
                    <i class="bi bi-person-lines-fill mx-2"></i> Contacts
                </router-link>
            </li>
            <li>
                <router-link :to="{ name: 'groups' }" @click="getGroups">
                    <i class="bi bi-collection my mx-2"></i> Groups
                </router-link>
            </li>
            <li>
                <a href="#contacts" @click="getFavotires()">
                    <i class="bi bi-star mx-2"></i> Favorites
                </a>
            </li>
        </ul>

        <ul class="contacts" v-show="show_contacts">
            <li v-for="(item, index) in contacts" :key="index">
                <router-link
                    :to="{ name: 'contacts', params: { id: item.id } }"
                    @click="isClicked"
                >
                    <i class="bi bi-person-lines-fill mx-2"></i>
                    {{ item.nombre }} {{ item.apellido }}
                    <i v-show="item.favorito" class="bi bi-star-fill mx-2"></i>
                </router-link>
            </li>
        </ul>

        <ul class="favorites" v-show="show_favorites">
            <li v-for="(item, index) in favorites" :key="index">
                <router-link
                    :to="{ name: 'contacts', params: { id: item.id } }"
                    @click="isClicked"
                >
                    <i class="bi bi-heart mx-2"></i>
                    {{ item.nombre }} {{ item.apellido }}
                </router-link>
            </li>
        </ul>

        <ul class="groups" v-show="show_groups">
            <li v-for="(item, index) in groups" :key="index" @click="isClicked">
                <a href="#" class="text-color">
                    <i class="bi bi-record-circle-fill mx-2"></i>
                    {{ item.grupo }}
                </a>
            </li>
        </ul>
    </aside>
</template>
<script>
export default {
    emits: ["selectedMenu"],

    data() {
        return {
            tags: {},
            app_name: process.env.MIX_APP_NAME,
            contacts: {},
            favorites: {},
            groups: {},
            show_groups: false,
            show_contacts: true,
            show_favorites: false,
        };
    },

    created() {
        this.getContacts();
    },

    mounted() {
        window.addEventListener("resize", this.screenIsChanging);
        this.screenIsChanging();
        this.listenEvents();
    },

    methods: {
        screenIsChanging() {
            if (window.innerWidth < 940) {
                this.$emit("selectedMenu", window.innerWidth < 940);
            }
        },

        isClicked() {
            if (window.innerWidth < 940) {
                this.$emit("selectedMenu", window.innerWidth < 940);
            }
        },

        getContacts() {
            this.show_contacts = true;
            this.show_favorites = false;
            this.show_groups = false;
            this.isClicked();
            this.$host
                .get("/api/contacts", {
                    params: { per_page: 100, order_by: "nombre" },
                })
                .then((res) => {
                    this.contacts = res.data.data;
                })
                .catch((err) => {
                    if (err.response) {
                        console.log(err.response);
                    }
                });
        },

        getGroups() {
            this.show_contacts = false;
            this.show_favorites = false;
            this.show_groups = true;
            this.isClicked();

            this.$host
                .get("/api/groups")
                .then((res) => {
                    this.groups = res.data.data;
                })
                .catch((err) => {
                    if (err.response) {
                        console.log(err.response);
                    }
                });
        },

        getFavotires() {
            this.show_contacts = false;
            this.show_favorites = true;
            this.show_groups = false;
            this.$host
                .get("/api/contacts", {
                    params: {
                        per_page: 25,
                        favorito: 1,
                        order_by: "nombre",
                    },
                })
                .then((res) => {
                    this.favorites = res.data.data;
                })
                .catch((err) => {
                    if (err.response) {
                        console.log(err.response);
                    }
                });
        },

        listenEvents() {
            this.$echo
                .private(this.$channels.ch_1(window.$id))
                .listen("StoreContactEvent", (e) => {
                    this.getContacts();
                });

            this.$echo
                .private(this.$channels.ch_1(window.$id))
                .listen("UpdateContactEvent", (e) => {
                    this.getContacts();
                });

            this.$echo
                .private(this.$channels.ch_1(window.$id))
                .listen("DestroyContactEvent", (e) => {
                    this.getContacts();
                });

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

<style scoped lang="scss">
.side {
    width: 100%;
    min-height: 100vh;
}

.side ul:nth-child(1) {
    padding-left: 1%;
    list-style: none;
    border-bottom: 2px solid var(--ternary);
    padding-bottom: 4%;
}
.side ul:nth-child(1) li {
    padding: 2%;
}

.side .contacts {
    list-style: none;
    padding-left: 5%;
}

.side .favorites {
    list-style: none;
    padding-left: 5%;
}

.side .groups {
    list-style: none;
    padding-left: 5%;
}
.side a {
    color: var(--code);
}
</style>
