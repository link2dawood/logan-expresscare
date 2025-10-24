import { reactive, ref } from "vue";
import { useI18n } from "vue-i18n";
import common from "@/common/composable/common";

const fields = () => {
    const { t } = useI18n();
    const addEditUrl = "expenses";
    const url = ref(
        "expenses?fields=id,xid,payment_date,payroll_month,payroll_year,bill,bill_url,payment_status,expense_type,status,expense_category_id,x_expense_category_id,payee_id,x_payee_id,account_id,x_account_id,reference_number,expenseCategory{id,xid,name},amount,user_id,x_user_id,user{id,xid,name,profile_image,profile_image_url},user:designation{id,xid,name},user:location{id,xid,name},notes,date_time,payee{id,xid,name},account{id,xid,name,initial_balance}"
    );
    const hashableColumns = [
        "user_id",
        "expense_category_id",
        "payee_id",
        "account_id",
    ];

    const { dayjs } = common();

    const initData = {
        account_id: undefined,
        payee_id: undefined,
        expense_category_id: undefined,
        amount: "",
        bill: undefined,
        bill_url: undefined,
        date_time: dayjs().utc().format("YYYY-MM-DDTHH:mm:ssZ"),
        reference_number: "",
        user_id: undefined,
        notes: "",
        expense_type: undefined,
        status: "approved",
        payment_date: dayjs().utc().format("YYYY-MM-DDTHH:mm:ssZ"),
        payment_status: 1,
    };

    const commonColumns = [
        {
            title: t("expense.date_time"),
            dataIndex: "date_time",
        },
        {
            title: t("expense.expense_category"),
            dataIndex: "expense_category_id",
        },
        {
            title: t("expense.amount"),
            dataIndex: "amount",
        },
        {
            title: t("expense.status"),
            dataIndex: "status",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const employeeClaimsColumns = [
        {
            title: t("expense.created_by_user"),
            dataIndex: "user_id",
        },
        ...commonColumns,
    ];

    const companyClaimsColumns = [
        {
            title: t("expense.account"),
            dataIndex: "account_id",
        },
        {
            title: t("expense.payee"),
            dataIndex: "payee_id",
        },
        ...commonColumns,
    ];

    return {
        url,
        addEditUrl,
        initData,
        employeeClaimsColumns,
        companyClaimsColumns,
        hashableColumns,
    };
};

export default fields;
