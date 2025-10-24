import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "self/complaints?fields=id,xid,title,date_time,description,status,to_user_id,x_to_user_id,toStaff{id,xid,name},proff_of_document,proff_of_document_url,reply_notes";
    const addEditUrl = "self/complaints";
    const hashableColumns = ["to_user_id"];
    const { t } = useI18n();

    const initData = {
        title: "",
        date_time: undefined,
        description: "",
        to_user_id: undefined,
        proff_of_document: undefined,
        proff_of_document_url: undefined
    };

    const columns = [
        {
            title: t("complaint.title"),
            dataIndex: "title",
        },
        {
            title: t("complaint.to_user_id"),
            dataIndex: "to_user_id",
        },
        {
            title: t("complaint.proff_of_document"),
            dataIndex: "proff_of_document",
        },
        {
            title: t("complaint.date_time"),
            dataIndex: "date_time",
        },
        {
            title: t("complaint.status"),
            dataIndex: "status",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "title",
            value: t("complaint.title"),
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
