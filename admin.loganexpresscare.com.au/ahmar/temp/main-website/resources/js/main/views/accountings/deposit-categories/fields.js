import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "deposit-categories?fields=id,xid,name";
    const addEditUrl = "deposit-categories";
    const { t } = useI18n();

    const initData = {
        name: "",
    };

    const columns = [
        {
            title: t("deposit_category.name"),
            dataIndex: "name",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("deposit_category.name"),
        },
    ];

    return {
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
    };
};

export default fields;
