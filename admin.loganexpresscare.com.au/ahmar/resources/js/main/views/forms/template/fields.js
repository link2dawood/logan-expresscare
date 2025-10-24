import { useI18n } from "vue-i18n";

const fields = () => {
    const url = "forms?fields=id,xid,name,status,form_fields";
    const addEditUrl = "forms";
    const hashableColumns = [];
    const { t } = useI18n();

    const initData = {
        name: "",
        form_fields: [],
        status: 1
    };

    const columns = [
        {
            title: t("form.name"),
            dataIndex: "name",
        },
        {
            title: t("form.form_fields"),
            dataIndex: "form_fields",
        },
        {
            title: t("form.status"),
            dataIndex: "status",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("form.name")
        },
    ];

    return {
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
    }
}

export default fields;
