<template>
    <AdminPageHeader>
        <template #header>
            <a-page-header :title="$t(`menu.email_settings`)" class="p-0">
                <template #extra>
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

    <a-row>
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
            }"
        >
            <SettingSidebar />
        </a-col>
        <a-col :xs="24" :sm="24" :md="24" :lg="20" :xl="20">
            <admin-page-table-content>
                <SendMailSetting
                    v-if="sendMailSettings && sendMailSettings.xid"
                    :sendMailSettings="sendMailSettings"
                    class="mt-20 mb-20"
                />
            </admin-page-table-content>
        </a-col>
    </a-row>
</template>
<script>
import { onMounted, ref } from "vue";
import { SaveOutlined, MailOutlined } from "@ant-design/icons-vue";
import SettingSidebar from "../SettingSidebar.vue";
import AdminPageHeader from "../../../../common/layouts/AdminPageHeader.vue";
import SendMailSetting from "@/main/views/common/settings/email/SendMailSetting.vue";
import Common from "../../../../common/composable/common";

export default {
    components: {
        SaveOutlined,
        MailOutlined,

        SettingSidebar,
        AdminPageHeader,
        SendMailSetting,
    },
    setup() {
        const sendMailSettings = ref([]);
        const { themeMode } = Common();

        onMounted(() => {
            axiosAdmin.get("settings/email").then((response) => {
                sendMailSettings.value = response.data.sendMailSettings;
            });
        });

        return {
            sendMailSettings,
            themeMode,
        };
    },
};
</script>
