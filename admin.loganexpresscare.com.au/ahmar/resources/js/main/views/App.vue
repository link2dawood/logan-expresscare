<template>
    <router-view v-slot="{ Component, route }">
        <suspense>
            <template #default>
                <a-config-provider
                    :theme="{
                        algorithm: [
                            themeMode == 'dark'
                                ? theme.darkAlgorithm
                                : theme.defaultAlgorithm,
                        ],
                        token: {
                            colorPrimary: appSetting.primary_color,
                            fontFamily: 'Nunito,sans-serif',
                            borderRadius: 4,
                        },
                    }"
                    :direction="appSetting.rtl ? 'rtl' : 'ltr'"
                >
                    <div class="theme-container">
                        <ThemeProvider :theme="{ ...themeVars }">
                            <LoadingApp v-if="appChecking" />
                            <component v-else :is="Component" />
                        </ThemeProvider>
                    </div>
                </a-config-provider>
            </template>
            <template #fallback> <LoadingApp /> </template>
        </suspense>
    </router-view>
</template>

<script>
import { watch, onMounted, computed } from "vue";
import { theme } from "ant-design-vue";
import { ThemeProvider } from "vue3-styled-components";
import { themeVars } from "../config/theme/themeVariables";
import { useRoute, useRouter } from "vue-router";
import common from "../../common/composable/common";
import LoadingApp from "./LoadingApp.vue";
import { useAuthStore } from "../store/authStore";

export default {
    name: "App",
    components: {
        ThemeProvider,
        LoadingApp,
    },
    setup() {
        const authStore = useAuthStore();
        const route = useRoute();
        const router = useRouter();
        const darkTheme = "dark";
        const {
            updatePageTitle,
            appSetting,
            frontWarehouse,
            appType,
            themeMode,
        } = common();
        const appChecking = computed(() => authStore.appChecking);

        onMounted(() => {
            if (
                router.currentRoute &&
                router.currentRoute.value &&
                router.currentRoute.value.meta.isFrontStore == undefined
            ) {
                setInterval(() => {
                    authStore.refreshTokenAction();
                }, 10 * 60 * 1000);
            }
        });

        watch(route, (newVal, oldVal) => {
            const menuKey =
                typeof newVal.meta.menuKey == "function"
                    ? newVal.meta.menuKey(newVal)
                    : newVal.meta.menuKey;

            updatePageTitle(menuKey.replace("-", "_"));

            // Redirecting if plan is expired
            if (
                appType == "saas" &&
                appSetting.value.is_global == 0 &&
                appSetting.value.status == "license_expired" &&
                newVal &&
                newVal.meta &&
                !(
                    newVal.meta.menuParent == "subscription" ||
                    newVal.name == "admin.login" ||
                    newVal.name == "verify.main"
                )
            ) {
                router.push({
                    name: "admin.subscription.current_plan",
                });
            }
        });

        return {
            theme,
            themeVars,
            darkTheme,
            appChecking,
            appSetting,
            themeMode,
        };
    },
};
</script>

<style>
body {
    background: #f0f2f5 !important;
}
</style>
