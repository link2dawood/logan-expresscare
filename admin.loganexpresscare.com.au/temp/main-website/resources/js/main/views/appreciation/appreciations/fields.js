import { ref } from "vue";
import { useI18n } from "vue-i18n";
import common from "@/common/composable/common";

const fields = () => {
    const url =
        "appreciations?fields=id,xid,user_id,profile_image,profile_image_url,account_id,x_account_id,letterhead_template_id,x_letterhead_template_id,x_user_id,user{id,xid,name,profile_image,profile_image_url},award_id,x_award_id,award{name},date,description,price_amount,price_given,user:designation{name},user:location{name},account{account_number},letterHeadTemplate{title,description},generates_id,x_generates_id,generate{description,title,left_space,right_space,top_space,bottom_space}";
    const addEditUrl = "appreciations";
    const { t } = useI18n();
    const hashableColumns = [
        "user_id",
        "award_id",
        "account_id",
        "letterhead_template_id",
    ];
    const multiDimensalObjectColumns = {
        letterhead_title: "generate.title",
        letterhead_description: "generate.description",
    };
    const { dayjs } = common();

    const initData = {
        user_id: undefined,
        award_id: undefined,
        date: dayjs().utc().format("YYYY-MM-DDTHH:mm:ssZ"),
        price_amount: "",
        price_given: "",
        description: "",
        profile_image: undefined,
        profile_image_url: undefined,
        account_id: undefined,
        letterhead_template_id: undefined,
        letterhead_title: "",
        letterhead_description: undefined,
    };

    const columns = ref([
        {
            title: t("appreciation.user"),
            dataIndex: "user_id",
        },
        {
            title: t("appreciation.date"),
            dataIndex: "date",
        },
        {
            title: t("appreciation.award"),
            dataIndex: "award_id",
        },
        {
            title: t("appreciation.price_amount"),
            dataIndex: "price_amount",
        },
        {
            title: t("appreciation.price_given"),
            dataIndex: "price_given",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        }
    ]);

    const filterableColumns = [
        {
            key: "description",
            value: t("common.description"),
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
