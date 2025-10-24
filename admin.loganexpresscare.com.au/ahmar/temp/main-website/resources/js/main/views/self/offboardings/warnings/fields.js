import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "self/warnings?fields=id,xid,title,description,warning_date,user{id,xid,name},user_id,x_user_id";
    const addEditUrl = "self/warnings";
    const hashableColumns = ["user_id"];
    const { t } = useI18n();

    const initData = {
        title: "",
        description: "",
        user_id: undefined,
        warning_date: undefined
    };

    const columns = [
        {
            title: t("warning.title"),
            dataIndex: "title",
        },
        {
            title: t("warning.warning_date"),
            dataIndex: "warning_date",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "title",
            value: t("warning.title"),
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
