import { createApp } from "vue";
import { $server, $host } from "./config/axios";
import { $echo, $channels } from "./config/echo";
import { router } from "./config/rutes";
import { components } from "./config/globalComponents";

import App from "./App.vue";
import Login from "./Login/Login.vue";
import './config/matomo'

//bootstrap
import * as bootstrap from "bootstrap";

/**
 * Cheking routes
 */
router.beforeEach((to, from, next) => {
    if (to.meta.auth) {
        /**
         * if the user is authenticated
         */
        $server
            .get("/api/gateway/check-authentication")
            .then((res) => {
                next();
            })
            .catch((err) => {
                /**
                 * authenticated redirect to the login
                 */
                window.location.href = "/login";
            });
    } else {
        /**
         * is no auth route go to next() request
         */
        next();
    }
});

/**
 * Mounting App depending if the user is or not Authenticalble
 */
$server
    .get("/api/gateway/user")
    .then((res) => {
        /**
         * Global User Authenticated user
         */
        window.$auth = res.data;

        /**
         * Creating the Vue App
         */
        const app = createApp(App);

        /**
         * Global properties for vuejs
         */
        app.config.globalProperties.$server = $server;
        app.config.globalProperties.$host = $host;
        app.config.globalProperties.$echo = $echo;
        app.config.globalProperties.$channels = $channels;

        /**
         * Global components for Vuejs
         */
        components.forEach((index) => {
            app.component(index[0], index[1]);
        });

        /**
         * User routes using VUeRouter
         */
        app.use(router);

        app.mount("#app");
    })
    .catch((err) => {
        //Unauthenticable APP
        const app = createApp(Login);
        app.mount("#login");
    });
