import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "payees?fields=id,xid,name,phone_number";
    const addEditUrl = "payees";
    const { t } = useI18n();

    const initData = {
        name: "",
        phone_number:"",
    };

    const columns = [
        {
            title: t("payee.name"),
            dataIndex: "name",
        },
        {
            title: t("payee.phone_number"),
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
            value: t("payee.name"),
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
