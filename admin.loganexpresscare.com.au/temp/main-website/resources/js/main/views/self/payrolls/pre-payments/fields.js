import { ref } from "vue";
import { useI18n } from "vue-i18n";

const fields = () => {
    const addEditUrl = "self/pre-payments";
    const { t } = useI18n();
    const hashableColumns = ["user_id"];

    const initData = {
        user_id: undefined,
        amount: "",
        date_time: "",
        notes: "",
        payroll_month: "",
        payroll_year: "",
        deduct_from_payroll: 1,
    };

    const columns = ref([
        {
            title: t("pre_payment.date_time"),
            dataIndex: "date_time",
        },
        {
            title: t("pre_payment.amount"),
            dataIndex: "amount",
        },
        {
            title: t("pre_payment.deduct_month"),
            dataIndex: "deduct_month",
        },

        {
            title: t("common.action"),
            dataIndex: "action",
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
