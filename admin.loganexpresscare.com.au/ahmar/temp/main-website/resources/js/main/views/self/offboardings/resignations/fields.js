import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "self/resignations?fields=id,xid,title,description,start_date,status,user_id,x_user_id,user{id,xid,name},reply_notes";
    const addEditUrl = "self/resignations";
    const { t } = useI18n();

    const initData = {
        title: "",
        description: "",
        start_date: "",
        status: "",
    };

    const columns = [
        {
            title: t("resignation.title"),
            dataIndex: "title",
        },
        {
            title: t("resignation.resignation_date"),
            dataIndex: "start_date",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "title",
            value: t("resignation.title"),
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
