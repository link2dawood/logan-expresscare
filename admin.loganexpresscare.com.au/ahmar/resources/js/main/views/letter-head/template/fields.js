import { useI18n } from "vue-i18n";

const fields = () => {
    const url = "letter-head-templates?fields=id,xid,title,description";
    const addEditUrl = "letter-head-templates";
    const hashableColumns = [];
    const { t } = useI18n();

    const initData = {
        title: "",
        description: "",
    };

    const columns = [
        {
            title: t("template.title"),
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
            value: t("template.title"),
        },

    ];

    return {
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
    };
};

export default fields;
