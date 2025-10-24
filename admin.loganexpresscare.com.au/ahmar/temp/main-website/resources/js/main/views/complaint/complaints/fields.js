import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "complaints?fields=id,xid,title,date_time,description,status,from_user_id,x_from_user_id,fromStaff{id,xid,name,profile_image,profile_image_url},fromStaff:designation{id,xid,name},fromStaff:location{id,xid,name},to_user_id,x_to_user_id,toStaff{id,xid,name,profile_image,profile_image_url},toStaff:designation{id,xid,name},toStaff:location{id,xid,name},proff_of_document,proff_of_document_url,reply_notes,letterhead_template_id,x_letterhead_template_id,letterHeadTemplate{id,xid,title,description},generates_id,x_generates_id,generate{id,xid,description,title,left_space,right_space,top_space,bottom_space}";
    const addEditUrl = "complaints";
    const hashableColumns = [
        "from_user_id",
        "to_user_id",
        "letterhead_template_id",
        "generates_id",
    ];
    const { t } = useI18n();
    const multiDimensalObjectColumns = {
        letterhead_title: "generate.title",
        letterhead_description: "generate.description",
    };

    const initData = {
        title: "",
        date_time: undefined,
        from_user_id: undefined,
        description: "",
        status: "approved",
        to_user_id: undefined,
        proff_of_document: undefined,
        proff_of_document_url: undefined,
        letterhead_template_id: undefined,
        letterhead_title: "",
        letterhead_description: undefined,
    };

    const columns = [
        {
            title: t("complaint.title"),
            dataIndex: "title",
        },
        {
            title: t("complaint.from_staff"),
            dataIndex: "from_user_id",
        },
        {
            title: t("complaint.to_user_id"),
            dataIndex: "to_user_id",
        },
        {
            title: t("complaint.date_time"),
            dataIndex: "date_time",
        },
        {
            title: t("complaint.proff_of_document"),
            dataIndex: "proff_of_document",
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
        multiDimensalObjectColumns,
    };
};

export default fields;
