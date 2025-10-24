import { useI18n } from "vue-i18n";
import common from "@/common/composable/common";

const fields = () => {
    const url =
        "resignations?fields=id,xid,title,description,start_date,end_date,status,user_id,x_user_id,user{id,xid,name,profile_image,profile_image_url},user:designation{id,xid,name},user:location{id,xid,name},reply_notes,letterhead_template_id,x_letterhead_template_id,letterHeadTemplate{id,xid,title,description},generates_id,x_generates_id,generate{id,xid,description,title,left_space,right_space,top_space,bottom_space}";
    const addEditUrl = "resignations";
    const hashableColumns = [
        "user_id",
        "letterhead_template_id",
        "generates_id",
    ];
    const { t } = useI18n();
    const { dayjs } = common();
    const multiDimensalObjectColumns = {
        letterhead_title: "generate.title",
        letterhead_description: "generate.description",
    };

    const initData = {
        title: "",
        description: "",
        status: "approved",
        start_date: dayjs().utc().format("YYYY-MM-DDTHH:mm:ssZ"),
        end_date: "",
        user_id: undefined,
        letterhead_template_id: undefined,
        letterhead_title: "",
        letterhead_description: undefined,
    };

    const columns = [
        {
            title: t("resignation.title"),
            dataIndex: "title",
        },
        {
            title: t("resignation.user"),
            dataIndex: "user_id",
        },
        {
            title: t("resignation.resignation_date"),
            dataIndex: "start_date",
        },
        {
            title: t("resignation.last_working_date"),
            dataIndex: "end_date",
        },
        {
            title: t("resignation.status"),
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
            value: t("resignation.title"),
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
