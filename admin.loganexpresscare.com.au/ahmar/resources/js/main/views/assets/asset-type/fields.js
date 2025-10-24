import { useI18n } from "vue-i18n";

const fields = () => {
    const url = "asset-types?fields=id,xid,name";
    const addEditUrl = "asset-types";
    const { t } = useI18n();

    const initData = {
        name: "",
    };

    const columns = [
        {
            title: t("asset_type.name"),
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
            value: t("asset_type.name"),
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
