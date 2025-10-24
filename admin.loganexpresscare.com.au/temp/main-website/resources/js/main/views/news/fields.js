import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "news?fields=id,xid,title,visible_to_all,description,status,newsUser{id,xid,user_id},newsUser:user{id,xid,name,profile_image,profile_image_url},newsUser:user:designation{id,xid,name},newsUser:user:location{id,xid,name}";
    const addEditUrl = "news";
    const { t } = useI18n();

    const initData = {
        title: "",
        description: "",
        status: "draft",
    };

    const columns = [
        {
            title: t("news.title"),
            dataIndex: "title",
        },
        {
            title: t("news.visible_to_all"),
            dataIndex: "visible",
        },
        {
            title: t("news.status"),
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
            value: t("news.title"),
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
