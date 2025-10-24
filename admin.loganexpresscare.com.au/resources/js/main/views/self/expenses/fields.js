import { reactive, ref } from "vue";
import { useI18n } from "vue-i18n";

const fields = () => {
    const { t } = useI18n();
    const addEditUrl = "self/expenses";
    const url = ref(
        "self/expenses?fields=id,xid,status,bill,bill_url,expense_category_id,payee_id,x_payee_id,account_id,x_account_id,reference_number,x_expense_category_id,expenseCategory{id,xid,name},amount,user_id,x_user_id,user{id,xid,name},notes,date_time, payee{id,xid,name},account{id,xid,name}"
    );
    const hashableColumns = [
        "user_id",
        "expense_category_id",
        "payee_id",
        "account_id",
    ];

    const initData = {
        account_id: undefined,
        payee_id: undefined,
        expense_category_id: undefined,
        amount: "",
        bill: undefined,
        bill_url: undefined,
        date_time: undefined,
        reference_number: "",
        user_id: undefined,
        notes: "",
    };

    const columns = [
        {
            title: t("expense.expense_category"),
            dataIndex: "expense_category_id",
        },
        {
            title: t("expense.amount"),
            dataIndex: "amount",
        },
        {
            title: t("expense.date_time"),
            dataIndex: "date_time",
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

    return {
        url,
        addEditUrl,
        initData,
        columns,
        hashableColumns,
    };
};

export default fields;
