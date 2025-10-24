import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "payers?fields=id,xid,name,phone_number";
    const addEditUrl = "payers";
    const { t } = useI18n();

    const initData = {
        name: "",
        phone_number:"",
    };

    const columns = [
        {
            title: t("payer.name"),
            dataIndex: "name",
        },
        {
            title: t("payer.phone_number"),
            dataIndex: "phone_number",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("payer.name"),
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
