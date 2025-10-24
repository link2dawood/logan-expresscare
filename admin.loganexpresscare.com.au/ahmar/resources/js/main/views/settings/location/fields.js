import { useI18n } from "vue-i18n";

const fields = () => {
    const url = "locations?fields=id,xid,name,address";
    const addEditUrl = "locations";
    const { t } = useI18n();

    const initData = {
        name: "",
        address: "",
    };

    const columns = [
        {
            title: t("location.name"),
            dataIndex: "name",
        },
        {
            title: t("location.address"),
            dataIndex: "address",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("location.name"),
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
