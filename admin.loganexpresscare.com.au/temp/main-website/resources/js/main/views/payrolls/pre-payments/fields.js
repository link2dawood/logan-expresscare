import { ref } from "vue";
import { useI18n } from "vue-i18n";
import common from "@/common/composable/common";

const fields = () => {
    const url =
        "pre-payments?fields=id,xid,user_id,x_user_id,user{id,xid,name},amount,notes,payroll_month,payroll_year,date_time,account_id,x_account_id,account{id,xid,name}";
    const addEditUrl = "pre-payments";
    const { t } = useI18n();
    const hashableColumns = ["user_id", "account_id"];
    const { dayjs } = common();

    const initData = {
        user_id: undefined,
        amount: "",
        date_time: dayjs().utc().format("YYYY-MM-DDTHH:mm:ssZ"),
        notes: "",
        payroll_month: "",
        payroll_year: "",
        deduct_from_payroll: 1,
        account_id: undefined,
    };

    const columns = ref([
        {
            title: t("pre_payment.user_id"),
            dataIndex: "user_id",
        },
        {
            title: t("pre_payment.date_time"),
            dataIndex: "date_time",
        },
        {
            title: t("pre_payment.deduct_account_number"),
            dataIndex: "account_id",
        },
        {
            title: t("pre_payment.amount"),
            dataIndex: "amount",
        },
        {
            title: t("pre_payment.deduct_month"),
            dataIndex: "deduct_month",
        },
    ]);

    const filterableColumns = [
        {
            key: "name",
            value: t("common.name"),
        },
    ];

    return {
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
    };
};

export default fields;
