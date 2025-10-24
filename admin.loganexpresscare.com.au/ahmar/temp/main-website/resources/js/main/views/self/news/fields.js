import { useI18n } from "vue-i18n";

const fields = () => {
    const addEditUrl = "holidays";
    const { t } = useI18n();
    const hashableColumns = [];

    const initData = {};

    const columns = [
        {
            title: t("news.title"),
            dataIndex: "title",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "title",
            value: t("news.title"),
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
