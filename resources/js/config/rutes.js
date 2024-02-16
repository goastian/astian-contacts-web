import { createRouter, createWebHistory } from "vue-router";

import Home from "../Pages/Home.vue";
import VContact from "../Pages/Contacts/Index.vue";
import VGroup from "../Pages/Group.vue";

const routes = [
    { path: "/:group?", name: "home", component: Home },
    { path: "/contacts/:id?", name: "contacts", component: VContact },
    { path: "/groups/:id?", name: "groups", component: VGroup },
    ,
];

export const router = createRouter({
    history: createWebHistory(),
    routes,
});
