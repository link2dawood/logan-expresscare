import { useI18n } from "vue-i18n";
import common from "@/common/composable/common";

const fields = () => {
    const url =
        "terminations?fields=id,xid,title,description,start_date,end_date,user_id,x_user_id,user{id,xid,name,profile_image,profile_image_url},user:designation{id,xid,name},user:location{id,xid,name},letterhead_template_id,x_letterhead_template_id,letterHeadTemplate{id,xid,title,description},generates_id,x_generates_id,generate{id,xid,description,title,left_space,right_space,top_space,bottom_space}";
    const addEditUrl = "terminations";
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
        start_date: dayjs().utc().format("YYYY-MM-DDTHH:mm:ssZ"),
        end_date: "",
        user_id: undefined,
        letterhead_template_id: undefined,
        letterhead_title: "",
        letterhead_description: undefined,
    };

    const columns = [
        {
            title: t("termination.title"),
            dataIndex: "title",
        },
        {
            title: t("termination.user"),
            dataIndex: "user_id",
        },
        {
            title: t("termination.notice_date"),
            dataIndex: "start_date",
        },
        {
            title: t("termination.termination_date"),
            dataIndex: "end_date",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "title",
            value: t("termination.title"),
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
