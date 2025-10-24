import { defineStore } from "pinia";
import { ref, computed } from "vue";
import moment from "moment";
import router from "../router";

const AUTH_USER = "auth_user";
const AUTH_TOKEN = "auth_token";
const EXIPRES_KEY = "expire_key";
const APP_SETTINGS_KEY = "app_settings";
const GLOBAL_SETTINGS_KEY = "global_settings";
const PER_PAGE_ITEM = "per_page_item";
const ALL_LANGS = "all_langs";
const SELECTED_LANG = "selected_lang";
const PAGE_TITLE = "page_title";
const DARK_THEME = "dark_theme";
const ACTIVE_MODULES = "active_modules";
const CSS_SETTINGS = "css_settings";
const ADD_MENUS = "add_menus";
const EMAIL_SETTING_VERIFIED = "email_setting_verified";
const EMAIL_VARIABLES = "email_variables";
const VISIBLE_SUBSCRIPTION_MODULES = "visible_subscription_modules";
const THEME_MODE = "theme_mode";

import {
    getValueFromStorage,
    getValueFromJsonStorage,
    setValueInStorage,
    setJSONValueInStorage,
} from "./storage";

moment.suppressDeprecationWarnings = true;

const getJSONFromLocalStorage = (key) => {
    const value = window.localStorage.getItem(key);
    return value ? JSON.parse(value) : null;
};

export const useAuthStore = defineStore("auth", () => {
    const user = ref(getValueFromJsonStorage(AUTH_USER));
    const allLangs = ref(getValueFromJsonStorage(ALL_LANGS, []));

    const lang = ref(getValueFromStorage(SELECTED_LANG, "en"));
    const token = ref(getValueFromStorage(AUTH_TOKEN, null));
    const expires = ref(getValueFromStorage(EXIPRES_KEY, null));

    const globalSetting = ref(getValueFromJsonStorage(GLOBAL_SETTINGS_KEY));
    const appSetting = ref(getValueFromJsonStorage(APP_SETTINGS_KEY));
    const addMenus = ref(getValueFromJsonStorage(ADD_MENUS, []));
    const visibleSubscriptionModules = ref(
        getValueFromJsonStorage(VISIBLE_SUBSCRIPTION_MODULES, [])
    );

    const perPage = ref(getValueFromStorage(PER_PAGE_ITEM, window.config.path));
    const pageTitle = ref(getValueFromStorage(PAGE_TITLE, ""));

    const darkTheme = ref(getValueFromJsonStorage(DARK_THEME));
    const activeModules = ref(getValueFromJsonStorage(ACTIVE_MODULES));
    const cssSettings = ref(
        getValueFromJsonStorage(CSS_SETTINGS, {
            leftRightPadding: "50px",
            headerMenuMode: "vertical",
        })
    );
    const appChecking = ref(true);
    const emailSettingVerified = ref(
        getValueFromStorage(EMAIL_SETTING_VERIFIED, false)
    );
    const emailVariables = ref(
        getValueFromJsonStorage(EMAIL_VARIABLES, [])
    );
    const menuCollapsed = ref(window.innerWidth <= 991);
    const themeMode = ref(getValueFromStorage(THEME_MODE, "light"));

    const isLoggedIn = computed(() => {
        if (token.value === null || token.value === "") {
            return false;
        } else {
            return moment(expires.value).isAfter(moment());
        }
    });

    const updateUser = (newVal) => {
        user.value = newVal;

        setJSONValueInStorage(AUTH_USER, newVal);
    };

    const updateAppChecking = (newVal) => {
        appChecking.value = newVal;
    };

    const updateToken = (newVal) => {
        token.value = newVal;
        setValueInStorage(AUTH_TOKEN, newVal);

        // Setting up auth bearer token to axios
        axiosAdmin.defaults.headers.common[
            "Authorization"
        ] = `Bearer ${newVal}`;

        axiosPdf.defaults.headers.common[
            "Authorization"
        ] = `Bearer ${newVal}`;
    };

    const updateExpires = (newVal) => {
        expires.value = newVal;
        setValueInStorage(EXIPRES_KEY, newVal);
    };

    const updateApp = (newVal) => {
        appSetting.value = newVal;
        setJSONValueInStorage(APP_SETTINGS_KEY, newVal);
    };

    const updateGlobalSetting = (newVal) => {
        globalSetting.value = newVal;
        setJSONValueInStorage(GLOBAL_SETTINGS_KEY, newVal);
    };

    const updateAddMenus = (newVal) => {
        addMenus.value = newVal;
        setJSONValueInStorage(ADD_MENUS, newVal);
    };

    const updatePerPage = (newVal) => {
        perPage.value = newVal;
        setValueInStorage(PER_PAGE_ITEM, newVal);
    };

    const updateLang = (newVal) => {
        lang.value = newVal;
        setValueInStorage(SELECTED_LANG, newVal);
    };

    const updatePageTitle = (newVal) => {
        pageTitle.value = newVal;
        setValueInStorage(PAGE_TITLE, newVal);

        if (appSetting.value !== null && appSetting.value !== undefined) {
            document.title = `${newVal} - ${appSetting.value.name}`;
        } else {
            document.title = `${newVal} - Hrmifly`;
        }
    };

    const updateDarkTheme = (newVal) => {
        darkTheme.value = newVal;
        setValueInStorage(DARK_THEME, newVal);
    };

    const updateActiveModules = (newVal) => {
        activeModules.value = newVal;
        setJSONValueInStorage(ACTIVE_MODULES, newVal);
    };

    const updateCssSettings = (newVal) => {
        cssSettings.value = newVal;
        setJSONValueInStorage(CSS_SETTINGS, newVal);
    };

    const updateAllLangs = (newVal) => {
        allLangs.value = newVal;

        setJSONValueInStorage(APP_LANG, newVal);
    };

    const updateMenuCollapsed = (newVal) => {
        menuCollapsed.value = newVal;
    };

    const updateEmailVerifiedSetting = (newVal) => {
        emailSettingVerified.value = newVal;

        setJSONValueInStorage(EMAIL_SETTING_VERIFIED, newVal);
    };

    const updateEmailVariables = (newVal) => {
        emailVariables.value = newVal;

        setJSONValueInStorage(EMAIL_VARIABLES, newVal);
    };

    const updateVisibleSubscriptionModules = (newVal) => {
        visibleSubscriptionModules.value = newVal;

        setJSONValueInStorage(VISIBLE_SUBSCRIPTION_MODULES, newVal);
    };

    const updateThemeMode = (newVal) => {
        themeMode.value = newVal;

        setJSONValueInStorage(THEME_MODE, newVal);
    };

    // actions
    const updateUserAction = () => {
        return axiosAdmin
            .post(`/user`)
            .then(function (response) {
                updateUser(response.data.user);
            })
            .catch(function (error) { });
    };

    const updateGlobalSettingAction = () => {
        return axiosAdmin
            .get("/global-setting")
            .then(function (response) {
                updateGlobalSetting(response.data.global_setting);

                // Changing favicon
                var faviconLink = document.querySelector("link[rel~='icon']");
                faviconLink.href = globalSetting.value.small_light_logo_url;
            })
            .catch(function (error) { });
    };

    const updateAppAction = () => {
        axiosAdmin
            .get("/app")
            .then(function (response) {
                updateApp(response.data.app);
                updateAddMenus(response.data.shortcut_menus.credentials);
                updateEmailVerifiedSetting(
                    response.data.email_setting_verified
                );
                updateEmailVariables(
                    response.data.email_variables
                );

                // Changing favicon
                var faviconLink = document.querySelector("link[rel~='icon']");
                faviconLink.href = response.data.app.small_light_logo_url;
            })
            .catch(function (error) { });
    };

    const updateAllLangsAction = () => {
        return axiosAdmin
            .get("/all-langs")
            .then(function (response) {
                updateAllLangs(response.data.langs);
            })
            .catch(function (error) { });
    };

    const refreshTokenAction = () => {
        if (
            token.value !== "" &&
            token.value !== "undefined" &&
            token.value != "null" &&
            token.value != null
        ) {
            axiosAdmin
                .post(`/auth/refresh-token?token=${token.value}`)
                .then(function (response) {
                    updateUser(response.data.user);
                    updateToken(response.data.token);
                    updateExpires(response.data.expires_in);
                })
                .catch(function (error) { });
        }
    };

    const logoutAction = () => {
        axiosAdmin
            .post("/auth/logout")
            .then(function (response) {
                window.sessionStorage.clear();

                updateUser([]);
                updateToken(null);
                updateExpires(null);
                updateEmailVerifiedSetting(0);

                router.push({
                    name: "admin.login",
                });
            })
            .catch(function (error) { });
    };

    const logoutToRootUrlAction = () => {
        axiosAdmin
            .post("/auth/logout")
            .then(function (response) {
                updateUser([]);
                updateToken(null);
                updateExpires(null);

                window.location.href = window.config.path;
            })
            .catch(function (error) { });
    };

    const updateVisibleSubscriptionModulesAction = () => {
        return axiosAdmin
            .post("/visible-subscription-modules")
            .then(function (response) {
                updateVisibleSubscriptionModules(response.data);
            })
            .catch(function (error) { });
    };

    return {
        user,
        allLangs,
        lang,
        token,
        expires,
        globalSetting,
        appSetting,
        addMenus,
        visibleSubscriptionModules,
        perPage,
        pageTitle,
        darkTheme,
        activeModules,
        cssSettings,
        appChecking,
        emailSettingVerified,
        emailVariables,
        menuCollapsed,
        themeMode,

        isLoggedIn,
        updateUser,
        updateAppChecking,
        updateToken,
        updateExpires,
        updateApp,
        updateGlobalSetting,
        updateAddMenus,
        updatePerPage,
        updateLang,
        updatePageTitle,
        updateDarkTheme,
        updateActiveModules,
        updateCssSettings,
        updateAllLangs,
        updateMenuCollapsed,
        updateEmailVerifiedSetting,
        updateEmailVariables,
        updateVisibleSubscriptionModules,
        updateThemeMode,

        updateUserAction,
        updateGlobalSettingAction,
        updateAppAction,
        updateAllLangsAction,
        refreshTokenAction,
        logoutAction,
        logoutToRootUrlAction,
        updateVisibleSubscriptionModulesAction,
    };
});
