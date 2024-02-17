import { createApp } from "vue";
import { $server, $host } from "./config/axios";
import { $echo, $channels } from "./config/echo";
import { router } from "./config/rutes";
import { components } from "./config/globalComponents";

import App from "./App.vue";
import * as bootstrap from "bootstrap";

$server
    .get("/api/gateway/user")
    .then((res) => {
        window.$id = res.data.id;

        const app = createApp(App);

        app.config.globalProperties.$server = $server;
        app.config.globalProperties.$host = $host;
        app.config.globalProperties.$echo = $echo;
        app.config.globalProperties.$channels = $channels;

        components.forEach((index) => {
            app.component(index[0], index[1]);
        });

        app.use(router);

        app.mount("#app");
    })
    .catch((err) => {
        if (err.response && err.response.status == 401) {
            //console.log(err.response.data);
        }
    });
