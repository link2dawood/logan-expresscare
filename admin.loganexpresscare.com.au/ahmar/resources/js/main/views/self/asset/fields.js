import { useI18n } from "vue-i18n";

const fields = () => {
    const { t } = useI18n();
    const hashableColumns = [
        "location_id",
        "user_id",
        "asset_type_id",
        "asset_user_id",
    ];

    const initData = {
        name: "",
        year: "",
        month: "",
        date: undefined,
    };

    const columns = [
        {
            title: t("user.name"),
            dataIndex: "name",
        },
        {
            title: t("asset.lend_by"),
            dataIndex: "lend_by",
        },
        {
            title: t("asset.lend_date"),
            dataIndex: "lend_date",
        },

        {
            title: t("asset.return_date"),
            dataIndex: "return_date",
        },
        {
            title: t("asset.actual_return_date"),
            dataIndex: "actual_return_date",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("asset.name"),
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
