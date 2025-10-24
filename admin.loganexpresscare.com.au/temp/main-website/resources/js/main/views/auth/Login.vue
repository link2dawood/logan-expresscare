<template>
    <div
        :style="
            themeMode == 'dark'
                ? { background: '#141414', height: '100vh' }
                : { background: '#fff', height: '100vh' }
        "
    >
        <a-row class="main-container-div">
            <a-col
                :xs="24"
                :sm="24"
                :md="24"
                :lg="8"
                :style="{
                    background: themeMode == 'dark' ? '#141414' : '#fff',
                }"
            >
                <a-row class="login-left-div">
                    <a-col
                        :xs="{ span: 20, offset: 2 }"
                        :sm="{ span: 20, offset: 2 }"
                        :md="{ span: 16, offset: 4 }"
                        :lg="{ span: 16, offset: 4 }"
                    >
                        <a-card
                            :title="null"
                            class="login-div"
                            :bordered="innerWidth <= 768 ? true : false"
                        >
                            <a-form layout="vertical">
                                <div class="login-logo mb-30">
                                    <img
                                        class="login-img-logo"
                                        :src="globalSetting.light_logo_url"
                                    />
                                </div>
                                <a-alert
                                    v-if="onRequestSend.error != ''"
                                    :message="onRequestSend.error"
                                    type="error"
                                    show-icon
                                    class="mb-20 mt-10"
                                />
                                <a-alert
                                    v-if="onRequestSend.success"
                                    :message="$t('messages.login_success')"
                                    type="success"
                                    show-icon
                                    class="mb-20 mt-10"
                                />
                                <a-form-item
                                    :label="$t('user.email_phone')"
                                    name="email"
                                    :help="rules.email ? rules.email.message : null"
                                    :validateStatus="rules.email ? 'error' : null"
                                >
                                    <a-input
                                        v-model:value="credentials.email"
                                        @pressEnter="onSubmit"
                                        :placeholder="
                                            $t('common.placeholder_default_text', [
                                                $t('user.email_phone'),
                                            ])
                                        "
                                    />
                                </a-form-item>

                                <a-form-item
                                    :label="$t('user.password')"
                                    name="password"
                                    :help="rules.password ? rules.password.message : null"
                                    :validateStatus="rules.password ? 'error' : null"
                                >
                                    <a-input-password
                                        v-model:value="credentials.password"
                                        @pressEnter="onSubmit"
                                        :placeholder="
                                            $t('common.placeholder_default_text', [
                                                $t('user.password'),
                                            ])
                                        "
                                    />
                                </a-form-item>

                                <a-form-item class="mt-30">
                                    <a-button
                                        :loading="loading"
                                        @click="onSubmit"
                                        class="login-btn"
                                        block
                                    >
                                        {{ $t("menu.login") }}
                                    </a-button>
                                </a-form-item>
                            </a-form>
                            <DemoCredentials :credentials="credentials" />
                        </a-card>
                    </a-col>
                </a-row>
            </a-col>
            <a-col :xs="0" :sm="0" :md="24" :lg="16">
                <div class="right-login-div">
                    <img class="right-image" :src="loginBackground" />
                </div>
            </a-col>
        </a-row>
    </div>
</template>

<script>
import { defineComponent, reactive, ref } from "vue";
import { useAuthStore } from "../../store/authStore";
import { useRouter } from "vue-router";
import common from "../../../common/composable/common";
import apiAdmin from "../../../common/composable/apiAdmin";
import DemoCredentials from "./DemoCredentials.vue";

export default defineComponent({
    components: {
        DemoCredentials,
    },
    setup() {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { globalSetting, appType, themeMode } = common();
        const loginBackground = globalSetting.value.login_image_url;
        const authStore = useAuthStore();
        const router = useRouter();
        const credentials = reactive({
            email: null,
            password: null,
        });
        const onRequestSend = ref({
            error: "",
            success: "",
        });

        const onSubmit = () => {
            onRequestSend.value = {
                error: "",
                success: false,
            };

            addEditRequestAdmin({
                url: "auth/login",
                data: credentials,
                success: (response) => {
                    const user = response.user;
                    authStore.updateUser(user);
                    authStore.updateToken(response.token);
                    authStore.updateExpires(response.expires_in);
                    authStore.updateVisibleSubscriptionModules(
                        response.visible_subscription_modules
                    );
                    authStore.updateEmailVariables(response.email_variables);

                    if (appType == "non-saas") {
                        if (
                            response.user.role &&
                            response.user.is_manager === 1 &&
                            response.user.role.xid != null
                        ) {
                            router.push({
                                name: "admin.dashboard.index",
                                params: { success: true },
                            });
                        } else {
                            router.push({
                                name: "admin.self.dashboard.index",
                                params: { success: true },
                            });
                        }
                    } else {
                        if (user.is_superadmin && user.user_type == "super_admins") {
                            authStore.updateApp(response.app);
                            authStore.updateEmailVerifiedSetting(
                                response.email_setting_verified
                            );

                            router.push({
                                name: "superadmin.dashboard.index",
                                params: { success: true },
                            });
                        } else {
                            authStore.updateApp(response.app);
                            authStore.updateEmailVerifiedSetting(
                                response.email_setting_verified
                            );
                            authStore.updateAddMenus(response.shortcut_menus.credentials);

                            router.push({
                                name: "admin.dashboard.index",
                                params: { success: true },
                            });
                        }
                    }
                },
                error: (err) => {
                    onRequestSend.value = {
                        error: err.error.message ? err.error.message : "",
                        success: false,
                    };
                },
            });
        };

        return {
            themeMode,
            loading,
            rules,
            credentials,
            onSubmit,
            onRequestSend,
            globalSetting,
            loginBackground,

            innerWidth: window.innerWidth,
        };
    },
});
</script>

<style lang="less">
.login-main-container {
    background: #fff;
    height: 100vh;
}

.main-container-div {
    height: 100%;
}

.login-left-div {
    height: 100%;
    align-items: center;
}

.login-logo {
    text-align: center;
}

.login-img-logo {
    width: 150px;
    display: inline-flex;
}

.container-content {
    margin-top: 100px;
}

.login-div {
    border-radius: 10px;
}

.outer-div {
    margin: 0;
}

.right-login-div {
    background: #f8f8ff;
    height: 100%;
    display: flex;
    align-items: center;
}

.right-image {
    width: 100%;
    display: block;
    margin: 0 auto;
}

.login-btn,
.login-btn:hover,
.login-btn:active {
    background: #5254cf !important;
    border-color: #5254cf !important;
    border-radius: 5px;
    color: #fff !important;
}
</style>
