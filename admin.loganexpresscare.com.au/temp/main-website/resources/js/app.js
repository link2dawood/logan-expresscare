import "./common/plugins";
import "../less/pos_invoice.css";
import "../less/tailwind.css";
import 'leaflet/dist/leaflet.css';
import "../less/custom.less";
import "ant-design-vue/dist/reset.css";

import { createApp, defineAsyncComponent } from "vue";
import PerfectScrollbar from "vue3-perfect-scrollbar";
import App from "./main/views/App.vue";
import routes from "./main/router";
import { setupI18n, loadLocaleMessages } from "./common/i18n";

import "vue3-perfect-scrollbar/dist/vue3-perfect-scrollbar.css";

// Lazy Load Components
const AdminPageFilter = defineAsyncComponent(() =>
    import("./common/layouts/AdminPageFilters.vue")
);
const AdminPageTableContent = defineAsyncComponent(() =>
    import("./common/layouts/AdminPageTableContent.vue")
);
const UserViewPage = defineAsyncComponent(() =>
    import("./main/views/staff-members/users/UserViewPage.vue")
);

import { createPinia } from "pinia";
import { useAuthStore } from "./main/store/authStore";

async function bootstrap() {

    const app = createApp(App);
    app.use(createPinia());
    const authStore = useAuthStore();

    // Prepare auth tasks (wrap in Promise.resolve to support sync or async)
    const authTasks = [];

    if (authStore.isLoggedIn) {
        authTasks.push(Promise.resolve(authStore.updateUserAction()));
    }

    authTasks.push(
        Promise.resolve(authStore.updateGlobalSettingAction()),
        Promise.resolve(authStore.updateAppAction()),
        Promise.resolve(authStore.updateAllLangsAction()),
        Promise.resolve(authStore.updateVisibleSubscriptionModulesAction())
    );

    // Synchronous call
    authStore.updateActiveModules(window.config.modules);

    // Wait for all to complete
    await Promise.all(authTasks);

    const i18n = setupI18n({
        legacy: false,
        globalInjection: true,
        locale: authStore.lang,
        warnHtmlMessage: false,
    });
    await loadLocaleMessages(i18n, authStore.lang);

    app.use(i18n);
    app.use(PerfectScrollbar);

    const allModules = window.config.installed_modules;
    const allModulesPromise = [];
    allModules.forEach((allModule) => {
        const moduleName = allModule.verified_name;
        const pluginModule =
            import(
                `../../Modules/${moduleName}/Resources/assets/js/${moduleName}.js`
            );
        allModulesPromise.push(pluginModule);
    });
    Promise.all(allModulesPromise).then((allModulesPromiseResponse) => {
        allModulesPromiseResponse.forEach((allModulesPromiseResponseData) => {
            app.use(allModulesPromiseResponseData.default, { routes, authStore });
        });

        routes.addRoute({
            path: "/:catchAll(.*)",
            redirect: "/",
        });

        // Adding routes after plugins routes
        app.use(routes);
    });

    // Global Components
    app.component("admin-page-filters", AdminPageFilter);
    app.component("admin-page-table-content", AdminPageTableContent);
    app.component("user-view-page", UserViewPage);

    window.i18n = i18n;

    routes.isReady().then(() => {
        app.mount("#app");
    });
}

bootstrap();
