import { useI18n } from "vue-i18n";

const fields = () => {
    const { t } = useI18n();
    const hashableColumns = ["user_id", "award_id"];

    const initData = {
        name: "",
        year: "",
        month: "",
        date: undefined,
    };

    const columns = [
        {
            title: t("appreciation.award"),
            dataIndex: "award_id",
        },
        {
            title: t("appreciation.date"),
            dataIndex: "date",
        },
        {
            title: t("appreciation.price_amount"),
            dataIndex: "price_amount",
        },
        {
            title: t("appreciation.price_given"),
            dataIndex: "price_given",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "description",
            value: t("common.description"),
        },
    ];

    return {
        initData,
        columns,
        filterableColumns,
        hashableColumns,
    };
};

export default fields;
