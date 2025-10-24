import common from "@/common/composable/common";
import { ref } from "vue";
import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "increments-promotions?fields=id,xid,user_id,letterhead_template_id,x_letterhead_template_id,letterHeadTemplate{id,xid},x_user_id,user{id,xid,name},promoted_designation_id,x_promoted_designation_id,designation{id,xid,name},current_designation_id,x_current_designation_id,designation{id,xid,name},net_salary,type,description,date,generates_id,x_generates_id,generate{id,xid,description,title,left_space,right_space,top_space,bottom_space}";
    const addEditUrl = "increments-promotions";
    const { t } = useI18n();
    const hashableColumns = [
        "current_designation_id",
        "promoted_designation_id",
        "letterhead_template_id",
        "user_id",
    ];
    const { dayjs } = common();

    const multiDimensalObjectColumns = {
        letterhead_title: "generate.title",
        letterhead_description: "generate.description",
    };

    const initData = {
        user_id: undefined,
        current_designation_id: undefined,
        promoted_designation_id: undefined,
        letterhead_template_id: undefined,
        date: dayjs().utc().format("YYYY-MM-DDTHH:mm:ssZ"),
        type: "increment",
        description: "",
        net_salary: "",
        letterhead_description: "",
        letterhead_title: "",
    };

    const columns = ref([
        {
            title: t("increment_promotion.user_id"),
            dataIndex: "user_id",
        },
        {
            title: t("increment_promotion.date"),
            dataIndex: "date",
        },
        {
            title: t("increment_promotion.type"),
            dataIndex: "type",
        },
        {
            title: t("increment_promotion.current_designation_id"),
            dataIndex: "current_designation_id",
        },
        {
            title: t("increment_promotion.promoted_designation_id"),
            dataIndex: "promoted_designation_id",
        },
        {
            title: t("increment_promotion.net_salary"),
            dataIndex: "net_salary",
        },
    ]);

    const filterableColumns = [
        {
            key: "name",
            value: t("common.name"),
        },
    ];

    return {
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
        multiDimensalObjectColumns,
    };
};

export default fields;
