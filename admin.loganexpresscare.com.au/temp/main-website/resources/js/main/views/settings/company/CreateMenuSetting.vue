<template>
    <a-button type="primary" @click="openDrawer">
        <template #icon> <SettingOutlined /> </template>
        {{ $t("company.shortcut_menu_setting") }}
    </a-button>

    <a-drawer
        :title="$t('company.shortcut_menu_setting')"
        :width="drawerWidth"
        :visible="visible"
        :body-style="{ paddingBottom: '80px' }"
        :footer-style="{ textAlign: 'right' }"
        :maskClosable="false"
        @close="onClose"
    >
        <a-form layout="vertical">
            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('company.shortcut_menu_Placement')"
                        name="shortcut_menus"
                        :help="rules.shortcut_menus ? rules.shortcut_menus.message : null"
                        :validateStatus="rules.shortcut_menus ? 'error' : null"
                        class="required"
                    >
                        <a-select
                            v-model:value="createMenuPosition"
                            :placeholder="
                                $t('common.select_default_text', [
                                    $t('company.shortcut_menu_Placement'),
                                ])
                            "
                            :allowClear="true"
                            optionFilterProp="label"
                            show-search
                        >
                            <a-select-option key="top_bottom" value="top_bottom">
                                {{ $t("company.top_and_bottom") }}
                            </a-select-option>
                            <a-select-option key="top" value="top">
                                {{ $t("company.top_header") }}
                            </a-select-option>
                            <a-select-option key="bottom" value="bottom">
                                {{ $t("company.bottom_corner") }}
                            </a-select-option>
                        </a-select>
                    </a-form-item>
                </a-col>
            </a-row>

            <a-row :gutter="16">
                <a-col :xs="24" :sm="24" :md="24" :lg="24">
                    <a-form-item
                        :label="$t('company.shortcut_menu_setting')"
                        name="shortcut_menus"
                        :help="rules.createMenus ? rules.shortcut_menus.message : null"
                        :validateStatus="rules.shortcut_menus ? 'error' : null"
                    >
                        <a-checkbox-group v-model:value="createMenus" style="width: 100%">
                            <a-row :gutter="[16, 10]">
                                <a-col :span="24">
                                    <a-checkbox value="staff_member">
                                        {{ $t("topbar_add_button.add_staff_member") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="currency">
                                        {{ $t("topbar_add_button.add_currency") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="language">
                                        {{ $t("topbar_add_button.add_language") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="role">
                                        {{ $t("topbar_add_button.add_role") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="department">
                                        {{ $t("topbar_add_button.add_departments") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="designation">
                                        {{ $t("topbar_add_button.add_designations") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="shift">
                                        {{ $t("topbar_add_button.add_shifts") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="asste">
                                        {{ $t("topbar_add_button.add_assets") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="holiday">
                                        {{ $t("topbar_add_button.add_holidays") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="appreciation">
                                        {{ $t("topbar_add_button.add_appreciations") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="award">
                                        {{ $t("topbar_add_button.add_awards") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="leave">
                                        {{ $t("topbar_add_button.add_leaves") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="attendance">
                                        {{ $t("topbar_add_button.add_attendance") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="news">
                                        {{ $t("topbar_add_button.add_news") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="pre_payment">
                                        {{ $t("topbar_add_button.add_pre_payments") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="increment_promotion">
                                        {{
                                            $t(
                                                "topbar_add_button.add_increment_promotions"
                                            )
                                        }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="company_policy">
                                        {{ $t("topbar_add_button.add_company_policies") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="job_question">
                                        {{ $t("topbar_add_button.add_job_questions") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="opening">
                                        {{ $t("topbar_add_button.add_openings") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="application">
                                        {{ $t("topbar_add_button.add_applications") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="warning">
                                        {{ $t("topbar_add_button.add_warnings") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="resignation">
                                        {{ $t("topbar_add_button.add_resignations") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="termination">
                                        {{ $t("topbar_add_button.add_terminations") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="compliant">
                                        {{ $t("topbar_add_button.add_complaints") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="form">
                                        {{ $t("topbar_add_button.add_forms") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="indicator">
                                        {{ $t("topbar_add_button.add_indicators") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="feedback">
                                        {{ $t("topbar_add_button.add_feedbacks") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="account">
                                        {{ $t("topbar_add_button.add_accounts") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="payee">
                                        {{ $t("topbar_add_button.add_payees") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="deposit">
                                        {{ $t("topbar_add_button.add_deposits") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="deposit_category">
                                        {{
                                            $t("topbar_add_button.add_deposit_categories")
                                        }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="expense">
                                        {{ $t("topbar_add_button.add_expenses") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="expense_category">
                                        {{
                                            $t("topbar_add_button.add_expense_categories")
                                        }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="payers">
                                        {{ $t("topbar_add_button.add_payers") }}
                                    </a-checkbox>
                                </a-col>
                                <a-col :span="24">
                                    <a-checkbox value="template">
                                        {{ $t("topbar_add_button.add_templates") }}
                                    </a-checkbox>
                                </a-col>
                            </a-row>
                        </a-checkbox-group>
                    </a-form-item>
                </a-col>
            </a-row>
        </a-form>
        <template #footer>
            <a-button
                type="primary"
                @click="onSubmit"
                style="margin-right: 8px"
                :loading="loading"
            >
                <template #icon> <SaveOutlined /> </template>
                {{ $t("common.update") }}
            </a-button>
            <a-button @click="onClose">
                {{ $t("common.cancel") }}
            </a-button>
        </template>
    </a-drawer>
</template>

<script>
import { defineComponent, ref, onMounted, computed, watch } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    SettingOutlined,
} from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import apiAdmin from "../../../../common/composable/apiAdmin";
import common from "../../../../common/composable/common";
import { useAuthStore } from "../../../../main/store/authStore";

export default defineComponent({
    emits: ["success"],
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
        SettingOutlined,
    },
    setup(props, { emit }) {
        const { appSetting, addMenus } = common();
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { t } = useI18n();
        const createMenuPosition = ref(appSetting.value.shortcut_menus);
        const createMenus = ref(addMenus.value);
        const visible = ref(false);
        const authStore = useAuthStore();

        const onSubmit = () => {
            const newFormData = {
                position: createMenuPosition.value,
                shortcut_menus_settings: createMenus.value,
            };

            addEditRequestAdmin({
                url: "companies/update-create-menu",
                data: newFormData,
                successMessage: t("company.menu_setting_updated"),
                success: (res) => {
                    emit("success", createMenuPosition.value);
                    authStore.updateAppAction();
                    visible.value = false;
                },
            });
        };

        const openDrawer = () => {
            rules.value = {};
            visible.value = true;
        };

        const onClose = () => {
            rules.value = {};
            visible.value = false;
        };

        watch(appSetting, (newVal, oldVal) => {
            createMenuPosition.value = newVal.shortcut_menus;
        });

        return {
            visible,
            loading,
            rules,
            onClose,
            openDrawer,
            onSubmit,
            createMenuPosition,
            createMenus,

            drawerWidth: window.innerWidth <= 991 ? "90%" : "35%",
        };
    },
});
</script>
