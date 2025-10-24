<template>
    <div class="setting-sidebar">
        <perfect-scrollbar
            :options="{
                wheelSpeed: 1,
                swipeEasing: true,
                suppressScrollX: true,
            }"
        >
            <a-menu v-model:selectedKeys="selectedKeys">
                <a-menu-item
                    key="company"
                    v-if="
                        permsArray.includes('companies_edit') ||
                        permsArray.includes('admin')
                    "
                    @click="$router.push({ name: 'admin.settings.company.index' })"
                >
                    <template #icon>
                        <LaptopOutlined />
                    </template>
                    {{ $t("menu.company") }}
                </a-menu-item>
                <a-menu-item
                    key="profile"
                    @click="$router.push({ name: 'admin.settings.profile.index' })"
                >
                    <template #icon>
                        <UserOutlined />
                    </template>
                    {{ $t("menu.profile") }}
                </a-menu-item>
                <a-menu-item
                    key="translations"
                    v-if="
                        appType == 'non-saas' &&
                        (permsArray.includes('translations_view') ||
                            permsArray.includes('admin'))
                    "
                    @click="
                        $router.push({
                            name: 'admin.settings.translations.index',
                        })
                    "
                >
                    <template #icon>
                        <TranslationOutlined />
                    </template>
                    {{ $t("menu.translations") }}
                </a-menu-item>
                <a-menu-item
                    key="roles"
                    v-if="
                        permsArray.includes('roles_view') || permsArray.includes('admin')
                    "
                    @click="$router.push({ name: 'admin.settings.roles.index' })"
                >
                    <template #icon>
                        <SolutionOutlined />
                    </template>
                    {{ $t("menu.roles") }}
                </a-menu-item>
                <a-menu-item
                    key="employee_work_status"
                    v-if="
                        permsArray.includes('employee_work_status_view') ||
                        permsArray.includes('admin')
                    "
                    @click="
                        $router.push({
                            name: 'admin.settings.employee_work_status.index',
                        })
                    "
                >
                    <template #icon>
                        <SolutionOutlined />
                    </template>
                    {{ $t("menu.employee_work_status") }}
                </a-menu-item>
                <a-menu-item
                    key="currencies"
                    v-if="
                        permsArray.includes('currencies_view') ||
                        permsArray.includes('admin')
                    "
                    @click="
                        $router.push({
                            name: 'admin.settings.currencies.index',
                        })
                    "
                >
                    <template #icon>
                        <DollarOutlined />
                    </template>
                    {{ $t("menu.currencies") }}
                </a-menu-item>
                <a-menu-item
                    key="locations"
                    v-if="
                        permsArray.includes('locations_view') ||
                        permsArray.includes('admin')
                    "
                    @click="$router.push({ name: 'admin.settings.locations.index' })"
                >
                    <template #icon>
                        <EnvironmentOutlined />
                    </template>
                    {{ $t("menu.locations") }}
                </a-menu-item>
                <a-menu-item
                    key="pdf_fonts"
                    v-if="
                        permsArray.includes('pdf_fonts_view') ||
                        permsArray.includes('admin')
                    "
                    @click="$router.push({ name: 'admin.settings.pdf-fonts.index' })"
                >
                    <template #icon>
                        <FilePdfOutlined />
                    </template>
                    {{ $t("menu.pdf_fonts") }}
                </a-menu-item>
                <a-menu-item
                    key="hrm_settings"
                    v-if="
                        permsArray.includes('hrm_settings') ||
                        permsArray.includes('admin')
                    "
                    @click="$router.push({ name: 'admin.hrm_settings.index' })"
                >
                    <template #icon>
                        <ToolOutlined />
                    </template>
                    {{ $t("menu.hrm_settings") }}
                </a-menu-item>
                <a-sub-menu
                    key="payroll_settings"
                    v-if="
                        permsArray.includes('salary_components_view') ||
                        permsArray.includes('salary_groups_view') ||
                        permsArray.includes('admin')
                    "
                >
                    <template #title>
                        <span>
                            <ToolOutlined />
                            <span>{{ $t("menu.payroll_settings") }}</span>
                        </span>
                    </template>
                    <a-menu-item
                        v-if="
                            permsArray.includes('salary_components_view') ||
                            permsArray.includes('admin')
                        "
                        @click="
                            $router.push({
                                name: 'admin.settings.salary_components.index',
                            })
                        "
                    >
                        <template #icon>
                            <BankOutlined />
                        </template>
                        {{ $t("menu.salary_components") }}
                    </a-menu-item>
                    <a-menu-item
                        v-if="
                            permsArray.includes('salary_groups_view') ||
                            permsArray.includes('admin')
                        "
                        @click="
                            $router.push({
                                name: 'admin.settings.salary_groups.index',
                            })
                        "
                    >
                        <template #icon>
                            <AppstoreOutlined />
                        </template>
                        {{ $t("menu.salary_groups") }}
                    </a-menu-item>
                </a-sub-menu>
                <a-menu-item
                    key="employee_custom_fields"
                    v-if="
                        permsArray.includes('employee_custom_fields') ||
                        permsArray.includes('admin')
                    "
                    @click="
                        $router.push({
                            name: 'admin.settings.employee-custom-fields.index',
                        })
                    "
                >
                    <template #icon>
                        <FormOutlined />
                    </template>
                    {{ $t("menu.employee_custom_fields") }}
                </a-menu-item>
                <a-menu-item
                    key="email_settings"
                    v-if="
                        permsArray.includes('email_edit') || permsArray.includes('admin')
                    "
                    @click="$router.push({ name: 'admin.settings.email.index' })"
                >
                    <template #icon>
                        <MailOutlined />
                    </template>
                    {{ $t("menu.email_settings") }}
                </a-menu-item>
                <template v-if="appType == 'non-saas'">
                    <a-menu-item
                        key="storage_settings"
                        v-if="
                            permsArray.includes('storage_edit') ||
                            permsArray.includes('admin')
                        "
                        @click="
                            $router.push({
                                name: 'admin.settings.storage.index',
                            })
                        "
                    >
                        <template #icon>
                            <FolderOpenOutlined />
                        </template>
                        {{ $t("menu.storage_settings") }}
                    </a-menu-item>
                    <a-menu-item
                        key="database_backup"
                        v-if="
                            permsArray.includes('database_backup') ||
                            permsArray.includes('admin')
                        "
                        @click="
                            $router.push({
                                name: 'admin.settings.database_backup.index',
                            })
                        "
                    >
                        <template #icon>
                            <DatabaseOutlined />
                        </template>
                        {{ $t("menu.database_backup") }}
                    </a-menu-item>
                    <a-menu-item
                        key="update_app"
                        v-if="
                            permsArray.includes('update_app') ||
                            permsArray.includes('admin')
                        "
                        @click="
                            $router.push({
                                name: 'admin.settings.update_app.index',
                            })
                        "
                    >
                        <template #icon>
                            <HistoryOutlined />
                        </template>
                        {{ $t("menu.update_app") }}
                    </a-menu-item>
                </template>
            </a-menu>
        </perfect-scrollbar>
    </div>
</template>

<script>
import { defineComponent, ref, onMounted, watch } from "vue";
import {
    LaptopOutlined,
    UserOutlined,
    TranslationOutlined,
    ShopOutlined,
    SolutionOutlined,
    ScheduleOutlined,
    DollarOutlined,
    AccountBookOutlined,
    AppstoreAddOutlined,
    ApartmentOutlined,
    FolderOpenOutlined,
    MailOutlined,
    HistoryOutlined,
    FormOutlined,
    DatabaseOutlined,
    EnvironmentOutlined,
    ToolOutlined,
    BankOutlined,
    AppstoreOutlined,
    FilePdfOutlined,
} from "@ant-design/icons-vue";
import { useRoute } from "vue-router";
import common from "../../../common/composable/common";

export default defineComponent({
    components: {
        LaptopOutlined,
        UserOutlined,
        TranslationOutlined,
        ShopOutlined,
        SolutionOutlined,
        ScheduleOutlined,
        DollarOutlined,
        AccountBookOutlined,
        AppstoreAddOutlined,
        ApartmentOutlined,
        FolderOpenOutlined,
        MailOutlined,
        HistoryOutlined,
        FormOutlined,
        DatabaseOutlined,
        EnvironmentOutlined,
        ToolOutlined,
        BankOutlined,
        AppstoreOutlined,
        FilePdfOutlined,
    },
    setup() {
        const { appSetting, user, permsArray, appModules, appType } = common();
        const route = useRoute();
        const selectedKeys = ref([]);

        onMounted(() => {
            const menuKey =
                typeof route.meta.menuKey == "function"
                    ? route.meta.menuKey(route)
                    : route.meta.menuKey;

            selectedKeys.value = [menuKey.replace("-", "_")];
        });

        watch(route, (newVal, oldVal) => {
            const menuKey =
                typeof newVal.meta.menuKey == "function"
                    ? newVal.meta.menuKey(newVal)
                    : newVal.meta.menuKey;

            selectedKeys.value = [menuKey.replace("-", "_")];
        });

        return {
            permsArray,

            selectedKeys,
            appType,
        };
    },
});
</script>

<style lang="less">
.setting-sidebar .ps {
    height: calc(100vh - 145px);
}
</style>
