<template>
    <div :style="affixStyle">
        <a-dropdown
            :placement="
                appSetting.rtl
                    ? position == 'top'
                        ? 'bottomLeft'
                        : 'topLeft'
                    : position == 'top'
                    ? 'bottomRight'
                    : 'topRight'
            "
        >
            <a-button
                v-if="position == 'bottom'"
                type="primary"
                shape="circle"
                :style="{ width: '50px', height: '50px' }"
            >
                <template #icon><PlusOutlined /></template>
            </a-button>
            <a-button v-else type="link" shape="round" :style="{ padding: '0px' }">
                <template #icon><PlusCircleOutlined /></template>
            </a-button>
            <template #overlay>
                <a-menu>
                    <StaffAddButton
                        v-if="addMenus.includes('staff_member')"
                        customType="menu"
                    >
                        <template #icon>
                            <TeamOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_staff_member") }}</span>
                    </StaffAddButton>

                    <CurrencyAddButton
                        v-if="addMenus.includes('currency')"
                        customType="menu"
                    >
                        <template #icon>
                            <DollarOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_currency") }}</span>
                    </CurrencyAddButton>

                    <LanguageAddButton
                        v-if="addMenus.includes('language')"
                        customType="menu"
                    >
                        <template #icon>
                            <TranslationOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_language") }}</span>
                    </LanguageAddButton>

                    <DepartmentAddButton
                        v-if="addMenus.includes('department')"
                        customType="menu"
                    >
                        <template #icon>
                            <DeploymentUnitOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_departments") }}</span>
                    </DepartmentAddButton>

                    <DesignationAddButton
                        v-if="addMenus.includes('designation')"
                        customType="menu"
                    >
                        <template #icon>
                            <CloudServerOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_designations") }}</span>
                    </DesignationAddButton>

                    <ShiftsAddButton v-if="addMenus.includes('shift')" customType="menu">
                        <template #icon>
                            <ScheduleOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_shifts") }}</span>
                    </ShiftsAddButton>

                    <AssetAddButton v-if="addMenus.includes('asset')" customType="menu">
                        <template #icon>
                            <TranslationOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_assets") }}</span>
                    </AssetAddButton>

                    <AppreciationAddButton
                        v-if="addMenus.includes('appreciation')"
                        customType="menu"
                    >
                        <template #icon>
                            <PaperClipOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_appreciations") }}</span>
                    </AppreciationAddButton>

                    <AwardAddButton v-if="addMenus.includes('award')" customType="menu">
                        <template #icon>
                            <CrownOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_awards") }}</span>
                    </AwardAddButton>

                    <LeaveAddButton v-if="addMenus.includes('leave')" customType="menu">
                        <template #icon>
                            <CarOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_leaves") }}</span>
                    </LeaveAddButton>

                    <AttendanceAddButton
                        v-if="addMenus.includes('attendance')"
                        customType="menu"
                    >
                        <template #icon>
                            <FontColorsOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_attendance") }}</span>
                    </AttendanceAddButton>

                    <NewsAddButton v-if="addMenus.includes('news')" customType="menu">
                        <template #icon>
                            <NotificationOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_news") }}</span>
                    </NewsAddButton>

                    <PrePaymentAddButton
                        v-if="addMenus.includes('pre_payment')"
                        customType="menu"
                    >
                        <template #icon>
                            <TranslationOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_pre_payments") }}</span>
                    </PrePaymentAddButton>

                    <IncrementsPromotionsAddButton
                        v-if="addMenus.includes('increment_promotion')"
                        customType="menu"
                    >
                        <template #icon>
                            <PartitionOutlined />
                        </template>
                        <span>{{
                            $t("topbar_add_button.add_increment_promotions")
                        }}</span>
                    </IncrementsPromotionsAddButton>

                    <CompanyPolicyAddButton
                        v-if="addMenus.includes('company_policy')"
                        customType="menu"
                    >
                        <template #icon>
                            <ContainerOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_company_policies") }}</span>
                    </CompanyPolicyAddButton>

                    <WarningAddButton
                        v-if="addMenus.includes('warning')"
                        customType="menu"
                    >
                        <template #icon>
                            <WomanOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_warnings") }}</span>
                    </WarningAddButton>

                    <ResignationAddButton
                        v-if="addMenus.includes('resignation')"
                        customType="menu"
                    >
                        <template #icon>
                            <RedEnvelopeOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_resignations") }}</span>
                    </ResignationAddButton>

                    <TerminationAddButton
                        v-if="addMenus.includes('termination')"
                        customType="menu"
                    >
                        <template #icon>
                            <TransactionOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_terminations") }}</span>
                    </TerminationAddButton>

                    <ComplaintAddButton
                        v-if="addMenus.includes('complaint')"
                        customType="menu"
                    >
                        <template #icon>
                            <TranslationOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_complaints") }}</span>
                    </ComplaintAddButton>

                    <FormFeedbackAddButton
                        v-if="addMenus.includes('feedback')"
                        customType="menu"
                    >
                        <template #icon>
                            <FrownOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_feedbacks") }}</span>
                    </FormFeedbackAddButton>

                    <IndicatorAddButton
                        v-if="addMenus.includes('indicators')"
                        customType="menu"
                    >
                        <template #icon>
                            <TranslationOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_indicators") }}</span>
                    </IndicatorAddButton>

                    <AccountAddButton
                        v-if="addMenus.includes('account')"
                        customType="menu"
                    >
                        <template #icon>
                            <BankOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_accounts") }}</span>
                    </AccountAddButton>

                    <PayeeAddButton v-if="addMenus.includes('payee')" customType="menu">
                        <template #icon>
                            <PayCircleOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_payees") }}</span>
                    </PayeeAddButton>

                    <DepositAddButton
                        v-if="addMenus.includes('deposit')"
                        customType="menu"
                    >
                        <template #icon>
                            <RobotOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_deposits") }}</span>
                    </DepositAddButton>

                    <DepositCategoryAddButton
                        v-if="addMenus.includes('deposit_category')"
                        customType="menu"
                    >
                        <template #icon>
                            <ShareAltOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_deposit_categories") }}</span>
                    </DepositCategoryAddButton>

                    <ExpenseAddButton
                        v-if="addMenus.includes('expense')"
                        customType="menu"
                    >
                        <template #icon>
                            <ExceptionOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_expenses") }}</span>
                    </ExpenseAddButton>

                    <ExpenseCategoryAddButton
                        v-if="addMenus.includes('expense_category')"
                        customType="menu"
                    >
                        <template #icon>
                            <ExpandOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_expense_categories") }}</span>
                    </ExpenseCategoryAddButton>

                    <PayersAddButton
                        v-if="addMenus.includes('expense_category')"
                        customType="menu"
                    >
                        <template #icon> <RotateRightOutlined /></template>
                        <span>{{ $t("topbar_add_button.add_payers") }}</span>
                    </PayersAddButton>

                    <SurveyFormAddButton
                        v-if="addMenus.includes('form')"
                        customType="menu"
                    >
                        <template #icon> <FolderOpenOutlined /></template>
                        <span>{{ $t("topbar_add_button.add_forms") }}</span>
                    </SurveyFormAddButton>

                    <LetterHeadeTemplateAddButton
                        v-if="addMenus.includes('template')"
                        customType="menu"
                    >
                        <template #icon> <NodeCollapseOutlined /></template>
                        <span>{{ $t("topbar_add_button.add_templates") }}</span>
                    </LetterHeadeTemplateAddButton>

                    <RoleAddButton v-if="addMenus.includes('role')" customType="menu">
                        <template #icon>
                            <FundViewOutlined />
                        </template>
                        <span>{{ $t("topbar_add_button.add_role") }}</span>
                    </RoleAddButton>
                </a-menu>
            </template>
        </a-dropdown>
    </div>
</template>

<script>
import { computed } from "vue";
import {
    PlusOutlined,
    TeamOutlined,
    DollarOutlined,
    FundViewOutlined,
    TranslationOutlined,
    PlusCircleOutlined,
    UserOutlined,
    CloudServerOutlined,
    ScheduleOutlined,
    CrownOutlined,
    PaperClipOutlined,
    DeploymentUnitOutlined,
    CarOutlined,
    FontColorsOutlined,
    NotificationOutlined,
    PartitionOutlined,
    InteractionOutlined,
    PullRequestOutlined,
    WomanOutlined,
    RedEnvelopeOutlined,
    TransactionOutlined,
    FrownOutlined,
    BankOutlined,
    PayCircleOutlined,
    RobotOutlined,
    ShareAltOutlined,
    ExceptionOutlined,
    ExpandOutlined,
    RotateRightOutlined,
    FolderOpenOutlined,
    NodeCollapseOutlined,
    ContainerOutlined,
} from "@ant-design/icons-vue";
import common from "../composable/common";
import StaffAddButton from "../../main/views/staff-members/users/StaffAddButton.vue";
import CurrencyAddButton from "../../main/views/common/settings/currency/AddButton.vue";
import LanguageAddButton from "../../main/views/common/settings/translations/langs/AddButton.vue";
import RoleAddButton from "../../main/views/settings/roles/AddButton.vue";
import DepartmentAddButton from "../../main/views/staff-members/departments/AddButton.vue";
import DesignationAddButton from "../../main/views/staff-members/designations/AddButton.vue";
import ShiftsAddButton from "../../main/views/staff-members/shifts/AddButton.vue";
import AssetAddButton from "../../main/views/assets/asset/AddButton.vue";
import AppreciationAddButton from "../../main/views/appreciation/appreciations/AddButton.vue";
import AwardAddButton from "../../main/views/appreciation/awards/AddButton.vue";
import LeaveAddButton from "../../main/views/leave/leaves/AddButton.vue";
import AttendanceAddButton from "../../main/views/attendances/attendance/AddButton.vue";
import NewsAddButton from "../../main/views/news/AddButton.vue";
import PrePaymentAddButton from "../../main/views/payrolls/pre-payments/AddButton.vue";
import IncrementsPromotionsAddButton from "../../main/views/payrolls/increments-promotions/AddButton.vue";
import CompanyPolicyAddButton from "../../main/views/company-policies/AddButton.vue";
import WarningAddButton from "../../main/views/offboardings/warnings/AddButton.vue";
import ResignationAddButton from "../../main/views/offboardings/resignations/AddButton.vue";
import TerminationAddButton from "../../main/views/offboardings/terminations/AddButton.vue";
import ComplaintAddButton from "../../main/views/complaint/complaints/AddButton.vue";
import LetterHeadeTemplateAddButton from "../../main/views/letter-head/template/AddButton.vue";
import FormFeedbackAddButton from "../../main/views/forms/survey-form/AddButton.vue";
import IndicatorAddButton from "../../main/views/forms/indicators/AddButton.vue";
import AccountAddButton from "../../main/views/accountings/accounts/AddButton.vue";
import PayeeAddButton from "../../main/views/accountings/payees/AddButton.vue";
import DepositAddButton from "../../main/views/accountings/deposits/AddButton.vue";
import DepositCategoryAddButton from "../../main/views/accountings/deposit-categories/AddButton.vue";
import ExpenseAddButton from "../../main/views/accountings/expenses/AddButton.vue";
import ExpenseCategoryAddButton from "../../main/views/accountings/expense-categories/AddButton.vue";
import PayersAddButton from "../../main/views/accountings/payers/AddButton.vue";
import SurveyFormAddButton from "../../main/views/forms/template/AddButton.vue";

export default {
    props: {
        position: {
            default: "bottom",
        },
    },
    components: {
        PlusOutlined,
        TeamOutlined,
        DollarOutlined,
        FundViewOutlined,
        TranslationOutlined,
        PlusCircleOutlined,
        UserOutlined,
        CloudServerOutlined,
        ScheduleOutlined,
        CrownOutlined,
        PaperClipOutlined,
        DeploymentUnitOutlined,
        FontColorsOutlined,
        CarOutlined,
        NotificationOutlined,
        PartitionOutlined,
        InteractionOutlined,
        PullRequestOutlined,
        WomanOutlined,
        RedEnvelopeOutlined,
        TransactionOutlined,
        FrownOutlined,
        BankOutlined,
        PayCircleOutlined,
        RobotOutlined,
        ShareAltOutlined,
        ExceptionOutlined,
        ExpandOutlined,
        RotateRightOutlined,
        FolderOpenOutlined,
        NodeCollapseOutlined,
        ContainerOutlined,
        StaffAddButton,
        CurrencyAddButton,
        LanguageAddButton,
        RoleAddButton,
        DepartmentAddButton,
        DesignationAddButton,
        ShiftsAddButton,
        AssetAddButton,
        AppreciationAddButton,
        AwardAddButton,
        LeaveAddButton,
        AttendanceAddButton,
        NewsAddButton,
        PrePaymentAddButton,
        IncrementsPromotionsAddButton,
        CompanyPolicyAddButton,
        WarningAddButton,
        ResignationAddButton,
        TerminationAddButton,
        ComplaintAddButton,
        LetterHeadeTemplateAddButton,
        FormFeedbackAddButton,
        IndicatorAddButton,
        AccountAddButton,
        PayeeAddButton,
        DepositAddButton,
        DepositCategoryAddButton,
        ExpenseAddButton,
        ExpenseCategoryAddButton,
        PayersAddButton,
        SurveyFormAddButton,
    },
    setup(props, { emit }) {
        const { permsArray, appSetting, addMenus } = common();
        const menuSelected = () => {};
        const affixStyle = computed(() => {
            if (props.position == "bottom") {
                if (appSetting.value.rtl) {
                    return { position: "fixed", left: "40px", bottom: "30px" };
                } else {
                    return { position: "fixed", right: "40px", bottom: "30px" };
                }
            } else {
                return {};
            }
        });

        return {
            permsArray,
            appSetting,
            addMenus,
            menuSelected,
            affixStyle,

            innerWidth: window.innerWidth,
        };
    },
};
</script>
