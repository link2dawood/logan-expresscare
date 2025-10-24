import { useI18n } from "vue-i18n";

const fields = () => {
    const url = "indicators?fields=id,xid,name,fields";
    const addEditUrl = "indicators";
    const { t } = useI18n();

    const initData = {
        name: "",
        fields: [],
    };

    const columns = [
        {
            title: t("indicator.name"),
            dataIndex: "name",
        },
        {
            title: t("indicator.fields"),
            dataIndex: "fields",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("indicator.name")
        },
    ];

    return {
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
    }
}

export default fields;
