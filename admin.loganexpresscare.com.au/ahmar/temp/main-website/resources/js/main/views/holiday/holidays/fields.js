import { useI18n } from "vue-i18n";

const fields = () => {
    const addEditUrl = "holidays";
    const { t } = useI18n();
    const hashableColumns = [];

    const initData = {
        name: "",
        year: "",
        month: "",
        date: undefined,
        is_half_day: 0,
        half_holiday: "before_break",
    };

    const columns = [
        {
            title: t("holiday.name"),
            dataIndex: "name",
        },
        {
            title: t("holiday.date"),
            dataIndex: "date",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

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
