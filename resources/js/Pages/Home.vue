<template>
    <div>
        <button class="print btn btn-light btn-sm" @click="print">Print</button>
        <div class="table-box" id="table-box">
            <p for="" class="border-bottom h5">Contact list</p>
            <ul class="head">
                <li>Full Name</li>
                <li>Phone Number</li>
                <li>Email Address</li>
                <li>Group</li>
            </ul>
            <ul class="body" v-for="(item, index) in contacts" :key="index">
                <li>
                    <router-link
                        :to="{ name: 'contacts', params: { id: item.id } }"
                    >
                        {{ item.name }}
                        {{ item.last_name }}
                    </router-link>
                </li>
                <li>
                    {{ item.phones[0]["full_number"] }}
                </li>
                <li>
                    <a
                        :href="'mailto:' + item.emails[0]['email']"
                        target="_blank"
                    >
                        {{ item.emails[0]["email"] }}
                    </a>
                </li>
                <li>
                    {{ item.group_name }}
                </li>
            </ul>
        </div>
        <v-pagination
            v-show="pages.total > pages.per_page"
            :pages="pages"
            @changed-page="getContacts"
        ></v-pagination>
    </div>
</template>
<script>
export default {
    data() {
        return {
            contacts: {},
            form: {
                per_page: 100,
                page: 1,
            },
            pages: {},
            head: ["Full Name", "Number Phone", "Email Address", ""],
        };
    },

    created() {
        this.getContacts(1);
    },

    mounted() {
        this.listenEvents()    
    },

    methods: {
        addContacts() {
            this.$router.push({ name: "contacts" });
        },

        print() {
            var contenido = document.getElementById("table-box").innerHTML;
            var contenidoOriginal = document.body.innerHTML;

            document.body.innerHTML = contenido;
            document.body.classList.add("table-box");
            window.print();

            document.body.classList.remove("table-box");
            document.body.innerHTML = contenidoOriginal;
            window.close();
            location.reload();
        },

        showContact(id) {
            this.$router.push({
                name: "contacts",
                params: { id: id },
                replace: true,
            });
        },

        getContacts(id) {
            this.form.page = id;

            this.$host
                .get("/api/contacts", { params: this.form })
                .then((res) => {
                    this.contacts = res.data.data;
                    this.pages = res.data.meta.pagination;
                })
                .catch((err) => {});
        },

        listenEvents() {
            this.$echo
                .private(this.$channels.ch_1(this.$id))
                .listen("StoreContactEvent", (res) => {
                    this.getContacts();
                });

            this.$echo
                .private(this.$channels.ch_1(this.$id))
                .listen("UpdateContactEvent", (res) => {
                    this.getContacts();
                });

            this.$echo
                .private(this.$channels.ch_1(this.$id))
                .listen("DestroyContactEvent", (res) => {
                    this.getContacts();
                });
        },
    },
};
</script>
<style lang="scss" scoped>
.print {
    position: absolute;
    right: 2em;
    top: 5em;
}
.table-box {
    padding: 1em;
    color: var(--code);
    border-radius: 1em;
    .head {
        display: flex;
        list-style: none;
        padding: 0.2em 0.5em;
        border-bottom: 0.1em solid var(--secondary);
        li {
            flex: 1 1 calc(100% / 5);
            text-align: left;
            font-size: 0.9em;
            font-weight: bold;
        }
    }

    .body {
        display: flex;
        list-style: none;
        padding: 0em 0.5em;
        margin: 0.5em 0;
        li {
            flex: 1 1 calc(100% / 5);
            text-align: left;
            font-size: 0.9em;
        }
        a {
            text-decoration: none;
        }

        &:hover {
            background-color: var(--light);
            cursor: pointer;
        }
    }
}
</style>
