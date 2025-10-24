import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "email-templates?fields=id,xid,name,name_key,credentials,other_data";
    const addEditUrl = "email-templates";
    const { t } = useI18n();

    const initData = {
        credentials: {},
    };

    const columns = [
        {
            title: t("email_template.name"),
            dataIndex: "name",
        },
        {
            title: t("email_template.type"),
            dataIndex: "type",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "name",
            value: t("email_template.name"),
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
