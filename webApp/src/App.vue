<script setup>
    import { AppearanceProvider, LocaleProvider, AuthProvider } from 'tele-vue-lib';
    import 'tele-vue-lib/style.css';

    const devMode = (import.meta.env.MODE === 'development') && true;
    const hex_hmac_signature = import.meta.env.VITE_HEX_HMAC_SIGNATURE;
</script>

<template>
    <AuthProvider :hex_hmac_signature="hex_hmac_signature">
        <AppearanceProvider>
            <LocaleProvider>
                <router-view></router-view>
            </LocaleProvider>
        </AppearanceProvider>

        <template #unauthorized>
            Your client is not authorized, please use this WebApp from a valid Telegram Client
        </template>
    </AuthProvider>

    <!-- Include liriliri/eruda developer console if we are in Development Mode -->
    <teleport to="head" v-if="devMode">
        <component is="script" v-if="devMode" src="node_modules/eruda/eruda.js"></component>
        
        <component is="script" v-if="devMode">
            let timer = setInterval(() => {

                if (typeof eruda === 'object') {

                    clearInterval(timer);
                    eruda.init();

                }

            }, 250);
        </component>
    </teleport>
</template>

<style lang="scss">

    body {
        background-color: var(--tg-theme-bg-color);
        color: var(--tg-theme-text-color);
    }

</style>