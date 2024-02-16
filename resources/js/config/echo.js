import EchoClient from "echo-client-js";
import Cookies from "js-cookie";

const options = {
    host: process.env.MIX_ECHO_SERVER,
    port: process.env.MIX_ECHO_SERVER_PORT,
    transport: process.env.MIX_ECHO_SERVER_PROTOCOL,
    headers: {
        Authorization: Cookies.get(process.env.MIX_ECHO_TOKEN),
    },
};

export const $echo = new EchoClient(options);

export const $channels = {
    ch_0() {
        return process.env.MIX_ECHO_CHANNEL;
    },

    ch_1(id) {
        return `${process.env.MIX_ECHO_CHANNEL}.${id}`;
    },
};
