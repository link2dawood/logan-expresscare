<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.email_settings`)" class="p-0">
                <template #extra>
                    <a-space>
                        <a-button
                            type="primary"
                            @click="onSubmit"
                            :loading="settingRef && settingRef.loading ? true : false"
                        >
                            <template #icon> <SaveOutlined /> </template>
                            {{ $t("common.update") }}
                        </a-button>
                        <a-button
                            type="primary"
                            @click="
                                $router.push({
                                    name: `admin.settings.email_templates.index`,
                                })
                            "
                        >
                            <template #icon> <MailOutlined /> </template>
                            {{ $t("menu.email_templates") }}
                        </a-button>
                    </a-space>
                </template>
            </a-page-header>
        </template>
        <template #breadcrumb>
            <a-breadcrumb separator="-" style="font-size: 12px">
                <a-breadcrumb-item>
                    <router-link :to="{ name: 'admin.dashboard.index' }">
                        {{ $t(`menu.dashboard`) }}
                    </router-link>
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t("menu.settings") }}
                </a-breadcrumb-item>
                <a-breadcrumb-item>
                    {{ $t("menu.email_settings") }}
                </a-breadcrumb-item>
            </a-breadcrumb>
        </template>
    </AdminPageHeader>

    <a-row :gutter="16">
        <a-col
            :xs="24"
            :sm="24"
            :md="24"
            :lg="4"
            :xl="4"
            :style="{
                background: themeMode == 'dark' ? '#141414' : '#fff',
                borderRight:
                    themeMode == 'dark' ? '1px solid #303030' : '1px solid #f0f0f0',
                paddingRight: '0px',
            }"
        >
            <SettingSidebar />
        </a-col>
        <a-col :xs="24" :sm="24" :md="24" :lg="20" :xl="20">
            <admin-page-table-content>
                <EmailSettings ref="settingRef" />
            </admin-page-table-content>
        </a-col>
    </a-row>
</template>

<script>
import { ref } from "vue";
import { SaveOutlined, MailOutlined } from "@ant-design/icons-vue";
import SettingSidebar from "../SettingSidebar.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import EmailSettings from "../../common/settings/email/Edit.vue";
import Common from "../../../../common/composable/common";

export default {
    components: {
        SettingSidebar,
        AdminPageHeader,
        EmailSettings,
        SaveOutlined,
        MailOutlined,
    },
    setup() {
        const settingRef = ref(null);
        const { themeMode, appType } = Common();
        const routePrefix = appType == "non-saas" ? "admin" : "superadmin";

        const onSubmit = () => {
            settingRef.value.onSubmit();
        };

        return {
            onSubmit,
            settingRef,
            themeMode,
            routePrefix,
        };
    },
};
</script>
