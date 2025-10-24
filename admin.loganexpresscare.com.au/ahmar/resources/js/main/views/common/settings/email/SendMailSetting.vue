<template>
    <div class="notificaiton-setting-bar">
        <perfect-scrollbar
            :options="{
                wheelSpeed: 1,
                swipeEasing: true,
                suppressScrollX: true,
            }"
        >
            <a-card class="page-content-container" :bodyStyle="{ paddingTop: '20px' }">
                <template #title>
                    <div :style="{ marginTop: '8px', paddingBottom: '14px' }">
                        {{ $t("mail_settings.send_mail_for") }}
                    </div>
                </template>
                <a-tabs v-model:activeKey="activeTabKey">
                    <a-tab-pane key="company" :tab="$t('mail_settings.company')" />
                    <a-tab-pane key="employee" :tab="$t('mail_settings.employee')" />
                </a-tabs>
                <a-checkbox-group
                    v-model:value="settings"
                    style="width: 100%"
                    @change="settingsChanged"
                >
                    <div v-show="activeTabKey == 'company'">
                        <a-row>
                            <!-- Staff Member -->
                            <a-col :span="24" class="mb-5">
                                <a-typography-title :level="5">
                                    {{ $t("menu.leaves") }}
                                    <TooltipCompany />
                                </a-typography-title>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="company_employee_leave_apply">
                                    {{ $t("mail_settings.on_employee_leave_apply") }}
                                </a-checkbox>
                            </a-col>
                            <a-divider />
                            <a-col :span="24" class="mb-5">
                                <a-typography-title :level="5">
                                    {{ $t("menu.attendances") }}
                                    <TooltipCompany />
                                </a-typography-title>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="company_employee_clock_in">
                                    {{ $t("mail_settings.on_employee_clock_in") }}
                                </a-checkbox>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="company_employee_clock_out">
                                    {{ $t("mail_settings.on_employee_clock_out") }}
                                </a-checkbox>
                            </a-col>
                            <a-divider />
                            <a-col :span="24" class="mb-5">
                                <a-typography-title :level="5">
                                    {{ $t("menu.resignations") }}
                                    <TooltipCompany />
                                </a-typography-title>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="company_employee_resignation_apply">
                                    {{
                                        $t("mail_settings.on_employee_resignation_apply")
                                    }}
                                </a-checkbox>
                            </a-col>
                            <a-divider />
                            <a-col :span="24" class="mb-5">
                                <a-typography-title :level="5">
                                    {{ $t("menu.complaints") }}
                                    <TooltipCompany />
                                </a-typography-title>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="company_employee_complaint_apply">
                                    {{ $t("mail_settings.on_employee_complaint_apply") }}
                                </a-checkbox>
                            </a-col>
                            <a-divider />
                            <a-col :span="24" class="mb-5">
                                <a-typography-title :level="5">
                                    {{ $t("menu.expenses") }}
                                    <TooltipCompany />
                                </a-typography-title>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="company_employee_expense_apply">
                                    {{ $t("mail_settings.on_employee_expense_apply") }}
                                </a-checkbox>
                            </a-col>
                            <a-divider />
                            <a-col :span="24" class="mb-5">
                                <a-typography-title :level="5">
                                    {{ $t("menu.assigned_survey") }}
                                    <TooltipCompany />
                                </a-typography-title>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="company_employee_survey_submit">
                                    {{ $t("mail_settings.on_employee_survey_submit") }}
                                </a-checkbox>
                            </a-col>
                            <a-divider />
                        </a-row>
                    </div>
                    <div v-show="activeTabKey == 'employee'">
                        <a-row>
                            <!-- Staff Member -->
                            <a-col :span="24" class="mb-5">
                                <a-typography-title :level="5">
                                    {{ $t("menu.staff_members") }}
                                    <TooltipEmployee />
                                </a-typography-title>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="employee_welcome_mail">
                                    {{ $t("mail_settings.on_welcome_mail") }}
                                </a-checkbox>
                            </a-col>
                            <a-divider />
                            <a-col :span="24" class="mb-5">
                                <a-typography-title :level="5">
                                    {{ $t("menu.assets") }}
                                    <TooltipEmployee />
                                </a-typography-title>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="employee_asset_lent">
                                    {{ $t("mail_settings.on_asset_lent") }}
                                </a-checkbox>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="employee_asset_return">
                                    {{ $t("mail_settings.on_asset_return") }}
                                </a-checkbox>
                            </a-col>
                            <a-divider />
                            <a-col :span="24" class="mb-5">
                                <a-typography-title :level="5">
                                    {{ $t("menu.appreciations") }}
                                    <TooltipEmployee />
                                </a-typography-title>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="employee_on_appreciation">
                                    {{ $t("mail_settings.on_appreciation") }}
                                </a-checkbox>
                            </a-col>
                            <a-divider />
                            <a-col :span="24" class="mb-5">
                                <a-typography-title :level="5">
                                    {{ $t("menu.leaves") }}
                                    <TooltipEmployee />
                                </a-typography-title>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="employee_leave_approve">
                                    {{ $t("mail_settings.on_leave_approve") }}
                                </a-checkbox>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="employee_leave_reject">
                                    {{ $t("mail_settings.on_leave_reject") }}
                                </a-checkbox>
                            </a-col>
                            <a-divider />
                            <a-col :span="24" class="mb-5">
                                <a-typography-title :level="5">
                                    {{ $t("menu.news") }}
                                    <TooltipEmployee />
                                </a-typography-title>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="employee_news_published">
                                    {{ $t("mail_settings.on_news_published") }}
                                </a-checkbox>
                            </a-col>
                            <a-divider />
                            <a-col :span="24" class="mb-5">
                                <a-typography-title :level="5">
                                    {{ $t("menu.payrolls") }}
                                    <TooltipEmployee />
                                </a-typography-title>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="employee_payroll_paid">
                                    {{ $t("mail_settings.on_payroll_paid") }}
                                </a-checkbox>
                            </a-col>
                            <a-divider />
                            <a-col :span="24" class="mb-5">
                                <a-typography-title :level="5">
                                    {{ $t("menu.company_policies") }}
                                    <TooltipEmployee />
                                </a-typography-title>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="employee_company_policies_create">
                                    {{ $t("mail_settings.on_company_policies_create") }}
                                </a-checkbox>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="employee_company_policies_update">
                                    {{ $t("mail_settings.on_company_policies_update") }}
                                </a-checkbox>
                            </a-col>
                            <a-divider />
                            <a-col :span="24" class="mb-5">
                                <a-typography-title :level="5">
                                    {{ $t("menu.offboardings") }}
                                    <TooltipEmployee />
                                </a-typography-title>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="employee_warning">
                                    {{ $t("mail_settings.on_warning") }}
                                </a-checkbox>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="employee_terminations">
                                    {{ $t("mail_settings.on_terminations") }}
                                </a-checkbox>
                            </a-col>
                            <a-divider />
                            <a-col :span="24" class="mb-5">
                                <a-typography-title :level="5">
                                    {{ $t("menu.resignations") }}
                                    <TooltipEmployee />
                                </a-typography-title>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="employee_resignation_approve">
                                    {{ $t("mail_settings.on_resignation_approve") }}
                                </a-checkbox>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="employee_resignation_reject">
                                    {{ $t("mail_settings.on_resignation_reject") }}
                                </a-checkbox>
                            </a-col>
                            <a-divider />
                            <a-col :span="24" class="mb-5">
                                <a-typography-title :level="5">
                                    {{ $t("menu.complaints") }}
                                    <TooltipEmployee />
                                </a-typography-title>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="employee_complaint_approve">
                                    {{ $t("mail_settings.on_complaint_approve") }}
                                </a-checkbox>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="employee_complaint_reject">
                                    {{ $t("mail_settings.on_complaint_reject") }}
                                </a-checkbox>
                            </a-col>
                            <a-divider />
                            <a-col :span="24" class="mb-5">
                                <a-typography-title :level="5">
                                    {{ $t("menu.expenses") }}
                                    <TooltipEmployee />
                                </a-typography-title>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="employee_expense_approve">
                                    {{ $t("mail_settings.on_expense_approve") }}
                                </a-checkbox>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="employee_expense_reject">
                                    {{ $t("mail_settings.on_expense_reject") }}
                                </a-checkbox>
                            </a-col>
                            <a-divider />
                            <a-col :span="24" class="mb-5">
                                <a-typography-title :level="5">
                                    {{ $t("menu.survey_forms") }}
                                    <TooltipEmployee />
                                </a-typography-title>
                            </a-col>
                            <a-col :span="24" class="mb-5">
                                <a-checkbox value="employee_survey_forms_assign">
                                    {{ $t("mail_settings.on_survey_forms_assign") }}
                                </a-checkbox>
                            </a-col>
                            <a-divider />
                        </a-row>
                    </div>
                </a-checkbox-group>
            </a-card>
        </perfect-scrollbar>
    </div>
</template>
<script>
import { onMounted, ref } from "vue";
import { message } from "ant-design-vue";
import { useI18n } from "vue-i18n";
import apiAdmin from "../../../../../common/composable/apiAdmin";
import { getUrlByAppType } from "../../../../../common/scripts/functions";
import TooltipCompany from "./TooltipCompany.vue";
import TooltipEmployee from "./TooltipEmployee.vue";

export default {
    props: ["sendMailSettings"],
    components: {
        TooltipCompany,
        TooltipEmployee,
    },
    setup(props) {
        const { addEditRequestAdmin } = apiAdmin();
        const { t } = useI18n();
        const settings = ref([]);
        const activeTabKey = ref("company");

        onMounted(() => {
            settings.value = props.sendMailSettings.credentials;
        });

        const settingsChanged = (checkedValue) => {
            addEditRequestAdmin({
                url: `settings/email/send-mail-settings`,
                data: { name_key: "company", settings: checkedValue },
                success: (res) => {
                    message.success(t("mail_settings.send_mail_setting_saved"));
                },
            });
        };

        return {
            settings,
            settingsChanged,

            activeTabKey,
        };
    },
};
</script>

<style lang="less">
.notificaiton-setting-bar .ps {
    height: calc(100vh - 145px);
}
</style>
