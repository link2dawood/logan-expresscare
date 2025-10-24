import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "feedbacks?fields=id,xid,title,replied,not_replied,last_date,visible_to,form_id,x_form_id,description,formType{id,xid,name},feedbackUser{id,xid,user_id,rating},feedbackUser:user{id,xid,name,profile_image,profile_image_url},feedback_form_fields,indicator_fields";
    const addEditUrl = "feedbacks";
    const hashableColumns = ["form_id"];
    const { t } = useI18n();

    const initData = {
        title: "",
        form_id: undefined,
        last_date: undefined,
        description: "",
    };

    const columns = [
        {
            title: t("feedback.title"),
            dataIndex: "title",
        },
        {
            title: t("feedback.visible_to"),
            dataIndex: "visible_to",
        },
        {
            title: t("feedback.last_date"),
            dataIndex: "last_date",
        },
        {
            title: t("feedback.replied"),
            dataIndex: "replied",
        },
        {
            title: t("feedback.not_replied"),
            dataIndex: "not_replied",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const responseColumns = [
        {
            title: t("feedback.user_id"),
            dataIndex: "user_id",
        },
        {
            title: t("feedback.submit_date"),
            dataIndex: "submit_date",
        },
        {
            title: t("feedback.rating"),
            dataIndex: "rating",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "title",
            value: t("feedback.title"),
        },
    ];

    return {
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
        responseColumns,
    };
};

export default fields;
